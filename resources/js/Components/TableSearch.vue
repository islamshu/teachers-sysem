<template>
  <div class="relative">
    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
      <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
      </svg>
    </div>
    <input
      v-model="search"
      type="text"
      class="w-56 pr-9 pl-4 py-2 rounded-xl bg-surface-100 border border-surface-200 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all"
      :placeholder="placeholder"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: { type: String, default: '' },
  placeholder: { type: String, default: 'بحث...' },
})

const emit = defineEmits(['update:modelValue'])

const search = computed({
  get: () => props.modelValue,
  set: (v) => emit('update:modelValue', v),
})
</script>
