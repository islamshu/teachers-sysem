<template>
  <div
    class="group relative overflow-hidden rounded-xl border backdrop-blur-md p-4 shadow-lg transition-all duration-300"
    :class="[
      variant === 'success' && 'border-emerald-300/50 bg-emerald-50/50 hover:shadow-emerald-500/20',
      variant === 'warning' && 'border-amber-300/50 bg-amber-50/50 hover:shadow-amber-500/20',
      variant === 'error' && 'border-red-300/50 bg-red-50/50 hover:shadow-red-500/20',
      variant === 'info' && 'border-blue-300/50 bg-blue-50/50 hover:shadow-blue-500/20'
    ]"
  >
    <!-- Background gradient -->
    <div
      class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
      :class="[
        variant === 'success' && 'bg-gradient-to-r from-emerald-100 to-transparent',
        variant === 'warning' && 'bg-gradient-to-r from-amber-100 to-transparent',
        variant === 'error' && 'bg-gradient-to-r from-red-100 to-transparent',
        variant === 'info' && 'bg-gradient-to-r from-blue-100 to-transparent'
      ]"
    ></div>

    <div class="relative flex items-start gap-4">
      <!-- Icon -->
      <div class="flex-shrink-0">
        <svg v-if="variant === 'success'" class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <svg v-else-if="variant === 'warning'" class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
        </svg>
        <svg v-else-if="variant === 'error'" class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <svg v-else class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </div>

      <!-- Content -->
      <div class="flex-1">
        <h3
          v-if="title"
          class="font-semibold leading-none mb-1"
          :class="[
            variant === 'success' && 'text-emerald-900',
            variant === 'warning' && 'text-amber-900',
            variant === 'error' && 'text-red-900',
            variant === 'info' && 'text-blue-900'
          ]"
        >
          {{ title }}
        </h3>
        <div
          class="text-sm leading-relaxed"
          :class="[
            variant === 'success' && 'text-emerald-800',
            variant === 'warning' && 'text-amber-800',
            variant === 'error' && 'text-red-800',
            variant === 'info' && 'text-blue-800'
          ]"
        >
          <slot />
        </div>
      </div>

      <!-- Close Button -->
      <button
        v-if="closeable"
        @click="$emit('close')"
        class="flex-shrink-0 opacity-50 hover:opacity-100 transition-opacity"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  title: String,
  variant: {
    type: String,
    default: 'info',
    validator: (value) => ['success', 'warning', 'error', 'info'].includes(value),
  },
  closeable: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['close'])
</script>
