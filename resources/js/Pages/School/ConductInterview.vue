<template>
  <DashboardLayout>
    <div>
      <div class="flex items-center justify-between mb-8 animate-fade-in-up">
        <div>
          <h1 class="text-3xl font-extrabold text-slate-900">إجراء مقابلة</h1>
          <p class="text-slate-500 mt-1">تقييم المدرس {{ employment.teacher?.user?.name }}</p>
        </div>
        <Link href="/school/invitations" class="btn-ghost">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          العودة
        </Link>
      </div>

      <Alert v-if="flash.success" variant="success" :title="flash.success" closeable class="mb-6 animate-fade-in-down" />
      <Alert v-if="flash.error" variant="danger" :title="flash.error" closeable class="mb-6 animate-fade-in-down" />

      <div v-if="questions.length === 0" class="card p-10 text-center animate-fade-in-up">
        <p class="text-slate-500">لا توجد أسئلة مقابلة مضافة بعد. يرجى التواصل مع الإدارة.</p>
      </div>

      <form v-else @submit.prevent="submitInterview" class="space-y-6">
        <div class="card p-6 animate-fade-in-up">
          <h2 class="text-lg font-bold text-slate-900 mb-4">معلومات المدرس</h2>
          <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-100 to-surface-100 overflow-hidden flex-shrink-0">
              <img v-if="employment.teacher?.photo" :src="`/storage/${employment.teacher.photo}`" :alt="employment.teacher?.user?.name" class="w-full h-full object-cover" />
              <div v-else class="w-full h-full flex items-center justify-center text-xl font-bold text-primary-600">
                {{ employment.teacher?.user?.name?.charAt(0) }}
              </div>
            </div>
            <div>
              <h3 class="text-lg font-bold text-slate-900">{{ employment.teacher?.user?.name }}</h3>
              <p class="text-sm text-slate-500">{{ employment.subject?.name }}</p>
            </div>
          </div>
        </div>

        <div class="space-y-4">
          <div
            v-for="(q, index) in questions"
            :key="q.id"
            class="card p-6 animate-fade-in-up"
          >
            <div class="flex items-start justify-between gap-4 mb-4">
              <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                  <span class="w-7 h-7 rounded-full bg-primary-100 text-primary-700 text-sm font-bold flex items-center justify-center flex-shrink-0">{{ index + 1 }}</span>
                  <span v-if="q.category" class="text-xs px-2 py-0.5 rounded-full bg-surface-100 text-slate-500">{{ q.category }}</span>
                </div>
                <p class="text-base font-semibold text-slate-900 mt-1">{{ q.question }}</p>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                  النتيجة (0-10) <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center gap-2">
                  <input
                    :ref="el => { if (el) scoreRefs[index] = el }"
                    v-model.number="answers[index].score"
                    type="range"
                    min="0"
                    max="10"
                    step="0.5"
                    class="flex-1 accent-primary-600"
                    required
                  />
                  <span class="w-10 h-10 rounded-xl bg-primary-100 text-primary-700 font-bold flex items-center justify-center">
                    {{ answers[index].score }}
                  </span>
                </div>
              </div>

              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">ملاحظات</label>
                <input
                  v-model="answers[index].notes"
                  type="text"
                  class="input-base"
                  placeholder="ملاحظة (اختياري)"
                />
              </div>
            </div>
          </div>
        </div>

        <div class="card p-6 animate-fade-in-up">
          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">ملاحظات عامة</label>
            <textarea
              v-model="notes"
              class="input-base min-h-[80px]"
              placeholder="أي ملاحظات إضافية عن المقابلة..."
            ></textarea>
          </div>
        </div>

        <div class="card p-6 animate-fade-in-up">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-slate-500">إجمالي النقاط المحتملة: <strong class="text-slate-900">{{ questions.length * 10 }}</strong></p>
              <p class="text-sm text-slate-500">النتيجة الحالية: <strong class="text-primary-700">{{ currentTotal }}</strong></p>
            </div>
            <button type="submit" class="btn-primary px-8 py-3 text-base" :disabled="form.processing">
              <svg v-if="form.processing" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
              </svg>
              <template v-else>إتمام المقابلة</template>
            </button>
          </div>
        </div>
      </form>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm, usePage, Link } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import Alert from '@/Components/Alert.vue'

const props = defineProps({
  employment: Object,
  questions: Array,
  existingInterview: Object,
})

const page = usePage()
const flash = page.props.flash || {}

const notes = ref('')
const scoreRefs = ref([])

const answers = ref(props.questions.map(q => ({
  question_id: q.id,
  score: 5,
  notes: '',
})))

const currentTotal = computed(() => {
  return answers.value.reduce((sum, a) => sum + (Number(a.score) || 0), 0)
})

const form = useForm({})

const submitInterview = () => {
  form
    .transform(() => ({
      answers: answers.value,
      notes: notes.value,
    }))
    .post(`/school/employments/${props.employment.id}/interview`, {
      preserveScroll: true,
    })
}
</script>
