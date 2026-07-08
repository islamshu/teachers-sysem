<template>
  <div class="relative" ref="container">
    <div
      @click="open = !open"
      class="input-base flex items-center flex-wrap gap-1.5 cursor-pointer min-h-[2.75rem]"
      :class="{ 'ring-2 ring-primary-500 border-primary-500': open }"
    >
      <span
        v-for="item in selectedItems"
        :key="item.id"
        class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-primary-100 text-primary-700 text-sm font-semibold"
      >
        {{ item.label }}
        <button @click.stop="remove(item)" class="hover:text-primary-900">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </span>
      <span v-if="selectedItems.length === 0" class="text-slate-400 text-sm">{{ placeholder }}</span>
    </div>

    <div
      v-if="open"
      class="absolute z-50 mt-1 w-full bg-white border border-surface-200 rounded-xl shadow-lg"
    >
      <div class="p-2 border-b border-surface-100">
        <input
          v-model="search"
          type="text"
          class="w-full px-3 py-2 rounded-lg bg-surface-50 border border-surface-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
          :placeholder="searchPlaceholder"
          @click.stop
        />
      </div>
      <div class="max-h-48 overflow-y-auto">
        <div v-if="filteredOptions.length === 0" class="p-4 text-sm text-slate-400 text-center">
          لا توجد نتائج
        </div>
        <div
          v-for="option in filteredOptions"
          :key="option.id"
          @click="toggle(option)"
          class="flex items-center gap-3 px-4 py-2.5 cursor-pointer hover:bg-primary-50 transition-colors"
          :class="{ 'bg-primary-50': isSelected(option) }"
        >
          <input
            type="checkbox"
            :checked="isSelected(option)"
            class="w-4 h-4 rounded border-surface-300 text-primary-600 focus:ring-primary-500"
            @click.stop
          />
          <span class="text-sm text-slate-700 font-medium" v-html="highlightMatch(option.label)"></span>
        </div>
      </div>
    </div>

    <p v-if="error" class="mt-1 text-xs text-red-500 font-semibold">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  modelValue: { type: Array, default: () => [] },
  options: { type: Array, default: () => [] },
  placeholder: { type: String, default: 'اختر...' },
  searchPlaceholder: { type: String, default: 'بحث...' },
  error: { type: String, default: '' },
})

const emit = defineEmits(['update:modelValue'])

const open = ref(false)
const search = ref('')
const container = ref(null)

const selectedItems = computed(() =>
  props.options.filter((o) => props.modelValue.includes(o.id))
)

const filteredOptions = computed(() => {
  if (!search.value) return props.options
  const q = search.value.toLowerCase()
  return props.options.filter((o) => o.label.toLowerCase().includes(q))
})

function highlightMatch(label) {
  if (!search.value) return label
  const q = search.value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')
  const regex = new RegExp(`(${q})`, 'gi')
  return label.replace(regex, '<span class="bg-primary-200 rounded px-0.5">$1</span>')
}

function isSelected(option) {
  return props.modelValue.includes(option.id)
}

function toggle(option) {
  const selected = [...props.modelValue]
  const idx = selected.indexOf(option.id)
  if (idx > -1) {
    selected.splice(idx, 1)
  } else {
    selected.push(option.id)
  }
  emit('update:modelValue', selected)
}

function remove(item) {
  toggle(item)
}

function handleClickOutside(e) {
  if (container.value && !container.value.contains(e.target)) {
    open.value = false
  }
}

if (typeof window !== 'undefined') {
  window.addEventListener('click', handleClickOutside)
}
</script>
