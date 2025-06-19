

<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
  content: Object // e.g. content.content is a JSON string or array
});

const user = computed(() => props.content?.role === 'user');

const formatContent = (rawContent) => {
  try {
    const messages = JSON.parse(rawContent);
    let skippedFirstUser = false;

    const filtered = messages.filter(msg => {
      if (!skippedFirstUser && msg.role === 'user') {
        skippedFirstUser = true;
        return false; // skip first user message
      }
      return true;
    });

    const formatted = filtered.map(msg =>
      msg.content
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        .replace(/\n{2,}/g, '<br><br>')
        .replace(/\n/g, '<br>')
    );

    return formatted.join('<br><br>');
  } catch (e) {
    // Fallback to simple formatting if not JSON
    return rawContent
      .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
      .replace(/\n{2,}/g, '<br><br>')
      .replace(/\n/g, '<br>');
  }
};

</script>

<template>
  <div class="container my-4" id="printArea">
    <div class="d-flex align-items-center gap-2 mb-2">
    <svg
      v-if="props.content.role === 'user'"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
      stroke-width="1.5"
      stroke="currentColor"
      class="icon-24"
    >
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
      />
    </svg>

    <svg
      v-else
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
      stroke-width="1.5"
      stroke="currentColor"
      class="icon-24"
    >
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"
      />
    </svg>

    <span class="fw-bold text-danger">{{ props.content.role === 'user' ? 'You' : 'AI Assistant' }}</span>
  </div>

    <div class="mb-4">
      <div v-html="formatContent(content.content)" class="p-3 bg-white rounded shadow-sm"></div>
    </div>
  </div>
</template>

<style scoped>
.icon-24 {
  width: 24px;
  height: 24px;
}
</style>


