<template>
  <DashboardLayout>
    <div>
      <div class="flex items-center justify-between mb-8 animate-fade-in-up">
        <div>
          <h1 class="text-3xl font-extrabold text-slate-900">الدعوات المرسلة</h1>
          <p class="text-slate-500 mt-1">متابعة حالة الدعوات المرسلة للمدرسين</p>
        </div>
      </div>

      <div v-if="employments.length === 0" class="card p-10 text-center animate-fade-in-up">
        <div class="w-16 h-16 rounded-2xl bg-surface-100 flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
          </svg>
        </div>
        <h3 class="text-lg font-bold text-slate-900 mb-1">لا توجد دعوات</h3>
        <p class="text-sm text-slate-500">لم تقم بإرسال أي دعوات بعد</p>
      </div>

      <div v-else class="space-y-4">
        <div
          v-for="employment in employments"
          :key="employment.id"
          class="card p-5 animate-fade-in-up"
        >
          <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary-100 to-surface-100 overflow-hidden flex-shrink-0">
              <img v-if="employment.teacher?.photo" :src="`/storage/${employment.teacher.photo}`" :alt="employment.teacher?.user?.name" class="w-full h-full object-cover" />
              <div v-else class="w-full h-full flex items-center justify-center text-lg font-bold text-primary-600">
                {{ employment.teacher?.user?.name?.charAt(0) }}
              </div>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-start justify-between gap-4">
                <div>
                  <div class="flex items-center gap-2">
                    <h3 class="text-lg font-bold text-slate-900">{{ employment.teacher?.user?.name }}</h3>
                    <Link :href="`/teachers/${employment.teacher_id}`" class="text-primary-500 hover:text-primary-700 transition-colors flex-shrink-0" title="عرض المدرس">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                    </Link>
                  </div>
                  <div class="flex flex-wrap items-center gap-x-2 gap-y-1 mt-0.5">
                    <span class="badge-primary text-xs">{{ employment.subject?.name }}</span>
                    <span v-if="employment.teacher?.gender" class="text-xs">{{ employment.teacher.gender === 'male' ? '♂' : '♀' }}</span>
                  </div>
                  <div class="flex flex-wrap gap-1.5 mt-1.5">
                    <span v-for="grade in employment.teacher?.grades" :key="grade.id" class="inline-block px-2 py-0.5 text-xs rounded-lg bg-surface-100 text-surface-600 font-medium">
                      {{ grade.name }}
                    </span>
                  </div>
                </div>
                <span
                  class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold whitespace-nowrap flex-shrink-0"
                  :class="{
                    'bg-amber-100 text-amber-700': employment.status === 'invited',
                    'bg-emerald-100 text-emerald-700': employment.status === 'accepted',
                    'bg-blue-100 text-blue-700': employment.status === 'interviewed',
                  }"
                >
                  <span class="w-1.5 h-1.5 rounded-full" :class="{
                    'bg-amber-500': employment.status === 'invited',
                    'bg-emerald-500': employment.status === 'accepted',
                    'bg-blue-500': employment.status === 'interviewed',
                  }"></span>
                  {{ statusLabel(employment.status) }}
                </span>
              </div>

              <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mt-3 mb-3 text-sm">
                <div class="p-2 rounded-xl bg-warm-50 text-center">
                  <p class="text-xs text-warm-600 font-semibold">الخبرة</p>
                  <p class="font-bold text-slate-900">{{ employment.teacher?.experience_years || 0 }} سنة</p>
                </div>
                <div class="p-2 rounded-xl bg-primary-50 text-center">
                  <p class="text-xs text-primary-600 font-semibold">المؤهل</p>
                  <p class="font-bold text-primary-700 text-xs truncate">{{ employment.teacher?.qualification || '-' }}</p>
                </div>
                <div class="p-2 rounded-xl bg-slate-50 text-center col-span-2 sm:col-span-1">
                  <p class="text-xs text-slate-500 font-semibold">تاريخ الميلاد</p>
                  <p class="font-bold text-slate-900 text-xs">{{ employment.teacher?.birth_date || '-' }}</p>
                </div>
              </div>

              <div v-if="employment.teacher?.residence_place || employment.teacher?.current_location" class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500 mb-3">
                <span v-if="employment.teacher?.residence_place" class="flex items-center gap-1">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                  {{ employment.teacher.residence_place }}
                </span>
                <span v-if="employment.teacher?.current_location" class="flex items-center gap-1">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                  {{ employment.teacher.current_location }}
                </span>
              </div>

              <div class="flex items-center gap-4 mt-2 text-sm">
                <span class="font-medium text-slate-700 flex items-center gap-1.5" dir="ltr">
                  <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                  </svg>
                  {{ employment.teacher?.phone || '-' }}
                </span>
                <span class="font-medium text-slate-700 flex items-center gap-1.5">
                  <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                  {{ employment.teacher?.user?.email || '-' }}
                </span>
              </div>

              <p v-if="employment.message" class="text-sm text-slate-600 mt-2 bg-surface-50 p-3 rounded-xl">{{ employment.message }}</p>

              <div class="mt-3 flex flex-wrap gap-2">
                <template v-if="employment.status === 'invited'">
                  <button @click="rejectInvitation(employment.id)" class="btn-ghost text-xs px-3 py-1.5 text-red-600 hover:bg-red-50">
                    إلغاء الدعوة
                  </button>
                </template>

                <template v-if="employment.status === 'accepted'">
                  <div class="w-full p-3 rounded-xl bg-emerald-50 border border-emerald-200 mb-2">
                    <p class="text-sm font-semibold text-emerald-800 mb-1">✅ تم قبول الدعوة</p>
                  </div>
                  <Link :href="`/school/employments/${employment.id}/interview`" class="btn-primary text-xs px-4 py-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                    إجراء مقابلة
                  </Link>
                  <button @click="hireTeacher(employment.id)" class="px-4 py-2 rounded-xl bg-emerald-100 text-emerald-700 font-semibold hover:bg-emerald-200 transition-all text-xs">
                    توظيف
                  </button>
                </template>

                <template v-if="employment.status === 'interviewed' && employment.interview">
                  <div class="w-full p-3 rounded-xl bg-blue-50 border border-blue-200 mb-2">
                    <div class="flex items-center justify-between">
                      <p class="text-sm font-semibold text-blue-800">📋 نتيجة المقابلة</p>
                      <span class="text-lg font-extrabold" :class="employment.interview.total_score / employment.interview.max_score >= 0.7 ? 'text-emerald-600' : employment.interview.total_score / employment.interview.max_score >= 0.5 ? 'text-amber-600' : 'text-red-600'">
                        {{ employment.interview.total_score }}/{{ employment.interview.max_score }}
                      </span>
                    </div>
                    <div class="mt-2 w-full bg-blue-200 rounded-full h-2">
                      <div
                        class="h-2 rounded-full transition-all duration-500"
                        :class="employment.interview.total_score / employment.interview.max_score >= 0.7 ? 'bg-emerald-500' : employment.interview.total_score / employment.interview.max_score >= 0.5 ? 'bg-amber-500' : 'bg-red-500'"
                        :style="{ width: (employment.interview.max_score > 0 ? (employment.interview.total_score / employment.interview.max_score) * 100 : 0) + '%' }"
                      ></div>
                    </div>
                  </div>
                  <Link
                    :href="`/school/interviews/${employment.interview.id}`"
                    class="px-4 py-2 rounded-xl bg-blue-100 text-blue-700 font-semibold hover:bg-blue-200 transition-all text-xs"
                  >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    عرض التفاصيل
                  </Link>
                  <button @click="hireTeacher(employment.id)" class="px-4 py-2 rounded-xl bg-emerald-100 text-emerald-700 font-semibold hover:bg-emerald-200 transition-all text-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                    توظيف
                  </button>
                  <button @click="rejectInvitation(employment.id)" class="btn-ghost text-xs px-3 py-1.5 text-red-600 hover:bg-red-50">
                    رفض
                  </button>
                </template>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const props = defineProps({
  employments: Array,
})

const statusLabel = (status) => {
  const map = {
    invited: 'دعوة مرسلة',
    accepted: 'تم القبول',
    interviewed: 'مقابلة',
  }
  return map[status] || status
}

const hireTeacher = (id) => {
  useForm({}).patch(`/school/employments/${id}/hire`, {
    preserveScroll: true,
    onSuccess: () => location.reload(),
  })
}

const rejectInvitation = (id) => {
  useForm({}).patch(`/school/employments/${id}/reject`, {
    preserveScroll: true,
    onSuccess: () => location.reload(),
  })
}
</script>
