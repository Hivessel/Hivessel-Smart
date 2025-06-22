

<script setup>
import { computed, onMounted, ref } from 'vue';

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

onMounted(() => {

})

const timeAgo = (date) => {
    const now = new Date();
    const timeDiff = now - new Date(date); // Difference in milliseconds

    const seconds = Math.floor(timeDiff / 1000);
    const minutes = Math.floor(seconds / 60);
    const hours = Math.floor(minutes / 60);
    const days = Math.floor(hours / 24);
    const months = Math.floor(days / 30);
    const years = Math.floor(days / 365);

    // if (years > 0) return `${years} year${years > 1 ? 's' : ''} ago`;
    // if (months > 0) return `${months} month${months > 1 ? 's' : ''} ago`;
    // if (days > 0) return `${days} day${days > 1 ? 's' : ''} ago`;
    // if (hours > 0) return `${hours} hour${hours > 1 ? 's' : ''} ago`;
    // if (minutes > 0) return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
    // return `${seconds} second${seconds > 1 ? 's' : ''} ago`;

    if (years > 0) return `${years}y${years > 1 ? '' : ''} ago`;
    if (months > 0) return `${months}m${months > 1 ? '' : ''} ago`;
    if (days > 0) return `${days}d${days > 1 ? '' : ''} ago`;
    if (hours > 0) return `${hours}h${hours > 1 ? '' : ''} ago`;
    if (minutes > 0) return `${minutes} min${minutes > 1 ? '' : ''} ago`;
    return `${seconds} sec${seconds > 1 ? 's' : ''} ago`;
  }

</script>

<template>
  <div class="container my-4 printArea">
    <div class="d-flex align-items-center justify-content-between mb-2">
      <div class="d-flex align-items-center gap-2">
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
      <span class="text-muted" v-if="props.content.created_at">{{ timeAgo(props.content.created_at) }}</span>
    </div>

    <div class="mb-4">
      <div v-html="content.content" class="p-3 bg-white rounded shadow-sm"></div>
    </div>
  </div>
</template>

<style scoped>
.icon-24 {
  width: 24px;
  height: 24px;
}
</style>


