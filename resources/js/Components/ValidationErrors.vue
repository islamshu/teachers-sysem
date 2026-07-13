<template>
  <div v-if="Object.keys(errors).length > 0" class="mb-6 animate-fade-in-down">
    <div class="rounded-2xl bg-red-50 border border-red-200 p-4 shadow-sm">
      <div class="flex items-start gap-3">
        <div class="w-8 h-8 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0 mt-0.5">
          <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div class="flex-1 min-w-0">
          <h3 class="text-sm font-bold text-red-800 mb-1">يوجد أخطاء في البيانات المدخلة</h3>
          <ul class="list-disc list-inside space-y-0.5">
            <li v-for="(msg, field) in errors" :key="field" class="text-sm text-red-700">
              {{ Array.isArray(msg) ? msg[0] : msg }}
            </li>
          </ul>
        </div>
        <button @click="dismiss" class="p-1.5 rounded-lg hover:bg-red-100 transition-colors flex-shrink-0">
          <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const dismissed = ref(false)

const errors = computed(() => {
  if (dismissed.value) return {}
  return page.props.errors || {}
})

const dismiss = () => {
  dismissed.value = true
}
</script>
