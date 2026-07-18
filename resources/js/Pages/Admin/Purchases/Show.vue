<template>
  <DashboardLayout>
    <div>
      <Link href="/admin/purchases" class="btn-ghost mb-6 animate-fade-in-up">
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
              </svg>
            </div>
            <div>
              <h1 class="text-2xl font-bold text-white">تفاصيل طلب الشراء</h1>
              <p class="text-white/70 text-sm">رقم {{ purchase.id }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6 animate-fade-in-up animate-delay-100">
        <div class="card p-6">
          <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">معلومات الطلب</h3>
          <div class="space-y-4">
            <div class="flex items-center justify-between py-3 px-4 bg-surface-50 rounded-xl">
              <span class="text-sm text-slate-500">المنتج</span>
              <span class="text-sm font-bold text-slate-900">{{ purchase.item_name }}</span>
            </div>
            <div class="flex items-center justify-between py-3 px-4 bg-surface-50 rounded-xl">
              <span class="text-sm text-slate-500">المبلغ</span>
              <span class="text-sm font-bold text-slate-900">{{ purchase.amount }} ريال</span>
            </div>
            <div class="flex items-center justify-between py-3 px-4 bg-surface-50 rounded-xl">
              <span class="text-sm text-slate-500">الحالة</span>
              <span
                class="inline-flex px-2.5 py-0.5 rounded-lg text-xs font-bold"
                :class="statusClass"
              >
                {{ statusText }}
              </span>
            </div>
            <div class="flex items-center justify-between py-3 px-4 bg-surface-50 rounded-xl">
              <span class="text-sm text-slate-500">الفاتورة</span>
              <span
                class="inline-flex px-2.5 py-0.5 rounded-lg text-xs font-bold"
                :class="purchase.requires_invoice ? 'bg-amber-100 text-amber-700' : 'bg-surface-100 text-slate-500'"
              >
                {{ purchase.requires_invoice ? 'مطلوبة' : 'اختياري' }}
              </span>
            </div>
            <div class="flex items-center justify-between py-3 px-4 bg-surface-50 rounded-xl">
              <span class="text-sm text-slate-500">بواسطة</span>
              <span class="text-sm font-bold text-slate-900">{{ purchase.creator?.name }}</span>
            </div>
            <div class="flex items-center justify-between py-3 px-4 bg-surface-50 rounded-xl">
              <span class="text-sm text-slate-500">تاريخ الإنشاء</span>
              <span class="text-sm font-bold text-slate-900">{{ formatDate(purchase.created_at) }}</span>
            </div>
          </div>
        </div>

        <div class="card p-6">
          <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">الموظفون المكلفون</h3>
          <div class="space-y-3">
            <div
              v-for="emp in purchase.employees"
              :key="emp.id"
              class="flex items-center justify-between py-3 px-4 bg-surface-50 rounded-xl"
            >
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-primary-100 flex items-center justify-center text-primary-700 font-bold text-sm">
                  {{ emp.name?.charAt(0) }}
                </div>
                <span class="text-sm font-bold text-slate-900">{{ emp.name }}</span>
              </div>
              <span v-if="employeeBalance != null" class="text-xs font-bold text-slate-500">
                الرصيد: {{ employeeBalance }} ريال
              </span>
            </div>
            <p v-if="!purchase.employees?.length" class="text-sm text-slate-400 text-center py-4">لا يوجد موظفون</p>
          </div>
        </div>
      </div>

      <div v-if="purchase.notes" class="card p-6 mb-6 animate-fade-in-up animate-delay-200">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">ملاحظات</h3>
        <p class="text-sm text-slate-700 whitespace-pre-wrap">{{ purchase.notes }}</p>
      </div>

      <div v-if="purchase.status === 'completed'" class="card p-6 mb-6 animate-fade-in-up animate-delay-200">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">نتيجة الشراء من الموظف</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div class="py-3 px-4 bg-surface-50 rounded-xl">
            <p class="text-xs text-slate-500 mb-1">المبلغ الفعلي</p>
            <p class="text-lg font-bold text-slate-900">{{ purchase.actual_amount }} ريال</p>
          </div>
          <div class="py-3 px-4 bg-surface-50 rounded-xl">
            <p class="text-xs text-slate-500 mb-1">المبلغ المتبقي</p>
            <p class="text-lg font-bold" :class="purchase.remaining_amount > 0 ? 'text-amber-600' : 'text-emerald-600'">
              {{ purchase.remaining_amount }} ريال
            </p>
          </div>
          <div class="py-3 px-4 bg-surface-50 rounded-xl">
            <p class="text-xs text-slate-500 mb-1">تاريخ الإكمال</p>
            <p class="text-sm font-bold text-slate-900">{{ formatDate(purchase.completed_at) }}</p>
          </div>
        </div>

        <div v-if="purchase.invoice_image" class="mt-4">
          <h4 class="text-sm font-bold text-slate-700 mb-2">الفاتورة</h4>
          <a
            :href="`/storage/${purchase.invoice_image}`"
            target="_blank"
            class="inline-flex items-center gap-2 px-4 py-3 bg-primary-50 rounded-xl hover:bg-primary-100 transition-colors border border-primary-200"
          >
            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span class="text-sm font-bold text-primary-700">عرض الفاتورة</span>
          </a>
        </div>

        <div class="flex items-center gap-3 mt-6 pt-4 border-t border-surface-200">
          <button
            @click="approvePurchase"
            :disabled="processing"
            class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-6 rounded-xl transition-colors disabled:opacity-50"
          >
            <svg class="w-5 h-5 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            الموافقة وخصم المبلغ
          </button>
          <button
            @click="showRejectModal = true"
            :disabled="processing"
            class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-xl transition-colors disabled:opacity-50"
          >
            <svg class="w-5 h-5 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            رفض الطلب
          </button>
        </div>
      </div>

      <div v-if="purchase.status === 'approved'" class="card p-6 mb-6 animate-fade-in-up animate-delay-200 border-2 border-emerald-200">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-bold text-emerald-700">تمت الموافقة على الطلب</h3>
            <p class="text-xs text-slate-500">{{ formatDate(purchase.approved_at) }} - بواسطة {{ purchase.approver?.name }}</p>
          </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div class="py-3 px-4 bg-emerald-50 rounded-xl">
            <p class="text-xs text-slate-500 mb-1">المبلغ الفعلي</p>
            <p class="text-lg font-bold text-emerald-700">{{ purchase.actual_amount }} ريال</p>
          </div>
          <div class="py-3 px-4 bg-emerald-50 rounded-xl">
            <p class="text-xs text-slate-500 mb-1">المبلغ المتبقي</p>
            <p class="text-lg font-bold" :class="purchase.remaining_amount > 0 ? 'text-amber-600' : 'text-emerald-600'">
              {{ purchase.remaining_amount }} ريال
            </p>
          </div>
          <div class="py-3 px-4 bg-emerald-50 rounded-xl">
            <p class="text-xs text-slate-500 mb-1">تاريخ الإكمال</p>
            <p class="text-sm font-bold text-slate-900">{{ formatDate(purchase.completed_at) }}</p>
          </div>
        </div>
      </div>

      <div v-if="purchase.status === 'rejected'" class="card p-6 mb-6 animate-fade-in-up animate-delay-200 border-2 border-red-200">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-bold text-red-700">تم رفض الطلب</h3>
          </div>
        </div>
        <div class="py-3 px-4 bg-red-50 rounded-xl">
          <p class="text-xs text-slate-500 mb-1">سبب الرفض</p>
          <p class="text-sm font-bold text-red-700">{{ purchase.rejection_reason }}</p>
        </div>
      </div>

      <div v-if="purchase.status === 'pending'" class="card p-6 animate-fade-in-up animate-delay-200">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">نتيجة الشراء</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div class="py-3 px-4 bg-surface-50 rounded-xl">
            <p class="text-xs text-slate-500 mb-1">المبلغ الفعلي</p>
            <p class="text-lg font-bold text-slate-900">{{ purchase.actual_amount }} ريال</p>
          </div>
          <div class="py-3 px-4 bg-surface-50 rounded-xl">
            <p class="text-xs text-slate-500 mb-1">المبلغ المتبقي</p>
            <p class="text-lg font-bold" :class="purchase.remaining_amount > 0 ? 'text-amber-600' : 'text-emerald-600'">
              {{ purchase.remaining_amount }} ريال
            </p>
          </div>
          <div class="py-3 px-4 bg-surface-50 rounded-xl">
            <p class="text-xs text-slate-500 mb-1">تاريخ الإكمال</p>
            <p class="text-sm font-bold text-slate-900">{{ formatDate(purchase.completed_at) }}</p>
          </div>
        </div>
      </div>

      <!-- Reject Modal -->
      <div v-if="showRejectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" @click.self="showRejectModal = false">
        <div class="bg-white rounded-3xl w-full max-w-lg mx-4 p-8 shadow-2xl animate-fade-in-up">
          <h3 class="text-xl font-extrabold text-slate-900 mb-6">رفض طلب الشراء</h3>
          <form @submit.prevent="submitReject">
            <div class="mb-5">
              <label class="block text-sm font-bold text-slate-700 mb-1.5">سبب الرفض <span class="text-red-500">*</span></label>
              <textarea
                v-model="rejectForm.rejection_reason"
                rows="4"
                class="input-base"
                placeholder="اكتب سبب رفض الطلب..."
              ></textarea>
              <p v-if="rejectForm.errors.rejection_reason" class="mt-1 text-xs text-red-500 font-semibold">{{ rejectForm.errors.rejection_reason }}</p>
            </div>
            <div class="flex items-center gap-3">
              <button type="submit" :disabled="rejectForm.processing" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-xl transition-colors disabled:opacity-50 flex-1">
                {{ rejectForm.processing ? 'جاري الرفض...' : 'تأكيد الرفض' }}
              </button>
              <button type="button" @click="showRejectModal = false" class="btn-ghost flex-1">إلغاء</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const props = defineProps({
  purchase: Object,
  employeeBalance: Number,
})

const showRejectModal = ref(false)
const processing = ref(false)

const rejectForm = useForm({
  rejection_reason: '',
})

const statusText = computed(() => {
  const map = {
    pending: 'قيد الانتظار',
    completed: 'بانتظار المراجعة',
    approved: 'تمت الموافقة',
    rejected: 'مرفوض',
  }
  return map[props.purchase.status] || props.purchase.status
})

const statusClass = computed(() => {
  const map = {
    pending: 'bg-amber-100 text-amber-700',
    completed: 'bg-blue-100 text-blue-700',
    approved: 'bg-emerald-100 text-emerald-700',
    rejected: 'bg-red-100 text-red-700',
  }
  return map[props.purchase.status] || 'bg-surface-100 text-slate-500'
})

function approvePurchase() {
  if (!confirm('هل أنت متأكد من الموافقة على هذا الطلب؟ سيتم خصم المبلغ من رصيد الموظف.')) return
  processing.value = true
  router.post(`/admin/purchases/${props.purchase.id}/approve`, {}, {
    onFinish: () => { processing.value = false },
  })
}

function submitReject() {
  processing.value = true
  rejectForm.post(`/admin/purchases/${props.purchase.id}/reject`, {
    onSuccess: () => {
      showRejectModal.value = false
      rejectForm.reset()
    },
    onFinish: () => { processing.value = false },
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
