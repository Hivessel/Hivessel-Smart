<template>
    
  <div class="noise-detector">
    <canvas ref="worldRef" id="world"></canvas>

    <div class="overlay" :class="{ visible: isNoisy }">
      <h1>Too Noisy</h1>
    </div>

    <div class="tip" v-show="!live">
      <span class="dot"></span>Click “Start Microphone” — talk/clap and watch the bees pile up 🐝
    </div>

    <!-- HUD -->
    <div class="hud" :class="{ live }">
      <div style="display:flex;align-items:center;gap:8px">
        <span class="dot" :id="'liveDot'"></span> MICROPHONE
      </div>

      <div class="row" style="margin-top:6px">
        <button class="btn" @click="start" :disabled="live">Start Microphone</button>
      </div>

      <div class="row">
        <button class="btn" @click="() => stop(false)" :disabled="!live">Stop</button>
      </div>

      <h4>Sensitivity</h4>
      <div class="row">
        <span class="label">Trigger</span>
        <input v-model.number="sens" type="range" min="5" max="60" />
      </div>

      <h4>Total</h4>
      <div class="row">
        <span class="label">Max Bees</span>
        <input v-model.number="maxBees" type="range" min="120" max="900" />
      </div>

      <h4>Theme</h4>
      <div class="row">
        <span class="label">Bee Size</span>
        <input v-model.number="beeSize" type="range" min="24" max="96" />
      </div>
    </div>

    <!-- Meter -->
    <div class="meter" aria-label="input level">
      <div class="meter-fill" :style="{ width: meterPct + '%', background: meterBg }"></div>
      <div class="meter-line warn" :style="{ left: warnPct + '%' }"></div>
      <div class="meter-line noisy" :style="{ left: noisyPct + '%' }"></div>
    </div>

    <div class="error" v-show="errMsg">{{ errMsg }}</div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onBeforeUnmount, watch } from 'vue';

/* ---------- Refs & State ---------- */
const worldRef = ref(null);
const live = ref(false);
const isNoisy = ref(false);
const errMsg = ref('');

const sens = ref(40);     // 5..60
const maxBees = ref(650); // 120..900
const beeSize = ref(64);  // 24..96

// Meter visuals
const meterPct = ref(0);
const meterBg = ref('linear-gradient(90deg, #38bdf8, #a78bfa, #f472b6)');

const clamp = (n, lo, hi) => Math.min(hi, Math.max(lo, n));
const lerp = (a, b, t) => a + (b - a) * t;

/* ---------- Canvas ---------- */
let canvas, ctx;
function resizeCanvas() {
  if (!canvas) return;
  const dpr = Math.max(1, window.devicePixelRatio || 1);
  canvas.width = Math.floor(canvas.clientWidth * dpr);
  canvas.height = Math.floor(canvas.clientHeight * dpr);
  ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
}

/* ---------- Audio ---------- */
let audioCtx = null, analyser = null, micStream = null, data = null, rafId = null;

/* ---------- Bees (physics) ---------- */
const bees = [];
let lastFrame = performance.now();
const gravityBase = 980;     // px/s^2
const restitution = 0.85;    // bounciness
const airBase = 0.012;       // air drag

/* ---------- Multi-sprite loading ---------- */
const beeSources = [
  'https://images.vexels.com/media/users/3/235199/isolated/preview/2c46a957f757358d387baf0671701af5-flying-color-stroke-honey-bee.png',
  'https://pngfre.com/wp-content/uploads/bee-46-300x300.png',
  'https://uxwing.com/wp-content/themes/uxwing/download/animals-and-birds/bee-icon.png'
];
const beeSprites = [];
let loadedCount = 0;
for (const src of beeSources) {
  const img = new Image();
  img.src = src;
  img.crossOrigin = 'anonymous';
  img.onload = () => loadedCount++;
  beeSprites.push(img);
}
function beesReady() {
  return loadedCount === beeSources.length && beeSprites.length > 0;
}

/* ---------- Thresholds & meter lines ---------- */
function sliderToThreshold(v) { return 0.26 - (v * 0.0037); } // 5..60 → ~0.22..0.04 RMS

const warnPct = computed(() => {
  const t = sliderToThreshold(sens.value);
  return clamp((t / 0.35) * 100, 0, 100);
});
const noisyPct = computed(() => {
  const t = sliderToThreshold(sens.value) * 0.75;
  return clamp((t / 0.35) * 100, 0, 100);
});

/* ---------- Collisions (spatial hashing) ---------- */
const cellSize = 120;
function buildSpatial(items) {
  const map = new Map();
  for (let i = 0; i < items.length; i++) {
    const b = items[i];
    const minx = Math.floor((b.x - b.r) / cellSize);
    const miny = Math.floor((b.y - b.r) / cellSize);
    const maxx = Math.floor((b.x + b.r) / cellSize);
    const maxy = Math.floor((b.y + b.r) / cellSize);
    for (let gx = minx; gx <= maxx; gx++) {
      for (let gy = miny; gy <= maxy; gy++) {
        const key = gx + ',' + gy;
        if (!map.has(key)) map.set(key, []);
        map.get(key).push(i);
      }
    }
  }
  return map;
}
function collideAndResolve(items) {
  const map = buildSpatial(items);
  const checked = new Set();
  for (const idxs of map.values()) {
    for (let a = 0; a < idxs.length; a++) {
      for (let b = a + 1; b < idxs.length; b++) {
        const i = idxs[a], j = idxs[b];
        const key = i < j ? (i + '-' + j) : (j + '-' + i);
        if (checked.has(key)) continue;
        checked.add(key);

        const A = items[i], B = items[j];
        const dx = B.x - A.x, dy = B.y - A.y;
        const minD = A.r + B.r; const d2 = dx * dx + dy * dy;
        if (d2 > 0 && d2 < minD * minD) {
          const d = Math.sqrt(d2) || 1, nx = dx / d, ny = dy / d, overlap = (minD - d);
          A.x -= nx * overlap * 0.5; A.y -= ny * overlap * 0.5;
          B.x += nx * overlap * 0.5; B.y += ny * overlap * 0.5;

          const rvx = B.vx - A.vx, rvy = B.vy - A.vy, rel = rvx * nx + rvy * ny;
          if (rel < 0) {
            const jImpulse = -(1 + restitution) * rel * 0.5;
            A.vx -= jImpulse * nx; A.vy -= jImpulse * ny;
            B.vx += jImpulse * nx; B.vy += jImpulse * ny;
          }
        }
      }
    }
  }
}

/* ---------- Spawn / Step / Draw ---------- */
function spawnBees(count, intensity) {
  const W = canvas.clientWidth, H = canvas.clientHeight;
  for (let i = 0; i < count; i++) {
    const maxR = beeSize.value;
    const r = lerp(18, maxR, Math.pow(intensity, 0.65) * (0.5 + Math.random() * 0.5));
    const x = Math.random() * (W - 2 * r) + r;
    const y = H - r - Math.random() * 60;
    const speed = 120 + intensity * 520;
    const angle = (-Math.PI / 2) + (Math.random() * 0.9 - 0.45);
    bees.push({
      x, y, r,
      vx: Math.cos(angle) * speed,
      vy: Math.sin(angle) * speed,
      rot: Math.random() * Math.PI * 2,
      vr: (Math.random() * 2 - 1) * (0.4 + intensity * 0.8),
      img: beeSprites[(Math.random() * beeSprites.length) | 0]
    });
  }
}
function step(dt, intensity) {
  const W = canvas.clientWidth, H = canvas.clientHeight;
  const g = gravityBase * (0.5 + intensity);
  const air = airBase * (0.6 + intensity * 1.4);

  for (let b of bees) {
    b.vy += g * dt;
    b.vx *= (1 - air); b.vy *= (1 - air * 0.7);
    b.x += b.vx * dt; b.y += b.vy * dt;
    b.rot += b.vr * dt;

    if (b.x - b.r < 0) { b.x = b.r; b.vx = -b.vx * restitution; }
    if (b.x + b.r > W) { b.x = W - b.r; b.vx = -b.vx * restitution; }
    if (b.y - b.r < 0) { b.y = b.r; b.vy = -b.vy * restitution; }
    if (b.y + b.r > H) { b.y = H - b.r; b.vy = -b.vy * restitution; }
  }
  collideAndResolve(bees);
}
function draw() {
  const W = canvas.clientWidth, H = canvas.clientHeight;
  ctx.clearRect(0, 0, W, H);
  if (!beesReady()) return;

  for (let b of bees) {
    // soft shadow
    ctx.globalAlpha = 0.25;
    ctx.beginPath();
    ctx.ellipse(b.x, b.y + b.r * 0.85, b.r * 0.85, b.r * 0.35, 0, 0, Math.PI * 2);
    ctx.fillStyle = '#000'; ctx.fill();
    ctx.globalAlpha = 1;

    // sprite
    const s = b.r * 2;
    ctx.save();
    ctx.translate(b.x, b.y);
    ctx.rotate(b.rot);
    const squash = clamp(1 - b.vy / 900, 0.78, 1.08);
    ctx.scale(1, squash);
    ctx.drawImage(b.img, -s / 2, -s / 2, s, s);
    ctx.restore();
  }
}

/* ---------- Audio helpers ---------- */
function rms() {
  analyser.getByteTimeDomainData(data);
  let sum = 0;
  for (let i = 0; i < data.length; i++) {
    const v = (data[i] - 128) / 128;
    sum += v * v;
  }
  return Math.sqrt(sum / data.length);
}
function colorForLevel(x) {
  if (x < 0.08) return '#22c55e';
  if (x < 0.18) return '#fbbf24';
  return '#ef4444';
}

/* ---------- Main loop ---------- */
let ema = 0;
function loop() {
  const now = performance.now();
  const dt = Math.min(0.05, (now - lastFrame) / 1000);
  lastFrame = now;

  const level = rms();
  ema = ema * 0.85 + level * 0.15;

  const warnThr = sliderToThreshold(sens.value);
  const noisyThr = warnThr * 0.75;
  isNoisy.value = level > noisyThr;

  const pct = clamp((ema / 0.35) * 100, 0, 100);
  meterPct.value = pct;
  meterBg.value = `linear-gradient(90deg, ${colorForLevel(ema)}, #a78bfa)`;

  const cap = maxBees.value;
  const base = Math.floor(cap * 0.15);
  const dyn = Math.floor(cap * clamp((ema - warnThr + 0.05) / (0.5 - warnThr), 0, 1));
  const target = clamp(base + dyn, base, cap);

  const need = target - bees.length;
  if (need > 0) {
    const intensity = clamp((ema - warnThr + 0.06) / (0.5 - warnThr), 0, 1);
    spawnBees(Math.min(need, 80), intensity);
  } else if (need < 0) {
    bees.splice(0, Math.min(-need, 40));
  }

  const intensity = clamp((ema - warnThr + 0.06) / (0.5 - warnThr), 0, 1);
  step(dt, intensity);
  draw();

  rafId = requestAnimationFrame(loop);
}

/* ---------- Controls ---------- */
async function start() {
  try {
    errMsg.value = '';
    audioCtx = new (window.AudioContext || window.webkitAudioContext)({ latencyHint: 'interactive' });
    await audioCtx.resume();
    micStream = await navigator.mediaDevices.getUserMedia({
      audio: { echoCancellation: true, noiseSuppression: true, channelCount: 1 },
      video: false
    });
    const src = audioCtx.createMediaStreamSource(micStream);
    analyser = audioCtx.createAnalyser();
    analyser.fftSize = 1024;
    analyser.smoothingTimeConstant = 0.15;
    data = new Uint8Array(analyser.fftSize);
    src.connect(analyser);

    live.value = true;
    lastFrame = performance.now();
    rafId = requestAnimationFrame(loop);
  } catch (err) {
    stop(true);
    errMsg.value = 'Microphone error: ' + (err.message || err.name || 'Unknown error');
  }
}
function stop(isError = false) {
  if (rafId) cancelAnimationFrame(rafId);
  rafId = null;
  if (micStream) { micStream.getTracks().forEach(t => t.stop()); micStream = null; }
  if (audioCtx) { audioCtx.close().catch(() => {}); audioCtx = null; }
  analyser = null; data = null;
  live.value = false;
  isNoisy.value = false;
}

/* ---------- Lifecycle ---------- */
function handleVisibility() {
  if (document.hidden) { if (audioCtx && audioCtx.state !== 'closed') stop(false); }
}

onMounted(() => {
  canvas = worldRef.value;
  ctx = canvas.getContext('2d', { alpha: true });
  resizeCanvas();
  window.addEventListener('resize', resizeCanvas);
  document.addEventListener('visibilitychange', handleVisibility);

  if (location.protocol !== 'https:' && location.hostname !== 'localhost') {
    errMsg.value = 'Tip: Needs HTTPS (or localhost) for microphone access.';
  }
});

onBeforeUnmount(() => {
  window.removeEventListener('resize', resizeCanvas);
  document.removeEventListener('visibilitychange', handleVisibility);
  stop(false);
});

/* ---------- Reactive side-effects ---------- */
watch(sens, () => {
  // meter lines recompute via computed props; nothing else needed here
});
</script>

<style scoped>
:root{
  --warn:#fbbf24; --danger:#ef4444;
}
*{box-sizing:border-box}
.noise-detector{height:100vh;width:100vw;overflow:hidden;
  background:linear-gradient(#2b3642,#1f2833 40%, #11161c 75%, #0a0e12);
  color:#e6eefc; font-family:system-ui, Inter, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
}
canvas#world{ position:fixed; inset:0; width:100%; height:100%; display:block; background:
  radial-gradient(1000px 600px at 50% -200px, #2a3442 0%, #1c2430 55%, #0e141b 100%); }

/* HUD (right) */
.hud{
  position:fixed; right:18px; top:16px; width:220px; z-index:3;
  color:#cbd5e1; text-transform:uppercase; letter-spacing:.06em;
}
.hud h4{ margin:16px 0 6px; font-size:12px; opacity:.8; }
.row{ display:flex; align-items:center; gap:10px; height:30px; }
.row .label{ font-size:11px; opacity:.9; width:90px; }
input[type="range"]{ width:100%; }
.btn{
  appearance:none; border:1px solid rgba(255,255,255,.12);
  padding:8px 10px; width:100%; background:#0b1218; color:#e5e7eb;
  border-radius:8px; cursor:pointer; font-weight:700; text-align:center;
}
.btn:disabled{opacity:.6; cursor:not-allowed}
.dot{display:inline-block; width:10px; height:10px; border-radius:50%; background:#64748b; margin-right:6px}
.live .dot{ background:#22c55e; box-shadow:0 0 12px rgba(34,197,94,.6) }

/* Meter (bottom) */
.meter{ position:fixed; left:16px; right:16px; bottom:16px; height:12px; border-radius:999px;
  background:rgba(255,255,255,.06); overflow:hidden; z-index:3; border:1px solid rgba(255,255,255,.08); }
.meter-fill{ height:100%; width:0%; transition: width .08s linear; }
.meter-line{ position:absolute; top:-4px; bottom:-4px; width:2px; transform:translateX(-1px) }
.meter-line.warn{ background:var(--warn); box-shadow:0 0 8px var(--warn) }
.meter-line.noisy{ background:var(--danger); box-shadow:0 0 10px var(--danger) }

/* Overlay */
.overlay{ position:fixed; inset:0; display:grid; place-items:center; pointer-events:none; opacity:0; transition:opacity .15s; z-index:2; }
.overlay.visible{ opacity:1; }
.overlay h1{
  margin:0; font-size:clamp(28px, 6vw, 64px); font-weight:900;
  background:linear-gradient(90deg,#fff,#fecaca); -webkit-background-clip:text; background-clip:text; color:transparent;
  text-shadow:0 10px 30px rgba(239,68,68,.5); animation: throb .6s ease-in-out infinite alternate;
}
@keyframes throb{ from{ transform:scale(1)} to{ transform:scale(1.05)} }

/* Tip & Error */
.tip{
  position:fixed; left:18px; top:16px; padding:8px 12px; border-radius:10px;
  background:rgba(0,0,0,.35); border:1px solid rgba(255,255,255,.08); z-index:3; font-size:12px;
}
.error{ position:fixed; left:18px; bottom:36px; right:18px; z-index:3;
  background:rgba(239,68,68,.12); color:#fecaca; border:1px solid rgba(239,68,68,.35);
  padding:10px 12px; border-radius:12px; font-size:13px; display:block; }
.error:empty{ display:none; }
</style>