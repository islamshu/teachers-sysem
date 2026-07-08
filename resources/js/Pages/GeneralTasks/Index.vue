<template>
  <DashboardLayout>
    <div>
      <div class="mb-8 animate-fade-in-up">
        <h1 class="text-3xl font-extrabold text-slate-900">المهام العامة</h1>
        <p class="text-slate-500 mt-1">المهام الموكلة إليك</p>
      </div>

      <Alert v-if="flash.success" variant="success" :title="flash.success" closeable class="mb-6 animate-fade-in-down" />
      <Alert v-if="flash.error" variant="error" :title="flash.error" closeable class="mb-6 animate-fade-in-down" />

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 animate-fade-in-up animate-delay-50">
        <div class="card p-5 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-primary-100 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
          </div>
          <div>
            <p class="text-sm text-slate-500">إجمالي المهام</p>
            <p class="text-2xl font-bold text-slate-900">{{ totalCount }}</p>
          </div>
        </div>
        <div class="card p-5 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div>
            <p class="text-sm text-slate-500">تم الإنجاز</p>
            <p class="text-2xl font-bold text-emerald-600">{{ completedCount }}</p>
          </div>
        </div>
        <div class="card p-5 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div>
            <p class="text-sm text-slate-500">المتبقي</p>
            <p class="text-2xl font-bold text-amber-600">{{ totalCount - completedCount }}</p>
          </div>
        </div>
      </div>

      <div class="space-y-3 animate-fade-in-up animate-delay-100">
        <div
          v-for="task in tasks"
          :key="task.id"
          class="card p-5 flex items-center gap-4 transition-all duration-200"
          :class="task.completed ? 'bg-emerald-50/50 border-emerald-200' : ''"
        >
          <div
            class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 cursor-pointer transition-all duration-200"
            :class="task.completed ? 'bg-emerald-100 text-emerald-600' : 'bg-surface-100 text-slate-400 hover:bg-primary-100 hover:text-primary-600'"
            @click="task.completed ? undoTask(task) : completeTask(task)"
          >
            <svg v-if="task.completed" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
          </div>

          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2">
              <h3 class="font-bold text-slate-900" :class="task.completed ? 'line-through text-slate-400' : ''">{{ task.name }}</h3>
              <span
                class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium"
                :class="statusClass(task)"
              >
                {{ statusLabel(task) }}
              </span>
            </div>
            <p v-if="task.description" class="text-sm text-slate-500 mt-0.5">{{ task.description }}</p>
            <div class="flex gap-3 mt-1.5 text-xs text-slate-400" dir="ltr">
              <span>من {{ formatDateTime(task.start_at) }}</span>
              <span>إلى {{ formatDateTime(task.end_at) }}</span>
            </div>
          </div>

          <div class="shrink-0 text-left">
            <p v-if="task.completed && task.completed_at" class="text-xs text-slate-400" dir="ltr">
              تم في {{ formatTime(task.completed_at) }}
            </p>
            <button
              v-if="!task.completed"
              @click="completeTask(task)"
              class="btn-primary text-sm px-4 py-2"
            >
              إنجاز
            </button>
            <button
              v-else
              @click="undoTask(task)"
              class="btn-ghost text-sm px-4 py-2 text-amber-600"
            >
              تراجع
            </button>
          </div>
        </div>

        <div v-if="tasks.length === 0" class="card p-12 text-center">
          <p class="text-slate-500">لا توجد مهام موكلة إليك حالياً</p>
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
})

const page = usePage()
const flash = page.props.flash || {}

const statusClass = (task) => {
  const now = new Date()
  const start = new Date(task.start_at)
  const end = new Date(task.end_at)
  if (now < start) return 'bg-amber-100 text-amber-700'
  if (now > end) return 'bg-red-100 text-red-700'
  return 'bg-emerald-100 text-emerald-700'
}

const statusLabel = (task) => {
  const now = new Date()
  const start = new Date(task.start_at)
  const end = new Date(task.end_at)
  if (now < start) return 'قادمة'
  if (now > end) return 'منتهية'
  return 'نشط'
}

const formatDateTime = (dt) => {
  if (!dt) return '-'
  const d = new Date(dt)
  return d.toLocaleDateString('ar-SA', { day: 'numeric', month: 'short' }) + ' ' +
    d.toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' })
}

const formatTime = (dt) => {
  if (!dt) return ''
  const d = new Date(dt)
  return d.toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' })
}

const completeTask = (task) => {
  useForm({}).post(`/general-tasks/${task.id}/complete`, { preserveScroll: true })
}

const undoTask = (task) => {
  useForm({}).post(`/general-tasks/${task.id}/undo`, { preserveScroll: true })
}
</script>