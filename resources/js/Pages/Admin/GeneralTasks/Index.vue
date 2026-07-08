<template>
  <DashboardLayout>
    <div>
      <div class="flex items-center justify-between mb-8 animate-fade-in-up">
        <div>
          <h1 class="text-3xl font-extrabold text-slate-900">المهام العامة</h1>
          <p class="text-slate-500 mt-1">إنشاء وإدارة المهام العامة وتعيينها لموظفين أو أدوار محددة</p>
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
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">الموظفون</th>
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">الأدوار</th>
                <th class="px-6 py-4 text-center text-sm font-bold text-slate-700">المدة</th>
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
                      v-for="user in task.assigned_users"
                      :key="user.id"
                      class="inline-flex px-2.5 py-0.5 rounded-lg bg-amber-100 text-amber-700 text-xs font-semibold"
                    >
                      {{ user.name }}
                    </span>
                    <span v-if="!task.assigned_users?.length" class="text-sm text-slate-400">-</span>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <div class="flex flex-wrap gap-1.5">
                    <span
                      v-for="role in task.roles"
                      :key="role.id"
                      class="inline-flex px-2.5 py-0.5 rounded-lg bg-primary-100 text-primary-700 text-xs font-semibold"
                    >
                      {{ roleLabel(role.name) }}
                    </span>
                    <span v-if="!task.roles?.length" class="text-sm text-slate-400">-</span>
                  </div>
                </td>
                <td class="px-6 py-4 text-center text-sm">
                  <div class="flex flex-col gap-0.5">
                    <span class="text-slate-600 text-xs" dir="ltr">{{ formatDate(task.start_at) }}</span>
                    <span class="text-slate-400 text-xs">→</span>
                    <span class="text-slate-600 text-xs" dir="ltr">{{ formatDate(task.end_at) }}</span>
                  </div>
                </td>
                <td class="px-6 py-4 text-center">
                  <span
                    class="inline-flex px-3 py-1 rounded-full text-xs font-bold"
                    :class="taskStatusClass(task)"
                  >
                    {{ taskStatusLabel(task) }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <div class="flex gap-2 justify-center">
                    <Link :href="`/admin/general-tasks/${task.id}`" class="p-2 rounded-xl bg-sky-100 text-sky-700 hover:bg-sky-200 transition-all duration-200 inline-block" title="عرض التفاصيل">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                    </Link>
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
                <td colspan="7" class="px-6 py-12 text-center text-slate-500">لا توجد مهام بعد</td>
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
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg p-8 animate-scale-in max-h-[90vh] overflow-y-auto">
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
              <input v-model="form.name" type="text" class="input-base" placeholder="أدخل اسم المهمة" required />
            </div>

            <div class="mb-4">
              <label class="block text-sm font-semibold text-slate-700 mb-1.5">الوصف</label>
              <textarea v-model="form.description" class="input-base" rows="3" placeholder="وصف المهمة (اختياري)"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">تاريخ البداية <span class="text-red-500">*</span></label>
                <input v-model="form.start_at" type="datetime-local" class="input-base" required />
              </div>
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">تاريخ النهاية <span class="text-red-500">*</span></label>
                <input v-model="form.end_at" type="datetime-local" class="input-base" required />
              </div>
            </div>

            <div class="mb-4">
              <label class="block text-sm font-semibold text-slate-700 mb-2">الموظفون المستهدفون</label>
              <div class="space-y-2 max-h-32 overflow-y-auto p-3 bg-surface-50 rounded-xl">
                <label
                  v-for="emp in employees"
                  :key="emp.id"
                  class="flex items-center gap-3 cursor-pointer group"
                >
                  <input
                    type="checkbox"
                    :value="emp.id"
                    v-model="form.user_ids"
                    class="w-4 h-4 rounded border-surface-300 text-primary-600 focus:ring-primary-500"
                  />
                  <span class="text-sm text-slate-700 group-hover:text-slate-900">{{ emp.name }}</span>
                  <span class="text-xs text-slate-400 mr-auto">{{ emp.email }}</span>
                </label>
              </div>
            </div>

            <div class="mb-6">
              <label class="block text-sm font-semibold text-slate-700 mb-2">الأدوار المستهدفة</label>
              <div class="space-y-2 max-h-32 overflow-y-auto p-3 bg-surface-50 rounded-xl">
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
              <p class="text-xs text-slate-400 mt-1">* يجب اختيار موظف واحد على الأقل أو دور واحد</p>
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
import { Link, useForm, usePage } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import Alert from '@/Components/Alert.vue'
import TableSearch from '@/Components/TableSearch.vue'

const props = defineProps({
  tasks: Array,
  employees: Array,
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
  start_at: '',
  end_at: '',
  user_ids: [],
  role_ids: [],
})

const roleLabel = (name) => {
  const labels = { admin: 'إداري', school: 'مدرسة', teacher: 'مدرس', employee: 'موظف' }
  return labels[name] || name
}

const formatDate = (dt) => {
  if (!dt) return '-'
  const d = new Date(dt)
  return d.toLocaleDateString('ar-SA', { day: 'numeric', month: 'short', year: 'numeric' }) + ' ' +
    d.toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' })
}

const taskStatusClass = (task) => {
  if (!task.is_active) return 'bg-slate-100 text-slate-500'
  const now = new Date()
  const start = new Date(task.start_at)
  const end = new Date(task.end_at)
  if (now < start) return 'bg-amber-100 text-amber-700'
  if (now > end) return 'bg-red-100 text-red-700'
  return 'bg-emerald-100 text-emerald-700'
}

const taskStatusLabel = (task) => {
  if (!task.is_active) return 'غير نشط'
  const now = new Date()
  const start = new Date(task.start_at)
  const end = new Date(task.end_at)
  if (now < start) return 'قادمة'
  if (now > end) return 'منتهية'
  return 'نشط'
}

const openAddModal = () => {
  editingTask.value = null
  form.name = ''
  form.description = ''
  form.start_at = ''
  form.end_at = ''
  form.user_ids = []
  form.role_ids = []
  showModal.value = true
}

const openEditModal = (task) => {
  editingTask.value = task
  form.name = task.name
  form.description = task.description || ''
  form.start_at = task.start_at ? task.start_at.slice(0, 16) : ''
  form.end_at = task.end_at ? task.end_at.slice(0, 16) : ''
  form.user_ids = task.assigned_users?.map(u => u.id) || []
  form.role_ids = task.roles?.map(r => r.id) || []
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  form.reset()
}

const submitForm = () => {
  if (editingTask.value) {
    form.put(`/admin/general-tasks/${editingTask.value.id}`, {
      onSuccess: () => closeModal(),
    })
  } else {
    form.post('/admin/general-tasks', {
      onSuccess: () => closeModal(),
    })
  }
}

const confirmDelete = (task) => {
  if (confirm(`هل أنت متأكد من حذف "${task.name}"؟`)) {
    useForm({}).delete(`/admin/general-tasks/${task.id}`, {
      onSuccess: () => {},
    })
  }
}
</script>