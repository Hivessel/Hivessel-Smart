<template>
  <div class="noise-bees" :class="{ live: live }">
    <canvas ref="canvas" class="world"></canvas>

    <div class="overlay" :class="{ visible: tooNoisy }">
      <h1>Too Noisy</h1>
    </div>

    <div class="tip" v-if="!live"><span class="dot"></span>
      Click “Start Microphone” — talk/clap and drag a bee 🐝
    </div>

    <!-- HUD (right) -->
    <aside class="hud">
      <div class="hud-row title"><span class="dot"></span> MICROPHONE</div>
      <div class="hud-row"><button class="btn" @click="start" :disabled="live">Start Microphone</button></div>
      <div class="hud-row"><button class="btn" @click="stop" :disabled="!live">Stop</button></div>

      <h4>Sensitivity</h4>
      <div class="hud-row">
        <span class="label">Trigger</span>
        <input type="range" min="5" max="60" v-model.number="sens" @input="updateThresholdLines" />
      </div>

      <h4>Total</h4>
      <div class="hud-row">
        <span class="label">Max Bees</span>
        <input type="range" min="120" max="900" v-model.number="maxBees" />
      </div>

      <h4>Theme</h4>
      <div class="hud-row">
        <span class="label">Bee Size</span>
        <input type="range" min="24" max="96" v-model.number="beeSize" />
      </div>
    </aside>

    <!-- Level meter -->
    <div class="meter">
      <div class="meter-fill" :style="{ width: meterPct + '%', background: meterGradient }"></div>
      <div class="meter-line warn" :style="{ left: warnLeft }"></div>
      <div class="meter-line noisy" :style="{ left: noisyLeft }"></div>
    </div>

    <div class="error" v-show="error">{{ error }}</div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, toRefs, onMounted, onBeforeUnmount } from 'vue';

/* ---- Props: supply multiple images ---- */
const props = defineProps({
  sources: { type: Array, default: () => [
    'https://images.vexels.com/media/users/3/235199/isolated/preview/2c46a957f757358d387baf0671701af5-flying-color-stroke-honey-bee.png',
    'https://pngfre.com/wp-content/uploads/bee-46-300x300.png',
    'https://uxwing.com/wp-content/themes/uxwing/download/animals-and-birds/bee-icon.png',
    'https://png.pngtree.com/png-vector/20250729/ourmid/pngtree-realistic-sports-ball-with-textured-surface-and-authentic-groove-shadows-png-image_16918015.webp'
  ] }
});

/* ---- Canvas ---- */
const canvas = ref(null);
let ctx;

/* ---- Reactive state ---- */
const state = reactive({
  live: false,
  tooNoisy: false,
  sens: 40,
  maxBees: 650,
  beeSize: 64,
  error: '',
  meterPct: 0,
  warnLeft: '40%',
  noisyLeft: '65%'
});
const { live, tooNoisy, sens, maxBees, beeSize, error, meterPct, warnLeft, noisyLeft } = toRefs(state);

const meterGradient = computed(() => {
  const pct = meterPct.value / 100;
  const color = pct < 0.08/0.35 ? '#22c55e' : pct < 0.18/0.35 ? '#fbbf24' : '#ef4444';
  return `linear-gradient(90deg, ${color}, #a78bfa)`;
});

/* ---- Audio ---- */
let audioCtx = null, analyser = null, micStream = null, data = null, rafId = null;

/* ---- Physics world ---- */
let lastFrame = performance.now();
const bees = [];
const gravityBase = 980;
const restitution = 0.85;
const airBase = 0.012;

/* ---- Images (multi-sprite) ---- */
const beeSprites = [];
let loadedCount = 0;
function loadSprites() {
  beeSprites.length = 0;
  loadedCount = 0;
  props.sources.forEach(src => {
    const img = new Image();
    img.src = src;
    img.onload = () => { loadedCount++; };
    beeSprites.push(img);
  });
}
const beesReady = () => loadedCount === beeSprites.length && beeSprites.length > 0;

/* ---- Utils ---- */
const clamp = (n, lo, hi) => Math.min(hi, Math.max(lo, n));
const lerp  = (a,b,t) => a + (b-a)*t;
const sliderToThreshold = v => 0.26 - (v * 0.0037); // 5..60 → ~0.22..0.04 RMS

function updateThresholdLines() {
  const t = sliderToThreshold(sens.value);
  const warnPct = clamp((t / 0.35) * 100, 0, 100);
  const noisyPct = clamp(((t * 0.75) / 0.35) * 100, 0, 100);
  warnLeft.value = warnPct + '%';
  noisyLeft.value = noisyPct + '%';
}

/* ---- Drag state ---- */
const isDragging = ref(false);
let draggedBee = null;
let lastPointer = { x: 0, y: 0, t: 0 };

/* Pointer helpers */
function getPointerPos(e) {
  const rect = canvas.value.getBoundingClientRect();
  if (e.touches && e.touches[0]) e = e.touches[0];
  return { x: e.clientX - rect.left, y: e.clientY - rect.top, t: performance.now() };
}

/* Start drag: pick topmost bee under pointer */
function onPointerDown(e) {
  const p = getPointerPos(e);
  lastPointer = p;

  // Iterate from top to bottom (last drawn is topmost)
  for (let i = bees.length - 1; i >= 0; i--) {
    const b = bees[i];
    const dx = p.x - b.x, dy = p.y - b.y;
    if (Math.hypot(dx, dy) <= b.r) {
      isDragging.value = true;
      draggedBee = b;
      // zero velocities so it follows smoothly
      b.vx = 0; b.vy = 0;
      // bring to front (optional)
      bees.splice(i, 1);
      bees.push(b);
      // prevent scrolling on mobile
      if (e.cancelable) e.preventDefault();
      break;
    }
  }
}

/* Move drag: follow pointer & compute fling velocity */
function onPointerMove(e) {
  if (!isDragging.value || !draggedBee) return;
  const p = getPointerPos(e);
  const dt = Math.max(0.016, (p.t - lastPointer.t) / 1000); // clamp small dt

  // velocity from pointer movement
  draggedBee.vx = (p.x - lastPointer.x) / dt;
  draggedBee.vy = (p.y - lastPointer.y) / dt;

  // position follows pointer
  draggedBee.x = p.x;
  draggedBee.y = p.y;

  lastPointer = p;
  // prevent scroll on touch
  if (e.cancelable) e.preventDefault();
}

/* End drag: release back to physics with current vx/vy */
function onPointerUp() {
  isDragging.value = false;
  draggedBee = null;
}

/* ---- Spatial collisions (drag-aware) ---- */
const cellSize = 120;
function buildSpatial(items){
  const map = new Map();
  for (let i=0;i<items.length;i++){
    const b = items[i];
    const minx = Math.floor((b.x - b.r) / cellSize);
    const miny = Math.floor((b.y - b.r) / cellSize);
    const maxx = Math.floor((b.x + b.r) / cellSize);
    const maxy = Math.floor((b.y + b.r) / cellSize);
    for (let gx=minx; gx<=maxx; gx++)
      for (let gy=miny; gy<=maxy; gy++){
        const key = `${gx},${gy}`;
        if (!map.has(key)) map.set(key, []);
        map.get(key).push(i);
      }
  }
  return map;
}

/* If one of the pair is dragged, only move the OTHER bee (so dragged feels “solid”). */
function collideAndResolve(items){
  const map = buildSpatial(items);
  const seen = new Set();
  for (const idxs of map.values()){
    for (let a=0;a<idxs.length;a++){
      for (let b=a+1;b<idxs.length;b++){
        const i=idxs[a], j=idxs[b];
        const key = i<j ? `${i}-${j}` : `${j}-${i}`;
        if (seen.has(key)) continue; seen.add(key);

        const A=items[i], B=items[j];
        const dx=B.x-A.x, dy=B.y-A.y, minD=A.r+B.r;
        const d2 = dx*dx+dy*dy;
        if (d2>0 && d2<minD*minD){
          const d=Math.sqrt(d2)||1, nx=dx/d, ny=dy/d, overlap=(minD - d);

          const aDragged = (A === draggedBee);
          const bDragged = (B === draggedBee);

          if (aDragged && !bDragged){
            // push only B away
            B.x += nx*overlap; B.y += ny*overlap;
          } else if (bDragged && !aDragged){
            A.x -= nx*overlap; A.y -= ny*overlap;
          } else {
            // normal split
            A.x -= nx*overlap*0.5; A.y -= ny*overlap*0.5;
            B.x += nx*overlap*0.5; B.y += ny*overlap*0.5;
          }

          // velocity bounce (skip imparting bounce *to* dragged bee; others still react)
          const rvx=B.vx-A.vx, rvy=B.vy-A.vy, rel=rvx*nx+rvy*ny;
          if (rel<0){
            const J=-(1+restitution)*rel*0.5;
            if (!aDragged){ A.vx -= J*nx; A.vy -= J*ny; }
            if (!bDragged){ B.vx += J*nx; B.vy += J*ny; }
          }
        }
      }
    }
  }
}

/* ---- Spawn / Step / Draw ---- */
function spawnBees(count, intensity){
  const W = canvas.value.clientWidth, H = canvas.value.clientHeight;
  for (let i=0;i<count;i++){
    const maxR = beeSize.value;
    const r = lerp(18, maxR, Math.pow(intensity, 0.65) * (0.5 + Math.random()*0.5));
    const x = Math.random()*(W-2*r)+r;
    const y = H - r - Math.random()*60;
    const speed = 120 + intensity*520;
    const angle = (-Math.PI/2) + (Math.random()*0.9 - 0.45);
    bees.push({
      x, y, r,
      vx: Math.cos(angle)*speed,
      vy: Math.sin(angle)*speed,
      rot: Math.random()*Math.PI*2,
      vr: (Math.random()*2-1) * (0.4 + intensity*0.8),
      img: beeSprites[(Math.random()*beeSprites.length)|0]
    });
  }
}

function step(dt, intensity){
  const W = canvas.value.clientWidth, H = canvas.value.clientHeight;
  const g = gravityBase * (0.5 + intensity);
  const air = airBase * (0.6 + intensity*1.4);

  for (const b of bees){
    if (b === draggedBee){
      // while dragging: keep it in-bounds and let rotation react to motion
      b.x = clamp(b.x, b.r, W - b.r);
      b.y = clamp(b.y, b.r, H - b.r);
      b.rot += b.vr * dt;
      continue;
    }

    b.vy += g*dt;
    b.vx *= (1-air); b.vy *= (1-air*0.7);
    b.x += b.vx*dt;  b.y += b.vy*dt;
    b.rot += b.vr*dt;

    if (b.x - b.r < 0){ b.x=b.r; b.vx = -b.vx*restitution; }
    if (b.x + b.r > W){ b.x=W-b.r; b.vx = -b.vx*restitution; }
    if (b.y - b.r < 0){ b.y=b.r; b.vy = -b.vy*restitution; }
    if (b.y + b.r > H){ b.y=H-b.r; b.vy = -b.vy*restitution; }
  }
  collideAndResolve(bees);
}

function draw(){
  const W = canvas.value.clientWidth, H = canvas.value.clientHeight;
  ctx.clearRect(0,0,W,H);
  if (!beesReady()) return;

  for (const b of bees){
    // shadow
    ctx.globalAlpha = 0.25;
    ctx.beginPath();
    ctx.ellipse(b.x, b.y + b.r*0.85, b.r*0.85, b.r*0.35, 0, 0, Math.PI*2);
    ctx.fillStyle = '#000'; ctx.fill();
    ctx.globalAlpha = 1;

    // sprite
    const s = b.r*2;
    ctx.save();
    ctx.translate(b.x, b.y);
    ctx.rotate(b.rot);
    const squash = clamp(1 - b.vy/900, 0.78, 1.08);
    ctx.scale(1, squash);
    ctx.drawImage(b.img, -s/2, -s/2, s, s);

    // optional highlight for dragged bee
    if (b === draggedBee){
      ctx.lineWidth = 2;
      ctx.strokeStyle = 'rgba(255,255,255,.8)';
      ctx.beginPath(); ctx.arc(0, 0, s*0.52, 0, Math.PI*2); ctx.stroke();
    }
    ctx.restore();
  }
}

/* ---- Audio helpers ---- */
function rms(){
  analyser.getByteTimeDomainData(data);
  let sum=0;
  for (let i=0;i<data.length;i++){ const v=(data[i]-128)/128; sum += v*v; }
  return Math.sqrt(sum/data.length);
}
const colorForLevel = (pct0to1) =>
  pct0to1 < 0.08/0.35 ? '#22c55e' : pct0to1 < 0.18/0.35 ? '#fbbf24' : '#ef4444';

/* ---- Main loop ---- */
let ema = 0;
function frame(){
  const now = performance.now();
  const dt = Math.min(0.05, (now - lastFrame)/1000);
  lastFrame = now;

  const level = rms();
  ema = ema*0.85 + level*0.15;

  const warnThr = sliderToThreshold(sens.value);
  const noisyThr = warnThr*0.75;
  tooNoisy.value = level > noisyThr;

  const pct = clamp((ema / 0.35) * 100, 0, 100);
  meterPct.value = pct;

  const cap = maxBees.value;
  const base = Math.floor(cap * 0.15);
  const dyn  = Math.floor(cap * clamp((ema - warnThr + 0.05) / (0.5 - warnThr), 0, 1));
  const target = clamp(base + dyn, base, cap);

  const need = target - bees.length;
  if (need > 0){
    const intensity = clamp((ema - warnThr + 0.06) / (0.5 - warnThr), 0, 1);
    spawnBees(Math.min(need, 80), intensity);
  } else if (need < 0){
    // don't remove the currently dragged bee
    let toRemove = Math.min(-need, 40);
    for (let i=0; i<bees.length && toRemove>0;){
      if (bees[i] === draggedBee) { i++; continue; }
      bees.splice(i,1); toRemove--;
    }
  }

  const intensity = clamp((ema - warnThr + 0.06) / (0.5 - warnThr), 0, 1);
  step(dt, intensity);
  draw();

  rafId = requestAnimationFrame(frame);
}

/* ---- Public: start/stop ---- */
async function start(){
  try{
    state.error = '';
    if (!ctx){
      ctx = canvas.value.getContext('2d', { alpha: true });
      const dpr = Math.max(1, window.devicePixelRatio || 1);
      canvas.value.width = Math.floor(canvas.value.clientWidth * dpr);
      canvas.value.height = Math.floor(canvas.value.clientHeight * dpr);
      ctx.setTransform(dpr,0,0,dpr,0,0);
    }
    loadSprites();

    audioCtx = new (window.AudioContext || window.webkitAudioContext)({ latencyHint:'interactive' });
    await audioCtx.resume();
    micStream = await navigator.mediaDevices.getUserMedia({ audio:{ echoCancellation:true, noiseSuppression:true, channelCount:1 }, video:false });
    const src = audioCtx.createMediaStreamSource(micStream);
    analyser = audioCtx.createAnalyser();
    analyser.fftSize = 1024;
    analyser.smoothingTimeConstant = 0.15;
    data = new Uint8Array(analyser.fftSize);
    src.connect(analyser);

    state.live = true;
    lastFrame = performance.now();
    updateThresholdLines();
    rafId = requestAnimationFrame(frame);

    if (location.protocol !== 'https:' && location.hostname !== 'localhost'){
      state.error = 'Tip: Needs HTTPS (or localhost) for microphone access.';
    }
  }catch(err){
    stop();
    state.error = 'Microphone error: ' + (err.message || err.name || 'Unknown error');
  }
}
function stop(){
  if (rafId) cancelAnimationFrame(rafId);
  rafId = null;
  if (micStream) { micStream.getTracks().forEach(t=>t.stop()); micStream=null; }
  if (audioCtx) { audioCtx.close().catch(()=>{}); audioCtx=null; }
  analyser = null; data = null;
  state.live = false; state.tooNoisy = false;
}

/* ---- Mount/unmount: wire events ---- */
function addPointerListeners(){
  const c = canvas.value;
  c.addEventListener('mousedown', onPointerDown);
  window.addEventListener('mousemove', onPointerMove);
  window.addEventListener('mouseup', onPointerUp);

  c.addEventListener('touchstart', onPointerDown, { passive:false });
  window.addEventListener('touchmove', onPointerMove, { passive:false });
  window.addEventListener('touchend', onPointerUp);
}
function removePointerListeners(){
  const c = canvas.value;
  c.removeEventListener('mousedown', onPointerDown);
  window.removeEventListener('mousemove', onPointerMove);
  window.removeEventListener('mouseup', onPointerUp);

  c.removeEventListener('touchstart', onPointerDown);
  window.removeEventListener('touchmove', onPointerMove);
  window.removeEventListener('touchend', onPointerUp);
}

onMounted(() => {
  updateThresholdLines();
  // DPR & size updates
  const onResize = () => {
    const dpr = Math.max(1, window.devicePixelRatio || 1);
    canvas.value.width = Math.floor(canvas.value.clientWidth * dpr);
    canvas.value.height = Math.floor(canvas.value.clientHeight * dpr);
    if (ctx) ctx.setTransform(dpr,0,0,dpr,0,0);
  };
  window.addEventListener('resize', onResize);
  addPointerListeners();
});
onBeforeUnmount(() => {
  removePointerListeners();
  stop();
});
</script>

<style scoped>
.noise-bees{ position:relative; height:100vh; width:100vw; overflow:hidden;
  background:linear-gradient(#2b3642,#1f2833 40%, #11161c 75%, #0a0e12); }
.world{ position:fixed; inset:0; width:100%; height:100%; display:block;
  background:radial-gradient(1000px 600px at 50% -200px, #2a3442 0%, #1c2430 55%, #0e141b 100%); cursor:grab; }
.world:active{ cursor:grabbing; }

.hud{ position:fixed; right:18px; top:16px; width:220px; z-index:3; color:#cbd5e1;
  text-transform:uppercase; letter-spacing:.06em; }
.hud h4{ margin:16px 0 6px; font-size:12px; opacity:.8; }
.hud-row{ display:flex; align-items:center; gap:10px; height:30px; }
.label{ font-size:11px; opacity:.9; width:90px; }
input[type="range"]{ width:100%; }
.btn{ appearance:none; border:1px solid rgba(255,255,255,.12);
  padding:8px 10px; width:100%; background:#0b1218; color:#e5e7eb;
  border-radius:8px; cursor:pointer; font-weight:700; text-align:center; }
.btn:disabled{ opacity:.6; cursor:not-allowed; }
.dot{ display:inline-block; width:10px; height:10px; border-radius:50%; background:#64748b; margin-right:6px; }
.live .dot{ background:#22c55e; box-shadow:0 0 12px rgba(34,197,94,.6); }
.title{ font-weight:700; }

.meter{ position:fixed; left:16px; right:16px; bottom:16px; height:12px; border-radius:999px;
  background:rgba(255,255,255,.06); overflow:hidden; z-index:3; border:1px solid rgba(255,255,255,.08); }
.meter-fill{ height:100%; width:0%; transition:width .08s linear; }
.meter-line{ position:absolute; top:-4px; bottom:-4px; width:2px; transform:translateX(-1px); }
.meter-line.warn{ background:#fbbf24; box-shadow:0 0 8px #fbbf24; }
.meter-line.noisy{ background:#ef4444; box-shadow:0 0 10px #ef4444; }

.overlay{ position:fixed; inset:0; display:grid; place-items:center; pointer-events:none;
  opacity:0; transition:opacity .15s ease; z-index:2; }
.overlay.visible{ opacity:1; }
.overlay h1{ margin:0; font-size:clamp(28px, 6vw, 64px); font-weight:900;
  background:linear-gradient(90deg,#fff,#fecaca); -webkit-background-clip:text; background-clip:text; color:transparent;
  text-shadow:0 10px 30px rgba(239,68,68,.5); animation: throb .6s ease-in-out infinite alternate; }
@keyframes throb{ from{ transform:scale(1)} to{ transform:scale(1.05)} }

.tip{ position:fixed; left:18px; top:16px; padding:8px 12px; border-radius:10px;
  background:rgba(0,0,0,.35); border:1px solid rgba(255,255,255,.08); z-index:3; font-size:12px; }
.error{ position:fixed; left:18px; bottom:36px; right:18px; z-index:3;
  background:rgba(239,68,68,.12); color:#fecaca; border:1px solid rgba(239,68,68,.35);
  padding:10px 12px; border-radius:12px; font-size:13px; display:none; }
</style>