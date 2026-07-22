<template>
  <DashboardLayout>
    <div>
      <Link href="/admin/balances" class="btn-ghost mb-6 animate-fade-in-up">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        العودة
      </Link>

      <div class="relative mb-8 overflow-hidden rounded-3xl bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 shadow-2xl shadow-primary-500/20 animate-fade-in-up">
        <div class="absolute inset-0 bg-grid opacity-20"></div>
        <div class="relative px-8 py-10">
          <div class="flex items-center gap-4 mb-3">
            <div class="w-14 h-14 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center text-3xl shadow-lg">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div>
              <h1 class="text-2xl font-bold text-white">حساب {{ user.name }}</h1>
              <p class="text-white/70 text-sm">{{ user.email }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6 animate-fade-in-up animate-delay-50">
        <div class="card p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-slate-500 mb-1">الرصيد الحالي</p>
              <p class="text-3xl font-bold" :class="currentBalance > 0 ? 'text-emerald-600' : 'text-slate-400'">
                {{ Number(currentBalance).toLocaleString('ar') }} <span class="text-sm font-normal text-slate-500">ريال</span>
              </p>
            </div>
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center" :class="currentBalance > 0 ? 'bg-emerald-100' : 'bg-surface-100'">
              <svg class="w-7 h-7" :class="currentBalance > 0 ? 'text-emerald-500' : 'text-surface-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
        </div>

        <div class="card p-6">
          <div class="flex items-center justify-between mb-3">
            <p class="text-sm text-slate-500">إجمالي الإيداعات</p>
            <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
            </div>
          </div>
          <p class="text-lg font-bold text-emerald-600">{{ Number(totalDeposits).toLocaleString('ar') }} ريال</p>
          <p class="text-xs text-slate-500 mt-1">{{ depositsCount }} إيداع</p>
        </div>

        <div class="card p-6">
          <div class="flex items-center justify-between mb-3">
            <p class="text-sm text-slate-500">إجمالي الخصومات</p>
            <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
              </svg>
            </div>
          </div>
          <p class="text-lg font-bold text-red-600">{{ Number(Math.abs(totalDeductions)).toLocaleString('ar') }} ريال</p>
          <p class="text-xs text-slate-500 mt-1">{{ deductionsCount }} خصم</p>
        </div>
      </div>

      <div class="card animate-fade-in-up animate-delay-100">
        <div class="p-6 border-b border-surface-200">
          <h3 class="text-sm font-bold text-slate-900">سجل الحركات المالية</h3>
        </div>
        <div class="divide-y divide-surface-100 max-h-[600px] overflow-y-auto">
          <div
            v-for="tx in transactions"
            :key="tx.id"
            class="flex items-center justify-between px-6 py-4 hover:bg-surface-50 transition-colors"
          >
            <div class="flex items-center gap-3">
              <div
                class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                :class="tx.amount >= 0 ? 'bg-emerald-100' : 'bg-red-100'"
              >
                <svg
                  class="w-5 h-5"
                  :class="tx.amount >= 0 ? 'text-emerald-600' : 'text-red-600'"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path v-if="tx.amount >= 0" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                  <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                </svg>
              </div>
              <div>
                <p class="text-sm font-bold text-slate-900">{{ tx.description || (tx.amount >= 0 ? 'إضافة رصيد' : 'خصم') }}</p>
                <p class="text-xs text-slate-500">بواسطة {{ tx.added_by?.name }} · {{ formatDate(tx.created_at) }}</p>
              </div>
            </div>
            <div class="text-left">
              <p class="text-sm font-bold" :class="tx.amount >= 0 ? 'text-emerald-600' : 'text-red-600'">
                {{ tx.amount >= 0 ? '+' : '' }}{{ Number(tx.amount).toLocaleString('ar') }} ريال
              </p>
              <p class="text-[10px] text-slate-400">الرصيد: {{ Number(tx.balance_after).toLocaleString('ar') }}</p>
            </div>
          </div>

          <div v-if="transactions.length === 0" class="p-10 text-center">
            <div class="w-16 h-16 rounded-2xl bg-surface-100 flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-1">لا توجد حركات مالية</h3>
            <p class="text-sm text-slate-500">لم تُسجل أي حركات مالية لهذا المستخدم بعد</p>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const props = defineProps({
  user: Object,
  balance: Object,
  transactions: Array,
})

const currentBalance = computed(() => props.balance ? Number(props.balance.balance) : 0)

const totalDeposits = computed(() => {
  return props.transactions
    .filter(tx => tx.amount >= 0)
    .reduce((sum, tx) => sum + Number(tx.amount), 0)
})

const depositsCount = computed(() => {
  return props.transactions.filter(tx => tx.amount >= 0).length
})

const totalDeductions = computed(() => {
  return props.transactions
    .filter(tx => tx.amount < 0)
    .reduce((sum, tx) => sum + Number(tx.amount), 0)
})

const deductionsCount = computed(() => {
  return props.transactions.filter(tx => tx.amount < 0).length
})

function formatDate(date) {
  if (!date) return '-'
  const d = new Date(date)
  const pad = (n) => String(n).padStart(2, '0')
  return `${d.getFullYear()}/${pad(d.getMonth() + 1)}/${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}`
}
</script>
