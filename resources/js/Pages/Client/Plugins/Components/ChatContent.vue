<script setup>
import { computed } from 'vue';
const props = defineProps({
    content: Object
});

const user = computed(() => props.content?.role === 'user');

const formattedText = (string) => {
    return string.replace(/\n/g, '<br>');
}

</script>

<template>
  <div>
    <section class="col-8 text-start ps-3 p-3 " v-html="formattedText(content?.content)">
    </section>
  </div>
</template>

<style scoped>

</style>

