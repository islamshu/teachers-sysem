<template>
  <DashboardLayout>
    <div>
      <div class="relative mb-8 overflow-hidden rounded-3xl bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 shadow-2xl shadow-primary-500/20 animate-fade-in-up">
        <div class="absolute inset-0 bg-grid opacity-20"></div>
        <div class="relative px-8 py-10">
          <div class="flex items-center gap-4 mb-3">
            <div class="w-14 h-14 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center text-3xl shadow-lg">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
              </svg>
            </div>
            <div>
              <h1 class="text-3xl font-extrabold text-white">مهامي اليومية</h1>
              <p class="text-primary-200">{{ todayDate }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 animate-fade-in-up animate-delay-100">
        <div class="card p-4 text-center">
          <p class="text-2xl font-extrabold text-slate-900">{{ totalCount }}</p>
          <p class="text-xs font-semibold text-slate-500 mt-1">إجمالي المهام</p>
        </div>
        <div class="card p-4 text-center">
          <p class="text-2xl font-extrabold text-emerald-600">{{ completedCount }}</p>
          <p class="text-xs font-semibold text-slate-500 mt-1">تم الإنجاز</p>
        </div>
        <div class="card p-4 text-center">
          <p class="text-2xl font-extrabold text-amber-600">{{ totalCount - completedCount }}</p>
          <p class="text-xs font-semibold text-slate-500 mt-1">المتبقي</p>
        </div>
      </div>

      <Alert v-if="flash.message" :variant="flash.status === 'error' ? 'error' : 'success'" :title="flash.message" closeable class="mb-6 animate-fade-in-down" />

      <div class="space-y-3 animate-fade-in-up animate-delay-200">
        <div
          v-for="task in tasks"
          :key="task.id"
          class="card p-5 transition-all duration-200"
          :class="task.completed ? 'bg-emerald-50/50 border-emerald-200' : 'hover:shadow-md'"
        >
          <div class="flex items-start gap-4">
            <button
              @click="toggleTask(task)"
              class="flex-shrink-0 w-7 h-7 rounded-lg border-2 transition-all duration-200 flex items-center justify-center mt-0.5"
              :class="task.completed
                ? 'bg-emerald-500 border-emerald-500 text-white'
                : 'border-slate-300 hover:border-primary-400 hover:bg-primary-50'"
            >
              <svg v-if="task.completed" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
              </svg>
            </button>
            <div class="flex-1 min-w-0">
              <h3 class="font-bold text-slate-900" :class="task.completed ? 'line-through text-slate-400' : ''">
                {{ task.name }}
              </h3>
              <p v-if="task.description" class="text-sm text-slate-500 mt-1">{{ task.description }}</p>
              <p v-if="task.completed && task.completed_at" class="text-xs text-emerald-600 mt-2 font-medium">
                تم الإنجاز {{ formatTime(task.completed_at) }}
              </p>
            </div>
          </div>
        </div>

        <div v-if="tasks.length === 0" class="card p-10 text-center">
          <div class="w-16 h-16 rounded-2xl bg-surface-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
          </div>
          <h3 class="text-lg font-bold text-slate-900 mb-1">لا توجد مهام لليوم</h3>
          <p class="text-sm text-slate-500">لم يتم تعيين مهام للأدوار الخاصة بك بعد</p>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import Alert from '@/Components/Alert.vue'

const props = defineProps({
  tasks: Array,
  completedCount: Number,
  totalCount: Number,
  today: String,
})

const page = usePage()
const flash = page.props.flash || {}

const todayDate = new Date().toLocaleDateString('ar-SA', {
  weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
})

const formatTime = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' })
}

const toggleTask = (task) => {
  const form = useForm({})
  if (task.completed) {
    form.post(`/tasks/${task.id}/undo`, {
      preserveScroll: true,
    })
  } else {
    form.post(`/tasks/${task.id}/complete`, {
      preserveScroll: true,
    })
  }
}
</script>
