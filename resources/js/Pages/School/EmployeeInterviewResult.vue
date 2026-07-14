<template>
  <DashboardLayout>
    <div>
      <div class="flex items-center justify-between mb-8 animate-fade-in-up">
        <div>
          <h1 class="text-3xl font-extrabold text-slate-900">نتيجة المقابلة</h1>
          <p class="text-slate-500 mt-1">تقييم الموظف {{ interview.invitation?.employee?.name }}</p>
        </div>
        <Link href="/school/employee-invitations" class="btn-ghost">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          العودة
        </Link>
      </div>

      <Alert v-if="flash.success" variant="success" :title="flash.success" closeable class="mb-6 animate-fade-in-down" />
      <Alert v-if="flash.error" variant="danger" :title="flash.error" closeable class="mb-6 animate-fade-in-down" />

      <!-- Score Summary -->
      <div class="card p-8 mb-6 animate-fade-in-up">
        <div class="text-center">
          <div class="w-24 h-24 rounded-full mx-auto mb-4 flex items-center justify-center"
            :class="percentage >= 70 ? 'bg-emerald-100' : (percentage >= 50 ? 'bg-amber-100' : 'bg-red-100')">
            <span class="text-3xl font-extrabold"
              :class="percentage >= 70 ? 'text-emerald-700' : (percentage >= 50 ? 'text-amber-700' : 'text-red-700')">
              {{ percentage.toFixed(0) }}%
            </span>
          </div>
          <h2 class="text-2xl font-extrabold text-slate-900 mb-1">النتيجة العامة</h2>
          <p class="text-lg text-slate-500">
            {{ interview.total_score }} / {{ interview.max_score }} نقطة
          </p>
          <div class="mt-4 flex gap-3 justify-center">
            <span v-if="interview.invitation?.status === 'hired'" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-emerald-100 text-emerald-700 font-bold text-sm">
              تم التوظيف
            </span>
            <span v-else-if="interview.invitation?.status === 'rejected'" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-red-100 text-red-700 font-bold text-sm">
              تم الرفض
            </span>
            <span v-else class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-blue-100 text-blue-700 font-bold text-sm">
              في انتظار القرار
            </span>
          </div>
        </div>
      </div>

      <!-- Employee Info -->
      <div class="card p-6 mb-6 animate-fade-in-up">
        <h2 class="text-lg font-bold text-slate-900 mb-4">معلومات الموظف</h2>
        <div class="flex items-center gap-4">
          <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-100 to-surface-100 overflow-hidden flex-shrink-0">
            <div class="w-full h-full flex items-center justify-center text-xl font-bold text-primary-600">
              {{ interview.invitation?.employee?.name?.charAt(0) }}
            </div>
          </div>
          <div>
            <h3 class="text-lg font-bold text-slate-900">{{ interview.invitation?.employee?.name }}</h3>
            <div class="flex flex-wrap gap-1.5 mt-1">
              <span v-for="role in interview.invitation?.employee?.roles" :key="role.id" class="text-xs px-2 py-0.5 rounded-full bg-primary-100 text-primary-700">{{ role.name }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Questions & Answers -->
      <div class="space-y-4 mb-6">
        <div
          v-for="(answer, index) in interview.answers"
          :key="answer.id"
          class="card p-6 animate-fade-in-up"
        >
          <div class="flex items-start justify-between gap-4">
            <div class="flex-1">
              <div class="flex items-center gap-2 mb-1">
                <span class="w-7 h-7 rounded-full bg-primary-100 text-primary-700 text-sm font-bold flex items-center justify-center flex-shrink-0">{{ index + 1 }}</span>
                <span v-if="answer.question?.category" class="text-xs px-2 py-0.5 rounded-full bg-surface-100 text-slate-500">{{ answer.question.category }}</span>
              </div>
              <p class="text-base font-semibold text-slate-900 mt-1">{{ answer.question?.question }}</p>
              <p v-if="answer.notes" class="text-sm text-slate-500 mt-2 bg-surface-50 p-3 rounded-xl">{{ answer.notes }}</p>
            </div>
            <div class="flex-shrink-0">
              <div class="w-14 h-14 rounded-2xl flex items-center justify-center"
                :class="answer.score >= 7 ? 'bg-emerald-100' : (answer.score >= 5 ? 'bg-amber-100' : 'bg-red-100')">
                <span class="text-lg font-extrabold"
                  :class="answer.score >= 7 ? 'text-emerald-700' : (answer.score >= 5 ? 'text-amber-700' : 'text-red-700')">
                  {{ answer.score }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Notes -->
      <div v-if="interview.notes" class="card p-6 mb-6 animate-fade-in-up">
        <h3 class="text-sm font-bold text-slate-700 mb-2">ملاحظات المقابلة</h3>
        <p class="text-slate-600">{{ interview.notes }}</p>
      </div>

      <!-- Actions -->
      <div v-if="interview.invitation?.status === 'interviewed'" class="card p-6 animate-fade-in-up flex gap-3">
        <button @click="hireEmployee" class="btn-primary flex-1 py-3 text-base" :disabled="hiringForm.processing">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
          </svg>
          {{ hiringForm.processing ? 'جاري...' : 'توظيف' }}
        </button>
        <button @click="rejectEmployee" class="btn-ghost flex-1 py-3 text-base text-red-600 hover:bg-red-50">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
          رفض
        </button>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { computed } from 'vue'
import { useForm, usePage, Link } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import Alert from '@/Components/Alert.vue'

const props = defineProps({
  interview: Object,
})

const page = usePage()
const flash = page.props.flash || {}

const percentage = computed(() => {
  if (!props.interview.max_score || props.interview.max_score === 0) return 0
  return (props.interview.total_score / props.interview.max_score) * 100
})

const hiringForm = useForm({})

const hireEmployee = () => {
  hiringForm.patch(`/school/employee-interviews/${props.interview.id}/hire`, {
    preserveScroll: true,
  })
}

const rejectEmployee = () => {
  if (confirm('هل أنت متأكد من رفض هذا الموظف؟')) {
    hiringForm.patch(`/school/employee-interviews/${props.interview.id}/reject`, {
      preserveScroll: true,
    })
  }
}
</script>
