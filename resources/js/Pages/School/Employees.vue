<template>
  <DashboardLayout>
    <div>
      <div class="flex items-center justify-between mb-8 animate-fade-in-up">
        <div>
          <h1 class="text-3xl font-extrabold text-slate-900">المدرسون المعينون</h1>
          <p class="text-slate-500 mt-1">قائمة المدرسين الذين تم توظيفهم في المدرسة</p>
        </div>
        <div class="px-5 py-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-center">
          <div class="text-3xl font-extrabold text-emerald-700">{{ employments.length }}</div>
          <p class="text-xs font-semibold text-emerald-600">معلم</p>
        </div>
      </div>

      <div v-if="employments.length === 0" class="card p-10 text-center animate-fade-in-up">
        <div class="w-16 h-16 rounded-2xl bg-surface-100 flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
        </div>
        <h3 class="text-lg font-bold text-slate-900 mb-1">لا يوجد مدرسون معينون</h3>
        <p class="text-sm text-slate-500">قم بتصفح المدرسين وإرسال دعوات التوظيف</p>
      </div>

      <div v-else class="space-y-4">
        <div
          v-for="employment in employments"
          :key="employment.id"
          class="card p-5 animate-fade-in-up"
        >
          <div class="flex items-start gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-100 to-surface-100 overflow-hidden flex-shrink-0 shadow-sm">
              <img v-if="employment.teacher?.photo" :src="`/storage/${employment.teacher.photo}`" :alt="employment.teacher?.user?.name" class="w-full h-full object-cover" />
              <div v-else class="w-full h-full flex items-center justify-center text-xl font-bold text-primary-600">
                {{ employment.teacher?.user?.name?.charAt(0) }}
              </div>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-start justify-between gap-4">
                <div>
                  <h3 class="text-lg font-bold text-slate-900">{{ employment.teacher?.user?.name }}</h3>
                  <span class="badge-primary text-xs">{{ employment.subject?.name }}</span>
                  <span class="mr-2 text-sm text-slate-500">{{ employment.teacher?.qualification }}</span>
                </div>
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 whitespace-nowrap">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                  معين
                </span>
              </div>

              <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                <div class="p-2 rounded-xl bg-surface-50">
                  <p class="text-xs text-slate-500 font-semibold">تاريخ التعيين</p>
                  <p class="text-sm font-bold text-slate-900">{{ formatDate(employment.hired_at) }}</p>
                </div>
                <div class="p-2 rounded-xl bg-surface-50">
                  <p class="text-xs text-slate-500 font-semibold">الهاتف</p>
                  <p class="text-sm font-bold text-slate-900" dir="ltr">{{ employment.teacher?.phone || '-' }}</p>
                </div>
                <div class="p-2 rounded-xl bg-surface-50">
                  <p class="text-xs text-slate-500 font-semibold">البريد</p>
                  <p class="text-sm font-bold text-slate-900 truncate">{{ employment.teacher?.user?.email || '-' }}</p>
                </div>
                <div class="p-2 rounded-xl bg-surface-50">
                  <p class="text-xs text-slate-500 font-semibold">سنوات الخبرة</p>
                  <p class="text-sm font-bold text-slate-900">{{ employment.teacher?.experience_years }} سنة</p>
                </div>
              </div>

              <div class="mt-3 flex flex-wrap gap-2">
                <a :href="`tel:${employment.teacher?.phone}`" class="btn-primary text-xs px-4 py-2">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                  </svg>
                  اتصال
                </a>
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
  const d = new Date(date)
  const pad = (n) => String(n).padStart(2, '0')
  return `${d.getFullYear()}/${pad(d.getMonth() + 1)}/${pad(d.getDate())}`
}

const confirmEndEmployment = (employment) => {
  Swal.fire({
    title: 'إنهاء التوظيف',
    text: `هل أنت متأكد من إنهاء توظيف "${employment.teacher?.user?.name}"؟`,
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
      router.patch(`/school/employments/${employment.id}/end`, {}, {
        preserveScroll: true,
        onSuccess: () => {
          Swal.fire({
            title: 'تم',
            text: 'تم إنهاء توظيف المدرس بنجاح',
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
