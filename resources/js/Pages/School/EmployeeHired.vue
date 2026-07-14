<template>
  <DashboardLayout>
    <div>
      <div class="flex items-center justify-between mb-8 animate-fade-in-up">
        <div>
          <h1 class="text-3xl font-extrabold text-slate-900">الموظفون المعينون</h1>
          <p class="text-slate-500 mt-1">قائمة الموظفين الذين تم توظيفهم في المدرسة</p>
        </div>
        <div class="px-5 py-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-center">
          <div class="text-3xl font-extrabold text-emerald-700">{{ employments.length }}</div>
          <p class="text-xs font-semibold text-emerald-600">موظف</p>
        </div>
      </div>

      <div v-if="employments.length === 0" class="card p-10 text-center animate-fade-in-up">
        <div class="w-16 h-16 rounded-2xl bg-surface-100 flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
        </div>
        <h3 class="text-lg font-bold text-slate-900 mb-1">لا يوجد موظفون معينون</h3>
        <p class="text-sm text-slate-500">قم بتصفح الموظفين وإرسال دعوات التوظيف</p>
      </div>

      <div v-else class="space-y-4">
        <div
          v-for="employment in employments"
          :key="employment.id"
          class="card p-5 animate-fade-in-up"
        >
          <div class="flex items-start gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-100 to-surface-100 overflow-hidden flex-shrink-0 shadow-sm">
              <div class="w-full h-full flex items-center justify-center text-xl font-bold text-primary-600">
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
                </div>
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 whitespace-nowrap">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                  معين
                </span>
              </div>

              <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                <div class="p-2 rounded-xl bg-surface-50">
                  <p class="text-xs text-slate-500 font-semibold">تاريخ التعيين</p>
                  <p class="text-sm font-bold text-slate-900">{{ formatDate(employment.hired_at) }}</p>
                </div>
                <div class="p-2 rounded-xl bg-surface-50">
                  <p class="text-xs text-slate-500 font-semibold">البريد</p>
                  <p class="text-sm font-bold text-slate-900 truncate">{{ employment.employee?.email || '-' }}</p>
                </div>
                <div class="p-2 rounded-xl bg-surface-50">
                  <p class="text-xs text-slate-500 font-semibold">الفروع</p>
                  <div class="flex flex-wrap gap-1 mt-0.5">
                    <span v-for="branch in employment.employee?.branches" :key="branch.id" class="text-xs text-slate-600">{{ branch.name }}</span>
                  </div>
                </div>
              </div>

              <div class="mt-3 flex flex-wrap gap-2">
                <button
                  @click="confirmEndEmployment(employment)"
                  class="btn-ghost text-xs px-4 py-2 text-red-600 hover:bg-red-50 hover:text-red-700"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                  إنهاء التوظيف
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const props = defineProps({
  employments: Array,
})

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('ar-EG', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const confirmEndEmployment = (employment) => {
  Swal.fire({
    title: 'إنهاء التوظيف',
    text: `هل أنت متأكد من إنهاء توظيف "${employment.employee?.name}"؟`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#64748b',
    confirmButtonText: 'نعم، إنهاء التوظيف',
    cancelButtonText: 'إلغاء',
    customClass: {
      popup: 'rounded-2xl',
      confirmButton: 'px-5 py-2.5 text-sm font-medium text-white rounded-xl',
      cancelButton: 'px-5 py-2.5 text-sm font-medium rounded-xl',
    },
  }).then((result) => {
    if (result.isConfirmed) {
      router.patch(`/school/employee-invitations/${employment.id}/end`, {}, {
        preserveScroll: true,
        onSuccess: () => {
          Swal.fire({
            title: 'تم',
            text: 'تم إنهاء توظيف الموظف بنجاح',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false,
            customClass: { popup: 'rounded-2xl' },
          })
        },
      })
    }
  })
}
</script>
