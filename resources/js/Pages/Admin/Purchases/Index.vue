<template>
  <DashboardLayout>
    <div>
      <div class="relative mb-8 overflow-hidden rounded-3xl bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 shadow-2xl shadow-primary-500/20 animate-fade-in-up">
        <div class="absolute inset-0 bg-grid opacity-20"></div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-primary-400/20 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
        <div class="relative px-8 py-10">
          <div class="flex items-center gap-4 mb-3">
            <div class="w-14 h-14 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center text-3xl shadow-lg">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
              </svg>
            </div>
            <div>
              <h1 class="text-2xl font-bold text-white">المشتريات</h1>
              <p class="text-white/70 text-sm">إدارة طلبات الشراء</p>
            </div>
          </div>
        </div>
      </div>

      <div class="card animate-fade-in-up animate-delay-100">
        <div class="p-6 border-b border-surface-200 flex items-center justify-between">
          <div class="flex items-center gap-2">
            <span class="text-sm font-semibold text-slate-500">{{ purchases.length }} طلب</span>
          </div>
          <TableSearch v-model="search" />
          <Link href="/admin/purchases/create" class="btn-primary text-sm px-4 py-2 rounded-xl">
            <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            طلب شراء جديد
          </Link>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-surface-50 border-b border-surface-200">
                <th class="px-4 py-3 text-right text-sm font-bold text-slate-700">#</th>
                <th class="px-4 py-3 text-right text-sm font-bold text-slate-700">المنتج</th>
                <th class="px-4 py-3 text-right text-sm font-bold text-slate-700">المبلغ</th>
                <th class="px-4 py-3 text-right text-sm font-bold text-slate-700">الموظفون</th>
                <th class="px-4 py-3 text-center text-sm font-bold text-slate-700">فاتورة</th>
                <th class="px-4 py-3 text-center text-sm font-bold text-slate-700">الحالة</th>
                <th class="px-4 py-3 text-center text-sm font-bold text-slate-700"></th>
              </tr>
            </thead>
            <tbody class="divide-y divide-surface-100">
              <tr
                v-for="(purchase, index) in filteredList"
                :key="purchase.id"
                class="hover:bg-primary-50/50 transition-colors duration-150"
              >
                <td class="px-4 py-3 text-sm text-slate-500">{{ index + 1 }}</td>
                <td class="px-4 py-3 text-sm font-bold text-slate-900">{{ purchase.item_name }}</td>
                <td class="px-4 py-3 text-sm font-bold text-slate-900">{{ purchase.amount }} ريال</td>
                <td class="px-4 py-3">
                  <div class="flex flex-wrap gap-1">
                    <span
                      v-for="emp in purchase.employees"
                      :key="emp.id"
                      class="inline-flex px-2 py-0.5 rounded-lg bg-surface-100 text-slate-700 text-xs font-semibold"
                    >
                      {{ emp.name }}
                    </span>
                  </div>
                </td>
                <td class="px-4 py-3 text-center">
                  <span
                    class="inline-flex px-2.5 py-0.5 rounded-lg text-xs font-bold"
                    :class="purchase.requires_invoice ? 'bg-amber-100 text-amber-700' : 'bg-surface-100 text-slate-500'"
                  >
                    {{ purchase.requires_invoice ? 'مطلوبة' : 'اختياري' }}
                  </span>
                </td>
                <td class="px-4 py-3 text-center">
                  <span
                    class="inline-flex px-2.5 py-0.5 rounded-lg text-xs font-bold"
                    :class="purchase.status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'"
                  >
                    {{ purchase.status === 'completed' ? 'مكتمل' : 'قيد الانتظار' }}
                  </span>
                </td>
                <td class="px-4 py-3 text-center">
                  <Link :href="`/admin/purchases/${purchase.id}`" class="p-2 rounded-lg text-slate-400 hover:text-primary-600 hover:bg-primary-50 transition-all duration-200 inline-block" title="عرض التفاصيل">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="filteredList.length === 0" class="p-10 text-center">
          <div class="w-16 h-16 rounded-2xl bg-surface-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
            </svg>
          </div>
          <h3 class="text-lg font-bold text-slate-900 mb-1">لا توجد طلبات شراء</h3>
          <p class="text-sm text-slate-500">قم بإضافة طلب شراء جديد</p>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import TableSearch from '@/Components/TableSearch.vue'

const props = defineProps({
  purchases: Array,
})

const search = ref('')

const matchesSearch = (item, term) => {
  if (!term) return true
  const t = term.toLowerCase()
  return Object.values(item).some(val => {
    if (val == null) return false
    if (typeof val === 'string') return val.toLowerCase().includes(t)
    if (typeof val === 'number') return String(val).includes(t)
    if (typeof val === 'object') {
      if (Array.isArray(val)) return val.some(v => typeof v === 'object' ? matchesSearch(v, term) : String(v).toLowerCase().includes(t))
      return matchesSearch(val, term)
    }
    return false
  })
}

const filteredList = computed(() => props.purchases.filter(item => matchesSearch(item, search.value)))
</script>
