<template>
  <DashboardLayout>
    <div>
      <div class="flex items-center justify-between mb-8 animate-fade-in-up">
        <div>
          <h1 class="text-3xl font-extrabold text-slate-900">المهام اليومية الثابتة</h1>
          <p class="text-slate-500 mt-1">إدارة المهام اليومية وتعيينها للأدوار</p>
        </div>
        <button @click="openAddModal" class="btn-primary">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          إضافة مهمة
        </button>
      </div>

      <Alert v-if="flash.message" variant="success" :title="flash.message" closeable class="mb-6 animate-fade-in-down" />

      <div class="card overflow-hidden animate-fade-in-up animate-delay-100">
        <div class="p-6 border-b border-surface-200 flex items-center justify-between">
          <TableSearch v-model="search" />
        </div>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-surface-50 border-b border-surface-200">
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">المهمة</th>
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">الوصف</th>
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">الأدوار</th>
                <th class="px-6 py-4 text-center text-sm font-bold text-slate-700">الحالة</th>
                <th class="px-6 py-4 text-center text-sm font-bold text-slate-700">الإجراءات</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-surface-100">
              <tr v-for="task in filteredList" :key="task.id" class="hover:bg-primary-50/50 transition-colors">
                <td class="px-6 py-4 font-medium text-slate-900">{{ task.name }}</td>
                <td class="px-6 py-4 text-sm text-slate-500 max-w-xs truncate">{{ task.description || '-' }}</td>
                <td class="px-6 py-4">
                  <div class="flex flex-wrap gap-1.5">
                    <span
                      v-for="role in task.roles"
                      :key="role.id"
                      class="inline-flex px-2.5 py-0.5 rounded-lg bg-primary-100 text-primary-700 text-xs font-semibold"
                    >
                      {{ roleLabel(role.name) }}
                    </span>
                  </div>
                </td>
                <td class="px-6 py-4 text-center">
                  <span
                    class="inline-flex px-3 py-1 rounded-full text-xs font-bold"
                    :class="task.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500'"
                  >
                    {{ task.is_active ? 'نشط' : 'غير نشط' }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <div class="flex gap-2 justify-center">
                    <button @click="openEditModal(task)" class="p-2 rounded-xl bg-primary-100 text-primary-700 hover:bg-primary-200 transition-all duration-200" title="تعديل">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                    </button>
                    <button @click="confirmDelete(task)" class="p-2 rounded-xl bg-red-100 text-red-700 hover:bg-red-200 transition-all duration-200" title="حذف">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="filteredList.length === 0">
                <td colspan="5" class="px-6 py-12 text-center text-slate-500">لا توجد مهام بعد</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Add/Edit Modal -->
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
            <h3 class="text-xl font-bold text-slate-900">{{ editingTask ? 'تعديل المهمة' : 'إضافة مهمة جديدة' }}</h3>
          </div>

          <form @submit.prevent="submitForm">
            <div class="mb-4">
              <label class="block text-sm font-semibold text-slate-700 mb-1.5">اسم المهمة <span class="text-red-500">*</span></label>
              <input v-model="form.name" type="text" class="input-base" placeholder="مثال: مراجعة تقارير اليوم" required />
            </div>

            <div class="mb-4">
              <label class="block text-sm font-semibold text-slate-700 mb-1.5">الوصف</label>
              <textarea v-model="form.description" class="input-base" rows="3" placeholder="وصف المهمة (اختياري)"></textarea>
            </div>

            <div class="mb-4">
              <label class="block text-sm font-semibold text-slate-700 mb-2">الأدوار المستهدفة <span class="text-red-500">*</span></label>
              <div class="space-y-2 max-h-40 overflow-y-auto p-3 bg-surface-50 rounded-xl">
                <label
                  v-for="role in roles"
                  :key="role.id"
                  class="flex items-center gap-3 cursor-pointer group"
                >
                  <input
                    type="checkbox"
                    :value="role.id"
                    v-model="form.role_ids"
                    class="w-4 h-4 rounded border-surface-300 text-primary-600 focus:ring-primary-500"
                  />
                  <span class="text-sm text-slate-700 group-hover:text-slate-900">{{ roleLabel(role.name) }}</span>
                </label>
              </div>
            </div>

            <div class="mb-6">
              <label class="flex items-center gap-3 cursor-pointer">
                <input
                  type="checkbox"
                  v-model="form.is_active"
                  class="w-4 h-4 rounded border-surface-300 text-primary-600 focus:ring-primary-500"
                />
                <span class="text-sm font-semibold text-slate-700">المهمة نشطة</span>
              </label>
            </div>

            <div class="flex gap-3">
              <button type="submit" class="btn-primary flex-1" :disabled="form.processing">
                {{ form.processing ? 'جاري الحفظ...' : (editingTask ? 'تحديث' : 'إضافة') }}
              </button>
              <button type="button" @click="closeModal" class="btn-ghost px-6">إلغاء</button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import Alert from '@/Components/Alert.vue'
import TableSearch from '@/Components/TableSearch.vue'

const props = defineProps({
  tasks: Array,
  roles: Array,
})

const page = usePage()
const flash = page.props.flash || {}

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

const filteredList = computed(() => props.tasks.filter(item => matchesSearch(item, search.value)))

const showModal = ref(false)
const editingTask = ref(null)

const form = useForm({
  name: '',
  description: '',
  is_active: true,
  role_ids: [],
})

const roleLabel = (name) => {
  const labels = { admin: 'إداري', school: 'مدرسة', teacher: 'مدرس', employee: 'موظف' }
  return labels[name] || name
}

const openAddModal = () => {
  editingTask.value = null
  form.name = ''
  form.description = ''
  form.is_active = true
  form.role_ids = []
  showModal.value = true
}

const openEditModal = (task) => {
  editingTask.value = task
  form.name = task.name
  form.description = task.description || ''
  form.is_active = task.is_active
  form.role_ids = task.roles?.map(r => r.id) || []
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  form.reset()
}

const submitForm = () => {
  if (editingTask.value) {
    form.put(`/admin/fixed-tasks/${editingTask.value.id}`, {
      onSuccess: () => closeModal(),
    })
  } else {
    form.post('/admin/fixed-tasks', {
      onSuccess: () => closeModal(),
    })
  }
}

const confirmDelete = (task) => {
  if (confirm(`هل أنت متأكد من حذف "${task.name}"؟`)) {
    useForm({}).delete(`/admin/fixed-tasks/${task.id}`, {
      onSuccess: () => {},
    })
  }
}
</script>
