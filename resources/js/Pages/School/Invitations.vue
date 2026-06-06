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
                  <h3 class="text-lg font-bold text-slate-900">{{ employment.teacher?.user?.name }}</h3>
                  <p class="text-sm text-slate-500">{{ employment.subject?.name }}</p>
                </div>
                <span
                  class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold whitespace-nowrap"
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
                    <p class="text-xs text-emerald-600">يمكنك الآن التواصل مع المدرس عبر:</p>
                    <div class="flex gap-4 mt-1 text-sm">
                      <span class="font-medium text-slate-700" dir="ltr">{{ employment.teacher?.phone || '-' }}</span>
                      <span class="font-medium text-slate-700">{{ employment.teacher?.user?.email || '-' }}</span>
                    </div>
                  </div>
                  <button @click="markInterviewed(employment.id)" class="btn-primary text-xs px-4 py-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    تمت المقابلة
                  </button>
                  <button @click="hireTeacher(employment.id)" class="px-4 py-2 rounded-xl bg-emerald-100 text-emerald-700 font-semibold hover:bg-emerald-200 transition-all text-xs">
                    توظيف
                  </button>
                </template>

                <template v-if="employment.status === 'interviewed'">
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
import { useForm } from '@inertiajs/vue3'
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

const markInterviewed = (id) => {
  useForm({}).patch(`/school/employments/${id}/interviewed`, {
    preserveScroll: true,
    onSuccess: () => location.reload(),
  })
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
