<script setup>
import { ref, computed, onMounted, watch } from "vue";

/* ---------- state ---------- */
const entries = ref([]);
const winners = ref([]);
const newName = ref("");
const bulk = ref("");
const sorting = ref("none");

const questions = ref([]);
const newQuestion = ref("");
const bulkQuestions = ref("");
const qSorting = ref("none");
const removeQuestionOnUse = ref(true);

const isSpinning = ref(false);
const rotation = ref(0);
const spinStart = ref(0);
const spinDuration = ref(0);
const startRotation = ref(0);
const targetRotation = ref(0);
const canvasEl = ref(null);
const ctx = ref(null);

/* ---------- modal ---------- */
const showModal = ref(false);
const selectedWinner = ref(null);
const selectedQuestion = ref(null);

/* ---------- persistence ---------- */
const STORAGE_KEY = "name-wheel-v2";
onMounted(() => {
  try {
    const saved = JSON.parse(localStorage.getItem(STORAGE_KEY) || "{}");
    if (Array.isArray(saved.entries)) entries.value = saved.entries;
    if (Array.isArray(saved.winners)) winners.value = saved.winners;
    if (Array.isArray(saved.questions)) questions.value = saved.questions;
    if (typeof saved.removeQuestionOnUse === "boolean")
      removeQuestionOnUse.value = saved.removeQuestionOnUse;
  } catch {}
  const c = canvasEl.value;
  const dpr = window.devicePixelRatio || 1;
  c.width = c.clientWidth * dpr;
  c.height = c.clientHeight * dpr;
  ctx.value = c.getContext("2d");
  draw();
  new ResizeObserver(draw).observe(c);
});
watch([entries, winners, questions, removeQuestionOnUse], () => {
  localStorage.setItem(
    STORAGE_KEY,
    JSON.stringify({
      entries: entries.value,
      winners: winners.value,
      questions: questions.value,
      removeQuestionOnUse: removeQuestionOnUse.value
    })
  );
  draw();
}, { deep: true });

/* ---------- computed ---------- */
const canSpin = computed(() => !isSpinning.value && entries.value.length >= 2);

/* ---------- helpers ---------- */
const uniqueClean = (list) =>
  Array.from(new Set(list.map((s) => s.trim()).filter(Boolean)));
const shuffleInPlace = (arr) => {
  for (let i = arr.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [arr[i], arr[j]] = [arr[j], arr[i]];
  }
};
const clamp = (min, v, max) => Math.max(min, Math.min(v, max));
const normalize = (a) => ((a % (Math.PI * 2)) + Math.PI * 2) % (Math.PI * 2);
const lerp = (a, b, t) => a + (b - a) * t;
const easeOutCubic = (x) => 1 - Math.pow(1 - x, 3);

/* ---------- names actions ---------- */
function addSingle() {
  const n = newName.value.trim();
  if (!n) return;
  entries.value = uniqueClean([...entries.value, n]);
  newName.value = "";
  if (sorting.value !== "none") applySort(sorting.value);
}
function addBulk() {
  const parts = bulk.value.split(/[\n,;]+/);
  const cleaned = uniqueClean(parts);
  if (!cleaned.length) return;
  entries.value = uniqueClean([...entries.value, ...cleaned]);
  bulk.value = "";
  if (sorting.value !== "none") applySort(sorting.value);
}
const removeAt = (i) => entries.value.splice(i, 1);
function clearAll() {
  if (isSpinning.value) return;
  entries.value = [];
  winners.value = [];
}
function applySort(dir) {
  sorting.value = dir;
  entries.value = [...entries.value].sort((a, b) => a.localeCompare(b));
  if (dir === "desc") entries.value.reverse();
}
function shuffleAll() {
  entries.value = [...entries.value];
  shuffleInPlace(entries.value);
  sorting.value = "none";
}

/* ---------- questions actions ---------- */
function addQuestionSingle() {
  const q = newQuestion.value.trim();
  if (!q) return;
  questions.value = uniqueClean([...questions.value, q]);
  newQuestion.value = "";
  if (qSorting.value !== "none") applyQSort(qSorting.value);
}
function addQuestionBulk() {
  const parts = bulkQuestions.value.split(/[\n;]+/);
  const cleaned = uniqueClean(parts);
  if (!cleaned.length) return;
  questions.value = uniqueClean([...questions.value, ...cleaned]);
  bulkQuestions.value = "";
  if (qSorting.value !== "none") applyQSort(qSorting.value);
}
const removeQuestionAt = (i) => questions.value.splice(i, 1);
function clearQuestions() { questions.value = []; }
function applyQSort(dir) {
  qSorting.value = dir;
  questions.value = [...questions.value].sort((a, b) => a.localeCompare(b));
  if (dir === "desc") questions.value.reverse();
}
function shuffleQuestions() {
  questions.value = [...questions.value];
  shuffleInPlace(questions.value);
  qSorting.value = "none";
}

/* ---------- drawing ---------- */
function wrapText(k, text, maxWidth, lineHeight) {
  const words = text.split(/\s+/);
  let line = "", y = 0;
  for (let n = 0; n < words.length; n++) {
    const test = (line ? line + " " : "") + words[n];
    const w = k.measureText(test).width;
    if (w > maxWidth && n > 0) {
      k.fillText(line, 0, y);
      line = words[n];
      y += lineHeight;
    } else {
      line = test;
    }
  }
  k.fillText(line, 0, y);
}
function draw() {
  const c = canvasEl.value, k = ctx.value;
  if (!c || !k) return;
  const dpr = window.devicePixelRatio || 1;
  if (c.width !== c.clientWidth * dpr || c.height !== c.clientHeight * dpr) {
    c.width = c.clientWidth * dpr;
    c.height = c.clientHeight * dpr;
  }
  const W = c.width, H = c.height;
  k.clearRect(0, 0, W, H);

  const cx = W / 2, cy = H / 2;
  const r = Math.min(W, H) * 0.42;
  const items = entries.value.length || 1;
  const per = (Math.PI * 2) / items;

  k.save();
  k.translate(cx, cy);
  k.rotate(rotation.value);

  for (let i = 0; i < items; i++) {
    const start = i * per, end = start + per;
    const hue = (i * (360 / items)) % 360;

    k.beginPath();
    k.moveTo(0, 0);
    k.arc(0, 0, r, start, end, false);
    k.closePath();
    k.fillStyle = `hsl(${hue} 70% 50%)`;
    k.fill();
    k.strokeStyle = "rgba(0,0,0,.15)";
    k.lineWidth = 2;
    k.stroke();

    const label = entries.value[i] ?? "";
    k.save();
    const mid = start + per / 2;
    k.rotate(mid);
    k.textAlign = "right";
    k.fillStyle = "#fff";
    k.font = `${Math.max(16, r * 0.11)}px system-ui, -apple-system, Segoe UI, Roboto`;
    k.translate(r * 0.9, 0);
    wrapText(k, label, r * 0.85, Math.max(14, r * 0.09));
    k.restore();
  }
  k.restore();

  // hub
  k.beginPath();
  k.arc(cx, cy, Math.max(10, r * 0.1), 0, Math.PI * 2);
  k.fillStyle = "#111";
  k.fill();

  // pointer (top)
  k.save();
  k.translate(cx, cy);
  k.rotate(-Math.PI / 2);
  k.beginPath();
  k.moveTo(r + 18, 0);
  k.lineTo(r - 10, -14);
  k.lineTo(r - 10, 14);
  k.closePath();
  k.fillStyle = "#fff";
  k.shadowColor = "rgba(0,0,0,.3)";
  k.shadowBlur = 6;
  k.fill();
  k.restore();
}

/* ---------- spin + popup ---------- */
function spin() {
  if (!canSpin.value) return;
  isSpinning.value = true;

  const items = entries.value.length;
  const winningIndex = Math.floor(Math.random() * items);
  const per = (Math.PI * 2) / items;
  const sliceMid = winningIndex * per + per / 2;
  const targetBase = normalize((-Math.PI / 2) - sliceMid);
  const extraTurns = 5 + Math.floor(Math.random() * 3); // 5–7 spins
  targetRotation.value = targetBase + extraTurns * Math.PI * 2;

  startRotation.value = rotation.value % (Math.PI * 2);
  spinStart.value = performance.now();
  spinDuration.value = 4800 + Math.random() * 1200;

  requestAnimationFrame(function tick(now) {
    const t = clamp(0, (now - spinStart.value) / spinDuration.value, 1);
    const eased = easeOutCubic(t);
    rotation.value = lerp(startRotation.value, targetRotation.value, eased);
    draw();
    if (t < 1) {
      requestAnimationFrame(tick);
    } else {
      rotation.value = targetRotation.value;
      draw();

      const winner = entries.value[winningIndex];
      if (winner) {
        winners.value.unshift(winner);
        entries.value.splice(winningIndex, 1);
      }

      let q = null;
      if (questions.value.length) {
        const qi = Math.floor(Math.random() * questions.value.length);
        q = questions.value[qi];
        if (removeQuestionOnUse.value) questions.value.splice(qi, 1);
      }

      selectedWinner.value = winner || null;
      selectedQuestion.value = q;
      showModal.value = true;

      isSpinning.value = false;
    }
  });
}
function closeModal() {
  showModal.value = false;
  selectedWinner.value = null;
  selectedQuestion.value = null;
}
</script>

<template>
  <div class="page">
    <div class="layout">
      <!-- Wheel -->
      <section class="panel wheel">
        <canvas ref="canvasEl" class="canvas"></canvas>

        <div class="wheel-hud">
          <button class="btn primary" :disabled="!canSpin" @click="spin">
            {{ isSpinning ? "Spinning…" : "Spin" }}
          </button>
          <div class="meta">
            <span>Entries: {{ entries.length }}</span>
            <span v-if="winners.length">Winners: {{ winners.length }}</span>
            <span v-if="questions.length">Questions: {{ questions.length }}</span>
          </div>
        </div>
      </section>

      <!-- Controls -->
      <section class="panel controls">
        <h3>Add names</h3>
        <div class="row">
          <input v-model="newName" type="text" placeholder="Type a name then press Add / Enter" @keydown.enter.prevent="addSingle" />
          <button class="btn" @click="addSingle">Add</button>
        </div>

        <textarea v-model="bulk" rows="3" placeholder="Bulk add names: paste separated by new lines, commas, or semicolons"></textarea>
        <div class="row">
          <button class="btn" @click="addBulk">Add All</button>
          <button class="btn" @click="shuffleAll">Shuffle</button>
          <button class="btn" :class="{active: sorting==='asc'}" @click="applySort('asc')">Sort A–Z</button>
          <button class="btn" :class="{active: sorting==='desc'}" @click="applySort('desc')">Sort Z–A</button>
          <button class="btn danger" @click="clearAll">Clear</button>
        </div>

        <h3 style="margin-top:14px">Entries</h3>
        <ul class="list">
          <li v-for="(n,i) in entries" :key="n + i">
            <span>{{ n }}</span>
            <button class="icon" title="Remove" @click="removeAt(i)">✕</button>
          </li>
          <li v-if="!entries.length" class="empty">No names yet — add some!</li>
        </ul>

        <h3 style="margin-top:20px">Questions (optional)</h3>
        <div class="row">
          <input v-model="newQuestion" type="text" placeholder="Type a question then press Add / Enter" @keydown.enter.prevent="addQuestionSingle" />
          <button class="btn" @click="addQuestionSingle">Add</button>
        </div>
        <textarea v-model="bulkQuestions" rows="3" placeholder="Bulk add questions: one per line (or separated by semicolons)"></textarea>
        <div class="row">
          <button class="btn" @click="addQuestionBulk">Add All</button>
          <button class="btn" @click="shuffleQuestions">Shuffle</button>
          <button class="btn" :class="{active: qSorting==='asc'}" @click="applyQSort('asc')">Sort A–Z</button>
          <button class="btn" :class="{active: qSorting==='desc'}" @click="applyQSort('desc')">Sort Z–A</button>
          <button class="btn danger" @click="clearQuestions">Clear</button>
        </div>

        <label class="toggle">
          <input type="checkbox" v-model="removeQuestionOnUse" />
          Remove question after assigning
        </label>

        <ul class="list" style="margin-top:8px">
          <li v-for="(q,i) in questions" :key="q + i">
            <span style="white-space:pre-wrap">{{ q }}</span>
            <button class="icon" title="Remove" @click="removeQuestionAt(i)">✕</button>
          </li>
          <li v-if="!questions.length" class="empty">No questions yet — optional.</li>
        </ul>

        <h3 style="margin-top:12px" v-if="winners.length">Winners (latest first)</h3>
        <ol class="list compact" v-if="winners.length">
          <li v-for="w in winners" :key="w">{{ w }}</li>
        </ol>
      </section>
    </div>

    <!-- Winner Modal (teleported to body so it always shows above everything) -->
    <teleport to="body">
      <div v-if="showModal" class="modal-backdrop" @click.self="closeModal">
        <div class="modal">
          <h2 class="win-title">🎉 We have a winner!</h2>
          <div class="winner-name">{{ selectedWinner }}</div>
          <div v-if="selectedQuestion" class="question">
            <div class="q-label">Your question:</div>
            <div class="q-text">{{ selectedQuestion }}</div>
          </div>
          <div v-else class="question none">No question assigned (none in list).</div>
          <div class="modal-actions">
            <button class="btn" @click="closeModal">Close</button>
          </div>
        </div>
      </div>
    </teleport>
  </div>
</template>

<style scoped>
.page{
  min-height:100vh;
  background:#0b1020;
  color:#eaf2ff;
  display:flex;
  align-items:stretch;
  justify-content:center;
  padding:18px;
  box-sizing:border-box;
  font-family: system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, sans-serif;
}
.layout{
  width:min(1200px, 100%);
  display:grid;
  grid-template-columns: 1.2fr 1fr;
  gap:16px;
}
.panel{
  background: linear-gradient(180deg, rgba(255,255,255,.05), rgba(255,255,255,.02));
  border:1px solid rgba(255,255,255,.08);
  border-radius:16px;
  padding:16px;
  box-shadow: 0 10px 30px rgba(0,0,0,.3);
}
.wheel{ display:flex; flex-direction:column; gap:8px; align-items:center; justify-content:center; }
.canvas{
  width:100%;
  height:min(70vh, 650px);
  display:block;
  border-radius:12px;
  background: radial-gradient(1200px 600px at 50% 0%, rgba(255,255,255,.05), rgba(9,10,22,0) 60%),
              radial-gradient(900px 500px at 50% 100%, rgba(255,255,255,.04), rgba(9,10,22,0) 60%);
}
.wheel-hud{ display:flex; gap:12px; align-items:center; }
.meta{ opacity:.8; font-size:.9rem; display:flex; gap:12px; }
.controls .row{ display:flex; gap:8px; margin:8px 0 4px; }
.controls input[type="text"]{
  flex:1; padding:10px 12px; background:#0f1530; color:#fff;
  border:1px solid rgba(255,255,255,.1); border-radius:10px; outline:none;
}
.controls textarea{
  width:100%; padding:10px 12px; background:#0f1530; color:#fff;
  border:1px solid rgba(255,255,255,.1); border-radius:10px; resize:vertical;
}
.toggle{ display:flex; align-items:center; gap:8px; margin-top:8px; font-size:.95rem; opacity:.95; }
.btn{
  appearance:none; background:#1b2348; color:#fff; border:1px solid rgba(255,255,255,.12);
  padding:9px 12px; border-radius:10px; cursor:pointer; transition:transform .08s ease, background .2s ease;
}
.btn:hover{ transform:translateY(-1px); }
.btn.primary{ background:#2e65ff; border-color:#2e65ff; font-weight:600; }
.btn:disabled{ opacity:.5; cursor:not-allowed; transform:none; }
.btn.active{ outline:2px solid #2e65ff; }
.btn.danger{ background:#5a1e2a; border-color:#7d2232; }

.list{
  list-style:none; margin:8px 0 0; padding:0; max-height: 30vh; overflow:auto;
  border:1px dashed rgba(255,255,255,.12); border-radius:10px;
}
.list li{
  display:flex; justify-content:space-between; align-items:center; gap:12px;
  padding:8px 10px; border-bottom:1px dashed rgba(255,255,255,.07);
}
.list li:last-child{ border-bottom:0; }
.list li.empty{ opacity:.7; font-style:italic; justify-content:center; }
.list.compact li{ justify-content:flex-start; gap:8px; }
.icon{ background:transparent; color:#fff; border:0; font-size:16px; cursor:pointer; padding:6px 8px; border-radius:8px; }
.icon:hover{ background:rgba(255,255,255,.07); }
@media (max-width: 950px){
  .layout{ grid-template-columns: 1fr; }
  .canvas{ height: 56vh; }
}

/* ----- modal (teleported) ----- */
.modal-backdrop{
  position:fixed; inset:0; background:rgba(0,0,0,.55);
  display:flex; align-items:center; justify-content:center;
  padding:20px; z-index: 999999;  /* make sure it's above any Blade/header overlays */
}
.modal{
  width:min(560px, 95vw);
  background:#0d1430;
  border:1px solid rgba(255,255,255,.15);
  border-radius:16px; padding:20px;
  box-shadow:0 20px 60px rgba(0,0,0,.45);
}
.win-title{ margin:0 0 6px 0; }
.winner-name{
  font-size:2rem; font-weight:800; letter-spacing:.3px;
  background:linear-gradient(90deg,#8ab4ff,#b1e1ff);
  -webkit-background-clip:text; background-clip:text; color:transparent;
  margin-bottom:10px;
}
.question{ background:#0f1b48; border:1px solid rgba(255,255,255,.08); border-radius:12px; padding:12px; }
.question .q-label{ font-weight:600; opacity:.9; margin-bottom:6px; }
.question .q-text{ white-space:pre-wrap; line-height:1.4; }
.question.none{ opacity:.8; font-style:italic; }
.modal-actions{ display:flex; justify-content:flex-end; margin-top:12px; }
.modal {
    display: block;
}
</style>