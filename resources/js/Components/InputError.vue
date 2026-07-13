<template>
  <p v-if="message" class="mt-1.5 text-sm text-red-500 font-medium">{{ message }}</p>
</template>

<script setup>
defineProps({
  message: String,
})
</script>
