<template>
  <DashboardLayout>
    <div>
      <div class="relative mb-8 overflow-hidden rounded-3xl bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 shadow-2xl shadow-primary-500/20 animate-fade-in-up">
        <div class="absolute inset-0 bg-grid opacity-20"></div>
        <div class="relative px-8 py-10">
          <div class="flex items-center gap-4 mb-3">
            <div class="w-14 h-14 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center text-3xl shadow-lg">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
              </svg>
            </div>
            <div>
              <h1 class="text-2xl font-bold text-white">طلبات المشتريات</h1>
              <p class="text-white/70 text-sm">جميع طلبات الشراء</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Balance Card -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6 animate-fade-in-up animate-delay-50">
        <div class="card p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-slate-500 mb-1">رصيدي الحالي</p>
              <p class="text-3xl font-bold" :class="balance > 0 ? 'text-emerald-600' : 'text-slate-400'">
                {{ Number(balance).toLocaleString('ar') }} <span class="text-sm font-normal text-slate-500">ريال</span>
              </p>
            </div>
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center" :class="balance > 0 ? 'bg-emerald-100' : 'bg-surface-100'">
              <svg class="w-7 h-7" :class="balance > 0 ? 'text-emerald-500' : 'text-surface-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
        </div>

        <div class="card p-6">
          <div class="flex items-center justify-between mb-3">
            <p class="text-sm text-slate-500">آخر حركة</p>
            <div class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
          <template v-if="transactions.length > 0">
            <p class="text-lg font-bold text-slate-900">+{{ Number(transactions[0].amount).toLocaleString('ar') }} ريال</p>
            <p class="text-xs text-slate-500 mt-1">{{ formatDateFull(transactions[0].created_at) }}</p>
          </template>
          <template v-else>
            <p class="text-sm text-slate-400">لا توجد حركات بعد</p>
          </template>
        </div>
      </div>

      <!-- Transactions History -->
      <div v-if="transactions.length > 0" class="card mb-6 animate-fade-in-up animate-delay-100">
        <div class="p-4 border-b border-surface-200">
          <h3 class="text-sm font-bold text-slate-900">الحركات المالية</h3>
        </div>
        <div class="divide-y divide-surface-100 max-h-64 overflow-y-auto">
          <div
            v-for="tx in transactions"
            :key="tx.id"
            class="flex items-center justify-between px-4 py-3 hover:bg-surface-50 transition-colors"
          >
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
              </div>
              <div>
                <p class="text-sm font-bold text-slate-900">{{ tx.description || 'إضافة رصيد' }}</p>
                <p class="text-xs text-slate-500">بواسطة {{ tx.added_by?.name }} · {{ formatDateFull(tx.created_at) }}</p>
              </div>
            </div>
            <div class="text-left">
              <p class="text-sm font-bold text-emerald-600">+{{ Number(tx.amount).toLocaleString('ar') }} ريال</p>
              <p class="text-[10px] text-slate-400">الرصيد: {{ Number(tx.balance_after).toLocaleString('ar') }}</p>
            </div>
          </div>
        </div>
      </div>

      <div v-if="canManage" class="flex justify-end mb-4 animate-fade-in-up animate-delay-50">
        <Link href="/admin/purchases" class="btn-primary text-sm px-4 py-2 rounded-xl">
          <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          إدارة المشتريات
        </Link>
      </div>

      <div class="space-y-4 animate-fade-in-up animate-delay-100">
        <div
          v-for="purchase in purchases"
          :key="purchase.id"
          class="card p-6"
        >
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
              </div>
              <div>
                <h3 class="text-lg font-bold text-slate-900">{{ purchase.item_name }}</h3>
                <p class="text-xs text-slate-500">{{ formatDate(purchase.created_at) }}</p>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <span
                class="inline-flex px-2.5 py-0.5 rounded-lg text-xs font-bold"
                :class="{
                  'bg-amber-100 text-amber-700': purchase.status === 'pending',
                  'bg-blue-100 text-blue-700': purchase.status === 'completed',
                  'bg-emerald-100 text-emerald-700': purchase.status === 'approved',
                  'bg-red-100 text-red-700': purchase.status === 'rejected',
                }"
              >
                {{ { pending: 'قيد الانتظار', completed: 'بانتظار المراجعة', approved: 'تمت الموافقة', rejected: 'مرفوض' }[purchase.status] }}
              </span>
              <button
                v-if="purchase.status === 'pending' && isAssigned(purchase)"
                @click="openCompleteModal(purchase)"
                class="btn-primary text-xs px-3 py-1.5 rounded-lg"
              >
                إتمام الشراء
              </button>
              <Link
                v-if="canManage"
                :href="`/admin/purchases/${purchase.id}`"
                class="p-2 rounded-lg text-slate-400 hover:text-primary-600 hover:bg-primary-50 transition-all duration-200"
                title="عرض التفاصيل"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
              </Link>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="py-3 px-4 bg-surface-50 rounded-xl">
              <p class="text-xs text-slate-500 mb-1">المبلغ</p>
              <p class="text-lg font-bold text-slate-900">{{ purchase.amount }} ريال</p>
            </div>
            <div class="py-3 px-4 bg-surface-50 rounded-xl">
              <p class="text-xs text-slate-500 mb-1">بواسطة</p>
              <p class="text-sm font-bold text-slate-900">{{ purchase.creator?.name }}</p>
            </div>
            <div class="py-3 px-4 bg-surface-50 rounded-xl">
              <p class="text-xs text-slate-500 mb-1">الموظفون</p>
              <div class="flex flex-wrap gap-1">
                <span
                  v-for="emp in purchase.employees"
                  :key="emp.id"
                  class="inline-flex px-2 py-0.5 rounded-lg bg-surface-100 text-slate-700 text-xs font-semibold"
                >
                  {{ emp.name }}
                </span>
              </div>
            </div>
          </div>

          <div v-if="purchase.notes" class="mt-4 pt-4 border-t border-surface-200">
            <p class="text-xs text-slate-500 mb-1">ملاحظات</p>
            <p class="text-sm text-slate-700">{{ purchase.notes }}</p>
          </div>

          <div v-if="purchase.status === 'completed'" class="mt-4 pt-4 border-t border-surface-200">
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 mb-4">
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-xs font-bold text-blue-700">بانتظار مراجعة الإدارة</p>
              </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <div class="py-2 px-3 bg-blue-50 rounded-xl">
                <p class="text-xs text-slate-500 mb-1">المبلغ الفعلي</p>
                <p class="text-sm font-bold text-blue-700">{{ purchase.actual_amount }} ريال</p>
              </div>
              <div class="py-2 px-3 bg-surface-50 rounded-xl">
                <p class="text-xs text-slate-500 mb-1">المبلغ المتبقي</p>
                <p class="text-sm font-bold" :class="purchase.remaining_amount > 0 ? 'text-amber-600' : 'text-emerald-600'">
                  {{ purchase.remaining_amount }} ريال
                </p>
              </div>
              <div v-if="purchase.invoice_image" class="py-2 px-3 bg-surface-50 rounded-xl flex items-center">
                <a :href="`/storage/${purchase.invoice_image}`" target="_blank" class="text-sm font-bold text-primary-600 hover:text-primary-800 flex items-center gap-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  عرض الفاتورة
                </a>
              </div>
            </div>
          </div>

          <div v-if="purchase.status === 'approved'" class="mt-4 pt-4 border-t border-surface-200">
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-3 mb-4">
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <p class="text-xs font-bold text-emerald-700">تمت الموافقة وخصم المبلغ من رصيدك</p>
              </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <div class="py-2 px-3 bg-emerald-50 rounded-xl">
                <p class="text-xs text-slate-500 mb-1">المبلغ الفعلي</p>
                <p class="text-sm font-bold text-emerald-700">{{ purchase.actual_amount }} ريال</p>
              </div>
              <div class="py-2 px-3 bg-surface-50 rounded-xl">
                <p class="text-xs text-slate-500 mb-1">المبلغ المتبقي</p>
                <p class="text-sm font-bold" :class="purchase.remaining_amount > 0 ? 'text-amber-600' : 'text-emerald-600'">
                  {{ purchase.remaining_amount }} ريال
                </p>
              </div>
              <div v-if="purchase.invoice_image" class="py-2 px-3 bg-surface-50 rounded-xl flex items-center">
                <a :href="`/storage/${purchase.invoice_image}`" target="_blank" class="text-sm font-bold text-primary-600 hover:text-primary-800 flex items-center gap-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  عرض الفاتورة
                </a>
              </div>
            </div>
          </div>

          <div v-if="purchase.status === 'rejected'" class="mt-4 pt-4 border-t border-surface-200">
            <div class="bg-red-50 border border-red-200 rounded-xl p-3 mb-4">
              <div class="flex items-center gap-2 mb-2">
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <p class="text-xs font-bold text-red-700">تم رفض الطلب من الإدارة</p>
              </div>
              <div v-if="purchase.rejection_reason" class="bg-white rounded-xl p-2 border border-red-100">
                <p class="text-xs text-slate-500">سبب الرفض: <span class="font-bold text-red-700">{{ purchase.rejection_reason }}</span></p>
              </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <div class="py-2 px-3 bg-surface-50 rounded-xl">
                <p class="text-xs text-slate-500 mb-1">المبلغ الفعلي</p>
                <p class="text-sm font-bold text-slate-700">{{ purchase.actual_amount }} ريال</p>
              </div>
              <div class="py-2 px-3 bg-surface-50 rounded-xl">
                <p class="text-xs text-slate-500 mb-1">المبلغ المتبقي</p>
                <p class="text-sm font-bold" :class="purchase.remaining_amount > 0 ? 'text-amber-600' : 'text-emerald-600'">
                  {{ purchase.remaining_amount }} ريال
                </p>
              </div>
              <div v-if="purchase.invoice_image" class="py-2 px-3 bg-surface-50 rounded-xl flex items-center">
                <a :href="`/storage/${purchase.invoice_image}`" target="_blank" class="text-sm font-bold text-primary-600 hover:text-primary-800 flex items-center gap-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  عرض الفاتورة
                </a>
              </div>
            </div>
          </div>
        </div>

        <div v-if="purchases.length === 0" class="card p-10 text-center">
          <div class="w-16 h-16 rounded-2xl bg-surface-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
            </svg>
          </div>
          <h3 class="text-lg font-bold text-slate-900 mb-1">لا توجد طلبات شراء</h3>
          <p class="text-sm text-slate-500">لم يتم إضافة أي طلبات شراء بعد</p>
        </div>
      </div>

      <!-- Complete Purchase Modal -->
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" @click.self="closeModal">
        <div class="bg-white rounded-3xl w-full max-w-lg mx-4 p-8 shadow-2xl animate-fade-in-up">
          <h3 class="text-xl font-extrabold text-slate-900 mb-6">إتمام طلب الشراء</h3>

          <form @submit.prevent="submitComplete">
            <div class="mb-5">
              <label class="block text-sm font-bold text-slate-700 mb-1.5">المبلغ الفعلي <span class="text-red-500">*</span></label>
              <div class="relative">
                <input
                  v-model="completeForm.actual_amount"
                  type="number"
                  step="0.01"
                  min="0"
                  class="input-base pl-16"
                  placeholder="0.00"
                />
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400">ريال</span>
              </div>
              <p v-if="completeForm.errors.actual_amount" class="mt-1 text-xs text-red-500 font-semibold">{{ completeForm.errors.actual_amount }}</p>
            </div>

            <div v-if="selectedPurchase?.requires_invoice" class="mb-5">
              <label class="block text-sm font-bold text-slate-700 mb-1.5">صورة الفاتورة <span class="text-red-500">*</span></label>
              <input
                type="file"
                accept="image/jpeg,image/png"
                @input="completeForm.invoice_image = $event.target.files[0]"
                class="input-base file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
              />
              <p class="mt-1 text-xs text-slate-500">jpg, png - حد أقصى 2 ميجابايت</p>
              <p v-if="completeForm.errors.invoice_image" class="mt-1 text-xs text-red-500 font-semibold">{{ completeForm.errors.invoice_image }}</p>
            </div>

            <div v-else-if="selectedPurchase" class="mb-5">
              <label class="block text-sm font-bold text-slate-700 mb-1.5">صورة الفاتورة (اختياري)</label>
              <input
                type="file"
                accept="image/jpeg,image/png"
                @input="completeForm.invoice_image = $event.target.files[0]"
                class="input-base file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
              />
              <p class="mt-1 text-xs text-slate-500">jpg, png - حد أقصى 2 ميجابايت</p>
            </div>

            <div class="flex items-center gap-3">
              <button type="submit" :disabled="completeForm.processing" class="btn-primary flex-1">
                <svg v-if="completeForm.processing" class="w-4 h-4 animate-spin inline ml-1" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                </svg>
                {{ completeForm.processing ? 'جاري الحفظ...' : 'تأكيد الإتمام' }}
              </button>
              <button type="button" @click="closeModal" class="btn-ghost flex-1">إلغاء</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router, useForm, usePage } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const props = defineProps({
  purchases: Array,
  canManage: Boolean,
  balance: Number,
  transactions: Array,
})

const user = usePage().props.auth.user

const showModal = ref(false)
const selectedPurchase = ref(null)

const completeForm = useForm({
  actual_amount: '',
  invoice_image: null,
})

function isAssigned(purchase) {
  return purchase.employees?.some(emp => emp.id === user?.id)
}

function openCompleteModal(purchase) {
  selectedPurchase.value = purchase
  completeForm.actual_amount = purchase.amount
  completeForm.invoice_image = null
  completeForm.errors = {}
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  selectedPurchase.value = null
  completeForm.reset()
  completeForm.clearErrors()
}

function submitComplete() {
  if (!selectedPurchase.value) return

  const formData = new FormData()
  formData.append('actual_amount', completeForm.actual_amount)
  if (completeForm.invoice_image) {
    formData.append('invoice_image', completeForm.invoice_image)
  }

  router.post(`/employee/purchases/${selectedPurchase.value.id}/complete`, formData, {
    forceFormData: true,
    onSuccess: () => closeModal(),
  })
}

function formatDate(date) {
  if (!date) return '-'
  const d = new Date(date)
  const day = String(d.getDate()).padStart(2, '0')
  const month = String(d.getMonth() + 1).padStart(2, '0')
  const year = d.getFullYear()
  return `${day}-${month}-${year}`
}

function formatDateFull(date) {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('ar', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}
</script>
