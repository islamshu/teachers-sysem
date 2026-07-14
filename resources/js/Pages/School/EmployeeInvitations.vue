<template>
  <DashboardLayout>
    <div>
      <div class="flex items-center justify-between mb-8 animate-fade-in-up">
        <div>
          <h1 class="text-3xl font-extrabold text-slate-900">دعوات الموظفين المرسلة</h1>
          <p class="text-slate-500 mt-1">متابعة حالة الدعوات المرسلة للموظفين</p>
        </div>
      </div>

      <div v-if="employments.length === 0" class="card p-10 text-center animate-fade-in-up">
        <div class="w-16 h-16 rounded-2xl bg-surface-100 flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
          </svg>
        </div>
        <h3 class="text-lg font-bold text-slate-900 mb-1">لا توجد دعوات</h3>
        <p class="text-sm text-slate-500">لم تقم بإرسال أي دعوات للموظفين بعد</p>
      </div>

      <div v-else class="space-y-4">
        <div
          v-for="employment in employments"
          :key="employment.id"
          class="card p-5 animate-fade-in-up"
        >
          <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary-100 to-surface-100 overflow-hidden flex-shrink-0">
              <div class="w-full h-full flex items-center justify-center text-lg font-bold text-primary-600">
                {{ employment.employee?.name?.charAt(0) }}
              </div>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-start justify-between gap-4">
                <div>
                  <h3 class="text-lg font-bold text-slate-900">{{ employment.employee?.name }}</h3>
                  <div class="flex flex-wrap items-center gap-x-2 gap-y-1 mt-0.5">
                    <span v-for="role in employment.employee?.roles" :key="role.id" class="text-xs px-2 py-0.5 rounded-full bg-primary-100 text-primary-700">{{ role.name }}</span>
                  </div>
                  <div class="flex flex-wrap gap-1.5 mt-1.5">
                    <span v-for="branch in employment.employee?.branches" :key="branch.id" class="inline-block px-2 py-0.5 text-xs rounded-lg bg-surface-100 text-surface-600 font-medium">
                      {{ branch.name }}
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

              <p v-if="employment.message" class="text-sm text-slate-600 mt-2 bg-surface-50 p-3 rounded-xl">{{ employment.message }}</p>

              <div class="mt-3 flex flex-wrap gap-2">
                <template v-if="employment.status === 'invited'">
                  <button @click="rejectInvitation(employment.id)" class="btn-ghost text-xs px-3 py-1.5 text-red-600 hover:bg-red-50">
                    إلغاء الدعوة
                  </button>
                </template>

                <template v-if="employment.status === 'accepted'">
                  <div class="w-full p-3 rounded-xl bg-emerald-50 border border-emerald-200 mb-2">
                    <p class="text-sm font-semibold text-emerald-800 mb-1">تم قبول الدعوة</p>
                  </div>
                  <Link :href="`/school/employee-invitations/${employment.id}/interview`" class="btn-primary text-xs px-4 py-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                    إجراء مقابلة
                  </Link>
                  <button @click="hireEmployee(employment.id)" class="px-4 py-2 rounded-xl bg-emerald-100 text-emerald-700 font-semibold hover:bg-emerald-200 transition-all text-xs">
                    توظيف
                  </button>
                </template>

                <template v-if="employment.status === 'interviewed' && employment.interview">
                  <div class="w-full p-3 rounded-xl bg-blue-50 border border-blue-200 mb-2">
                    <div class="flex items-center justify-between">
                      <p class="text-sm font-semibold text-blue-800">نتيجة المقابلة</p>
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
                    :href="`/school/employee-interviews/${employment.interview.id}`"
                    class="px-4 py-2 rounded-xl bg-blue-100 text-blue-700 font-semibold hover:bg-blue-200 transition-all text-xs"
                  >
                    عرض التفاصيل
                  </Link>
                  <button @click="hireEmployee(employment.id)" class="px-4 py-2 rounded-xl bg-emerald-100 text-emerald-700 font-semibold hover:bg-emerald-200 transition-all text-xs">
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

const hireEmployee = (id) => {
  useForm({}).patch(`/school/employee-invitations/${id}/hire`, {
    preserveScroll: true,
    onSuccess: () => location.reload(),
  })
}

const rejectInvitation = (id) => {
  useForm({}).patch(`/school/employee-invitations/${id}/reject`, {
    preserveScroll: true,
    onSuccess: () => location.reload(),
  })
}
</script>
