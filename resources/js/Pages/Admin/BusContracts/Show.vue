<template>
  <DashboardLayout>
    <div>
      <button @click="goBack" class="btn-ghost mb-6 animate-fade-in-up">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        العودة
      </button>

      <div class="relative mb-8 overflow-hidden rounded-3xl bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 shadow-2xl shadow-primary-500/20 animate-fade-in-up">
        <div class="absolute inset-0 bg-grid opacity-20"></div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-primary-400/20 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
        <div class="relative px-8 py-10">
          <div class="flex items-center gap-4 mb-3">
            <div class="w-14 h-14 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center text-3xl shadow-lg">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
              </svg>
            </div>
            <div>
              <h1 class="text-2xl font-bold text-white">تفاصيل العقد</h1>
              <p class="text-white/70 text-sm">{{ contract.owner_name }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6 animate-fade-in-up animate-delay-100">
        <div class="card p-6">
          <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">معلومات المالك</h3>
          <div class="space-y-4">
            <div class="flex items-center justify-between py-3 px-4 bg-surface-50 rounded-xl">
              <span class="text-sm text-slate-500">الاسم</span>
              <span class="text-sm font-bold text-slate-900">{{ contract.owner_name }}</span>
            </div>
            <div class="flex items-center justify-between py-3 px-4 bg-surface-50 rounded-xl">
              <span class="text-sm text-slate-500">الهاتف</span>
              <span class="text-sm font-bold text-slate-900" dir="ltr">{{ contract.owner_phone || '-' }}</span>
            </div>
            <div class="flex items-center justify-between py-3 px-4 bg-surface-50 rounded-xl">
              <span class="text-sm text-slate-500">السائق</span>
              <span class="text-sm font-bold text-slate-900">{{ contract.same_driver ? 'نفس المالك' : contract.driver_name || '-' }}</span>
            </div>
            <div v-if="!contract.same_driver" class="flex items-center justify-between py-3 px-4 bg-surface-50 rounded-xl">
              <span class="text-sm text-slate-500">هاتف السائق</span>
              <span class="text-sm font-bold text-slate-900" dir="ltr">{{ contract.driver_phone || '-' }}</span>
            </div>
          </div>
        </div>

        <div class="card p-6">
          <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">معلومات المركبة</h3>
          <div class="grid grid-cols-2 gap-4">
            <div class="py-3 px-4 bg-surface-50 rounded-xl">
              <p class="text-xs text-slate-500 mb-1">الرخصة</p>
              <p class="text-sm font-bold text-slate-900">{{ contract.license }}</p>
            </div>
            <div class="py-3 px-4 bg-surface-50 rounded-xl">
              <p class="text-xs text-slate-500 mb-1">اللون</p>
              <p class="text-sm font-bold text-slate-900">{{ contract.van_color }}</p>
            </div>
            <div class="py-3 px-4 bg-surface-50 rounded-xl">
              <p class="text-xs text-slate-500 mb-1">عدد الركاب</p>
              <p class="text-sm font-bold text-slate-900">{{ contract.seats_count }}</p>
            </div>
            <div class="py-3 px-4 bg-surface-50 rounded-xl">
              <p class="text-xs text-slate-500 mb-1">الموقع</p>
              <p class="text-sm font-bold text-slate-900">{{ contract.location }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="card p-6 animate-fade-in-up animate-delay-200">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">المرفقات</h3>
        <div v-if="contract.attachments && contract.attachments.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          <a
            v-for="att in contract.attachments"
            :key="att.id"
            :href="`/storage/${att.pivot.file_path}`"
            target="_blank"
            class="flex items-center gap-3 p-4 bg-surface-50 rounded-xl hover:bg-primary-50 transition-all duration-200 group border border-surface-200"
          >
            <div class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center group-hover:scale-110 transition-transform">
              <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
            <div class="min-w-0">
              <p class="text-sm font-bold text-slate-900 truncate">{{ att.name }}</p>
              <p class="text-xs text-slate-500">عرض المرفق</p>
            </div>
          </a>
        </div>
        <div v-else class="py-6 text-center">
          <div class="w-14 h-14 rounded-2xl bg-surface-100 flex items-center justify-center mx-auto mb-3">
            <svg class="w-7 h-7 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
            </svg>
          </div>
          <h3 class="text-lg font-bold text-slate-900 mb-1">لا توجد مرفقات</h3>
          <p class="text-sm text-slate-500">لم يتم إرفاق أي ملفات لهذا العقد</p>
        </div>
      </div>

      <div class="flex items-center justify-between mt-6 animate-fade-in-up animate-delay-300">
        <div class="text-sm text-slate-500">
          تاريخ التسجيل: {{ formatDate(contract.created_at) }}
        </div>
        <button @click="deleteContract" class="btn-danger px-5 py-2.5">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
          حذف العقد
        </button>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import Swal from 'sweetalert2'

const props = defineProps({
  contract: Object,
})

function formatDate(date) {
  if (!date) return '-'
  const d = new Date(date)
  const day = String(d.getDate()).padStart(2, '0')
  const month = String(d.getMonth() + 1).padStart(2, '0')
  const year = d.getFullYear()
  return `${day}-${month}-${year}`
}

function goBack() {
  window.history.back()
}

function deleteContract() {
  Swal.fire({
    title: 'تأكيد الحذف',
    text: `هل أنت متأكد من حذف عقد ${props.contract.owner_name}؟`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#64748b',
    confirmButtonText: 'نعم، حذف',
    cancelButtonText: 'إلغاء',
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(`/admin/bus-contracts/${props.contract.id}`, {
        preserveScroll: true,
      })
    }
  })
}
</script>
