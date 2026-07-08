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
        <div class="relative px-8 py-10">
          <div class="flex items-center gap-4 mb-3">
            <div class="w-14 h-14 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center text-3xl shadow-lg">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
            </div>
            <div>
              <h1 class="text-2xl font-bold text-white">طلب شراء جديد</h1>
              <p class="text-white/70 text-sm">إضافة طلب شراء وتكليف الموظفين</p>
            </div>
          </div>
        </div>
      </div>

      <div class="max-w-2xl mx-auto animate-fade-in-up animate-delay-100">
        <form @submit.prevent="submit" class="card p-8">
          <div class="space-y-6">
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-1.5">اسم المنتج <span class="text-red-500">*</span></label>
              <input
                v-model="form.item_name"
                type="text"
                class="input-base"
                placeholder="أدخل اسم المنتج المطلوب شراؤه..."
              />
              <p v-if="form.errors.item_name" class="mt-1 text-xs text-red-500 font-semibold">{{ form.errors.item_name }}</p>
            </div>

            <div>
              <label class="block text-sm font-bold text-slate-700 mb-1.5">المبلغ <span class="text-red-500">*</span></label>
              <div class="relative">
                <input
                  v-model="form.amount"
                  type="number"
                  step="0.01"
                  min="0"
                  class="input-base pl-16"
                  placeholder="0.00"
                />
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400">ريال</span>
              </div>
              <p v-if="form.errors.amount" class="mt-1 text-xs text-red-500 font-semibold">{{ form.errors.amount }}</p>
            </div>

            <div>
              <label class="block text-sm font-bold text-slate-700 mb-1.5">الموظفون المكلفون <span class="text-red-500">*</span></label>
              <MultiSelect
                v-model="form.employee_ids"
                :options="employeeOptions"
                :error="form.errors.employee_ids"
                placeholder="اختر الموظفين..."
              />
            </div>

            <div>
              <label class="block text-sm font-bold text-slate-700 mb-1.5">ملاحظات</label>
              <textarea
                v-model="form.notes"
                class="input-base min-h-[100px]"
                placeholder="ملاحظات حول طلب الشراء..."
              ></textarea>
              <p v-if="form.errors.notes" class="mt-1 text-xs text-red-500 font-semibold">{{ form.errors.notes }}</p>
            </div>

            <div>
              <label class="flex items-center gap-3 cursor-pointer group">
                <input
                  type="checkbox"
                  v-model="form.requires_invoice"
                  class="w-4 h-4 rounded border-surface-300 text-primary-600 focus:ring-primary-500"
                />
                <div>
                  <span class="text-sm font-bold text-slate-700 group-hover:text-slate-900">إلزام بإرفاق فاتورة</span>
                  <p class="text-xs text-slate-500">عند التفعيل، يجب على الموظف إرفاق صورة الفاتورة عند إتمام الشراء</p>
                </div>
              </label>
            </div>
          </div>

          <div class="flex items-center gap-3 mt-8 pt-6 border-t border-surface-200">
            <button type="submit" :disabled="form.processing" class="btn-primary flex-1">
              <svg v-if="form.processing" class="w-4 h-4 animate-spin inline ml-1" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
              </svg>
              {{ form.processing ? 'جاري الإرسال...' : 'إرسال الطلب' }}
            </button>
            <Link href="/admin/purchases" class="btn-ghost flex-1">إلغاء</Link>
          </div>
        </form>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import MultiSelect from '@/Components/MultiSelect.vue'

const props = defineProps({
  employees: Array,
})

const employeeOptions = computed(() =>
  props.employees.map((e) => ({ id: e.id, label: e.name }))
)

const form = useForm({
  item_name: '',
  amount: '',
  employee_ids: [],
  notes: '',
  requires_invoice: false,
})

function submit() {
  form.post('/admin/purchases', {
    onSuccess: () => {
      form.reset()
    },
  })
}

function goBack() {
  window.history.back()
}
</script>
