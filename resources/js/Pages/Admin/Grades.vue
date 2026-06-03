<template>
  <MainLayout>
    <div>
      <!-- Header -->
      <div class="flex items-center justify-between mb-8 animate-fade-in-up">
        <div>
          <h1 class="text-3xl font-extrabold text-slate-900">إدارة الصفوف</h1>
          <p class="text-slate-500 mt-1">إضافة وتعديل وحذف الصفوف الدراسية</p>
        </div>
        <button @click="openAddModal" class="btn-primary">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          إضافة صف
        </button>
      </div>

      <!-- Flash Message -->
      <Alert v-if="flash.message" variant="success" :title="flash.message" closeable class="mb-6 animate-fade-in-down" />

      <!-- Grades List -->
      <div class="card overflow-hidden animate-fade-in-up animate-delay-100">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-surface-50 border-b border-surface-200">
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">#</th>
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">اسم الصف</th>
                <th class="px-6 py-4 text-center text-sm font-bold text-slate-700">الإجراءات</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-surface-100">
              <tr v-for="(grade, index) in grades" :key="grade.id" class="hover:bg-primary-50/50 transition-colors">
                <td class="px-6 py-4 text-slate-500 font-medium">{{ index + 1 }}</td>
                <td class="px-6 py-4 font-medium text-slate-900">{{ grade.name }}</td>
                <td class="px-6 py-4">
                  <div class="flex gap-2 justify-center">
                    <button @click="openEditModal(grade)" class="p-2 rounded-xl bg-primary-100 text-primary-700 hover:bg-primary-200 transition-all duration-200" title="تعديل">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                    </button>
                    <button @click="confirmDelete(grade)" class="p-2 rounded-xl bg-red-100 text-red-700 hover:bg-red-200 transition-all duration-200" title="حذف">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="grades.length === 0">
                <td colspan="3" class="px-6 py-12 text-center text-slate-500">لا توجد صفوف مضافة بعد</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Add/Edit Modal -->
      <Teleport to="body">
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="closeModal">
          <div class="fixed inset-0 bg-black/30 backdrop-blur-sm" @click="closeModal"></div>
          <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 animate-scale-in">
            <div class="flex items-center gap-3 mb-6">
              <div class="w-10 h-10 rounded-xl bg-warm-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-warm-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
              </div>
              <h3 class="text-xl font-bold text-slate-900">{{ editingGrade ? 'تعديل الصف' : 'إضافة صف جديد' }}</h3>
            </div>

            <form @submit.prevent="submitForm">
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">اسم الصف</label>
                <input
                  v-model="form.name"
                  type="text"
                  class="input-base"
                  placeholder="أدخل اسم الصف"
                  required
                />
              </div>

              <div class="flex gap-3 mt-8">
                <button type="submit" class="btn-primary flex-1" :disabled="form.processing">
                  {{ form.processing ? 'جاري الحفظ...' : (editingGrade ? 'تحديث' : 'إضافة') }}
                </button>
                <button type="button" @click="closeModal" class="btn-ghost px-6">إلغاء</button>
              </div>
            </form>
          </div>
        </div>
      </Teleport>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import Alert from '@/Components/Alert.vue'

const props = defineProps({
  grades: Array,
})

const page = usePage()
const flash = page.props.flash || {}

const showModal = ref(false)
const editingGrade = ref(null)

const form = useForm({
  name: '',
})

const openAddModal = () => {
  editingGrade.value = null
  form.name = ''
  showModal.value = true
}

const openEditModal = (grade) => {
  editingGrade.value = grade
  form.name = grade.name
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  form.reset()
}

const submitForm = () => {
  if (editingGrade.value) {
    form.put(`/admin/grades/${editingGrade.value.id}`, {
      onSuccess: () => closeModal(),
    })
  } else {
    form.post('/admin/grades', {
      onSuccess: () => closeModal(),
    })
  }
}

const confirmDelete = (grade) => {
  if (confirm(`هل أنت متأكد من حذف "${grade.name}"؟`)) {
    useForm({}).delete(`/admin/grades/${grade.id}`, {
      onSuccess: () => {},
    })
  }
}
</script>
