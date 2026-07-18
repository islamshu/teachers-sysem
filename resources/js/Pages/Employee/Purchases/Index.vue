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
              <h1 class="text-2xl font-bold text-white">طلبات الشراء</h1>
              <p class="text-white/70 text-sm">طلبات الشراء المكلف بها</p>
            </div>
          </div>
        </div>
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
                <h3 class="text-lg font-bold text-slate-900">طلب شراء #{{ purchase.id }}</h3>
                <p class="text-xs text-slate-500">{{ formatDate(purchase.created_at) }}</p>
              </div>
            </div>
            <span
              class="inline-flex px-2.5 py-0.5 rounded-lg text-xs font-bold"
              :class="statusClass(purchase.status)"
            >
              {{ statusText(purchase.status) }}
            </span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
            <div class="py-3 px-4 bg-surface-50 rounded-xl">
              <p class="text-xs text-slate-500 mb-1">المنتج</p>
              <p class="text-lg font-bold text-slate-900">{{ purchase.item_name }}</p>
            </div>
            <div class="py-3 px-4 bg-surface-50 rounded-xl">
              <p class="text-xs text-slate-500 mb-1">المبلغ المدفوع</p>
              <p class="text-lg font-bold text-slate-900">{{ purchase.amount }} ريال</p>
            </div>
            <div v-if="purchase.notes" class="py-3 px-4 bg-surface-50 rounded-xl">
              <p class="text-xs text-slate-500 mb-1">ملاحظات</p>
              <p class="text-sm text-slate-700">{{ purchase.notes }}</p>
            </div>
          </div>

          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-xs text-slate-500">
              <span class="inline-flex px-2 py-0.5 rounded-lg text-xs font-bold" :class="purchase.requires_invoice ? 'bg-amber-100 text-amber-700' : 'bg-surface-100 text-slate-500'">
                {{ purchase.requires_invoice ? 'فاتورة مطلوبة' : 'فاتورة اختيارية' }}
              </span>
              <span>بواسطة {{ purchase.creator?.name }}</span>
            </div>
            <button
              v-if="purchase.status === 'pending'"
              @click="openCompleteModal(purchase)"
              class="btn-primary text-sm px-4 py-2 rounded-xl"
            >
              إتمام الشراء
            </button>
          </div>

          <div v-if="purchase.status === 'completed'" class="mt-4 pt-4 border-t border-surface-200">
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4">
              <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm font-bold text-blue-700">تم إرسال طلبك للمراجعة - بانتظار موافقة الإدارة</p>
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
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-4">
              <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <p class="text-sm font-bold text-emerald-700">تمت الموافقة على طلبك وخصم المبلغ من رصيدك</p>
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
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-4">
              <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <p class="text-sm font-bold text-red-700">تم رفض طلبك من الإدارة</p>
              </div>
              <div class="bg-white rounded-xl p-3 border border-red-100">
                <p class="text-xs text-slate-500 mb-1">سبب الرفض</p>
                <p class="text-sm font-bold text-red-700">{{ purchase.rejection_reason }}</p>
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
          <p class="text-sm text-slate-500">لم يتم تكليفك بأي طلبات شراء بعد</p>
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
import { router, useForm } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const props = defineProps({
  purchases: Array,
})

const showModal = ref(false)
const selectedPurchase = ref(null)

const completeForm = useForm({
  actual_amount: '',
  invoice_image: null,
})

function statusText(status) {
  const map = {
    pending: 'قيد الانتظار',
    completed: 'بانتظار المراجعة',
    approved: 'تمت الموافقة',
    rejected: 'مرفوض',
  }
  return map[status] || status
}

function statusClass(status) {
  const map = {
    pending: 'bg-amber-100 text-amber-700',
    completed: 'bg-blue-100 text-blue-700',
    approved: 'bg-emerald-100 text-emerald-700',
    rejected: 'bg-red-100 text-red-700',
  }
  return map[status] || 'bg-surface-100 text-slate-500'
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
</script>
