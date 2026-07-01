<template>
  <DashboardLayout>
    <div>
      <!-- Header -->
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
              <h1 class="text-2xl font-bold text-white">عقود الباصات</h1>
              <p class="text-white/70 text-sm">إدارة عقود الباصات المسجلة في النظام</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Table Card -->
      <div class="card animate-fade-in-up animate-delay-100">
        <div class="p-6 border-b border-surface-200 flex items-center justify-between">
          <div class="flex items-center gap-2">
            <span class="text-sm font-semibold text-slate-500">{{ contracts.length }} عقد</span>
          </div>
  
        </div>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-surface-50 border-b border-surface-200">
                <th class="px-4 py-3 text-right text-sm font-bold text-slate-700">#</th>
                <th class="px-4 py-3 text-right text-sm font-bold text-slate-700">المالك</th>
                <th class="px-4 py-3 text-right text-sm font-bold text-slate-700">الرخصة</th>
                <th class="px-4 py-3 text-center text-sm font-bold text-slate-700">اللون</th>
                <th class="px-4 py-3 text-center text-sm font-bold text-slate-700">الركاب</th>
                <th class="px-4 py-3 text-right text-sm font-bold text-slate-700">الموقع</th>
                <th class="px-4 py-3 text-center text-sm font-bold text-slate-700"></th>
              </tr>
            </thead>
            <tbody class="divide-y divide-surface-100">
              <tr
                v-for="(contract, index) in contracts"
                :key="contract.id"
                class="hover:bg-primary-50/50 transition-colors duration-150"
              >
                <td class="px-4 py-3 text-sm text-slate-500">{{ index + 1 }}</td>
                <td class="px-4 py-3 text-sm font-bold text-slate-900">{{ contract.owner_name }}</td>
                <td class="px-4 py-3 text-sm text-slate-700">{{ contract.license }}</td>
                <td class="px-4 py-3 text-sm text-center">
                  <div class="flex items-center justify-center gap-2">
                    <span class="text-sm text-slate-700">{{ contract.van_color }}</span>
                  </div>
                </td>
                <td class="px-4 py-3 text-sm font-bold text-slate-900 text-center">{{ contract.seats_count }}</td>
                <td class="px-4 py-3 text-sm text-slate-700">{{ contract.location }}</td>
                <td class="px-4 py-3 text-center">
                  <div class="flex items-center justify-center gap-1">
                    <Link :href="`/admin/bus-contracts/${contract.id}`" class="p-2 rounded-lg text-slate-400 hover:text-primary-600 hover:bg-primary-50 transition-all duration-200" title="عرض التفاصيل">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                    </Link>
                    <button @click="deleteContract(contract)" class="p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-all duration-200" title="حذف">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-if="contracts.length === 0" class="p-10 text-center">
          <div class="w-16 h-16 rounded-2xl bg-surface-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
            </svg>
          </div>
          <h3 class="text-lg font-bold text-slate-900 mb-1">لا توجد عقود باصات</h3>
          <p class="text-sm text-slate-500">لم يتم إضافة أي عقود باصات بعد</p>
        </div>
      </div>


    </div>
  </DashboardLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import Swal from 'sweetalert2'

const props = defineProps({
  contracts: Array,
})

function deleteContract(contract) {
  Swal.fire({
    title: 'تأكيد الحذف',
    text: `هل أنت متأكد من حذف عقد ${contract.owner_name}؟`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#64748b',
    confirmButtonText: 'نعم، حذف',
    cancelButtonText: 'إلغاء',
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(`/admin/bus-contracts/${contract.id}`, {
        preserveScroll: true,
      })
    }
  })
}
</script>
