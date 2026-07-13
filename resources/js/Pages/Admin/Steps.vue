<template>
  <DashboardLayout>
    <div>
      <div class="flex items-center justify-between mb-8 animate-fade-in-up">
        <div>
          <h1 class="text-3xl font-extrabold text-slate-900">خطوات التسجيل</h1>
          <p class="text-slate-500 mt-1">إدارة خطوات التسجيل المعروضة في الصفحة الرئيسية</p>
        </div>
        <button @click="openAddModal" class="btn-primary">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          إضافة خطوة
        </button>
      </div>

      <Alert v-if="flash.success" variant="success" :title="flash.success" closeable class="mb-6 animate-fade-in-down" />

      <div class="card overflow-hidden animate-fade-in-up animate-delay-100">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-surface-50 border-b border-surface-200">
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">#</th>
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">العنوان</th>
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">الوصف</th>
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">الأيقونة</th>
                <th class="px-6 py-4 text-center text-sm font-bold text-slate-700">فعال</th>
                <th class="px-6 py-4 text-center text-sm font-bold text-slate-700">الإجراءات</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-surface-100">
              <tr v-for="(step, index) in steps" :key="step.id" class="hover:bg-primary-50/50 transition-colors">
                <td class="px-6 py-4 text-slate-400 font-bold">{{ step.sort_order || index + 1 }}</td>
                <td class="px-6 py-4 font-medium text-slate-900">{{ step.title }}</td>
                <td class="px-6 py-4 text-slate-600 max-w-[300px] truncate">{{ step.description }}</td>
                <td class="px-6 py-4 text-slate-600">{{ step.icon || '-' }}</td>
                <td class="px-6 py-4 text-center">
                  <span :class="step.is_active ? 'badge-success' : 'badge bg-surface-200 text-surface-600'">
                    {{ step.is_active ? 'نعم' : 'لا' }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <div class="flex gap-2 justify-center">
                    <button @click="openEditModal(step)" class="p-2 rounded-xl bg-primary-100 text-primary-700 hover:bg-primary-200 transition-all duration-200" title="تعديل">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                    </button>
                    <button @click="confirmDelete(step)" class="p-2 rounded-xl bg-red-100 text-red-700 hover:bg-red-200 transition-all duration-200" title="حذف">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="steps.length === 0">
                <td colspan="6" class="px-6 py-12 text-center text-slate-500">لا توجد خطوات مضافة بعد</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <Teleport to="body">
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="closeModal">
          <div class="fixed inset-0 bg-black/30 backdrop-blur-sm" @click="closeModal"></div>
          <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg p-8 animate-scale-in">
            <div class="flex items-center gap-3 mb-6">
              <div class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
              </div>
              <h3 class="text-xl font-bold text-slate-900">{{ editingStep ? 'تعديل الخطوة' : 'إضافة خطوة جديدة' }}</h3>
            </div>

            <form @submit.prevent="submitForm">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1.5">العنوان <span class="text-red-500">*</span></label>
                  <input v-model="form.title" type="text" class="input-base" required />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1.5">الوصف <span class="text-red-500">*</span></label>
                  <textarea v-model="form.description" class="input-base min-h-[80px]" required></textarea>
                </div>
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1.5">الأيقونة</label>
                  <input v-model="form.icon" type="text" class="input-base" placeholder="اسم SVG icon" />
                  <p class="text-xs text-slate-400 mt-1">اختياري: اسم أيقونة لعرضها بجانب الخطوة</p>
                </div>
                <div class="flex items-center gap-3">
                  <input v-model="form.is_active" type="checkbox" id="is_active" class="w-5 h-5 rounded border-surface-300 text-primary-600 focus:ring-primary-500" />
                  <label for="is_active" class="text-sm font-semibold text-slate-700">خطوة نشطة</label>
                </div>
              </div>

              <div class="flex gap-3 mt-8">
                <button type="submit" class="btn-primary flex-1" :disabled="form.processing">
                  {{ form.processing ? 'جاري الحفظ...' : (editingStep ? 'تحديث' : 'إضافة') }}
                </button>
                <button type="button" @click="closeModal" class="btn-ghost px-6">إلغاء</button>
              </div>
            </form>
          </div>
        </div>
      </Teleport>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import Alert from '@/Components/Alert.vue'

const props = defineProps({
  steps: Array,
})

const page = usePage()
const flash = page.props.flash || {}

const showModal = ref(false)
const editingStep = ref(null)

const form = useForm({
  title: '',
  description: '',
  icon: '',
  is_active: true,
})

const openAddModal = () => {
  editingStep.value = null
  form.reset()
  form.is_active = true
  showModal.value = true
}

const openEditModal = (step) => {
  editingStep.value = step
  form.title = step.title || ''
  form.description = step.description || ''
  form.icon = step.icon || ''
  form.is_active = step.is_active
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  form.reset()
}

const submitForm = () => {
  if (editingStep.value) {
    form.post(`/admin/steps/${editingStep.value.id}`, {
      onSuccess: () => closeModal(),
      preserveScroll: true,
    })
  } else {
    form.post('/admin/steps', {
      onSuccess: () => closeModal(),
      preserveScroll: true,
    })
  }
}

const confirmDelete = (step) => {
  if (confirm(`هل أنت متأكد من حذف "${step.title}"؟`)) {
    useForm({}).delete(`/admin/steps/${step.id}`, {
      preserveScroll: true,
    })
  }
}
</script>
