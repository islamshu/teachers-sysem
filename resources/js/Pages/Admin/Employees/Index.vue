<template>
  <DashboardLayout>
    <div>
      <div class="flex items-center justify-between mb-8 animate-fade-in-up">
        <div>
          <h1 class="text-3xl font-extrabold text-slate-900">إدارة الموظفين</h1>
          <p class="text-slate-500 mt-1">إضافة وتعديل وحذف الموظفين الإداريين</p>
        </div>
        <div class="flex gap-3">
          <button @click="openAddModal" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            إضافة موظف
          </button>
          <button @click="openTeacherModal" class="btn-primary" style="background-color: #059669;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            إضافة مدرس
          </button>
        </div>
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
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">الموظف</th>
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">البريد الإلكتروني</th>
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">الفروع</th>
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">الأدوار</th>
                <th class="px-6 py-4 text-center text-sm font-bold text-slate-700">الإجراءات</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-surface-100">
              <tr v-for="employee in filteredList" :key="employee.id" class="hover:bg-primary-50/50 transition-colors">
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary-100 to-surface-100 flex items-center justify-center text-sm font-bold text-primary-600">
                      {{ employee.name?.charAt(0) }}
                    </div>
                    <span class="font-medium text-slate-900">{{ employee.name }}</span>
                  </div>
                </td>
                <td class="px-6 py-4 text-sm text-slate-600">{{ employee.email }}</td>
                <td class="px-6 py-4">
                  <div class="flex flex-wrap gap-1.5">
                    <span
                      v-for="branch in employee.branches"
                      :key="branch.id"
                      class="inline-flex px-2.5 py-0.5 rounded-lg bg-emerald-100 text-emerald-700 text-xs font-semibold"
                    >
                      {{ branch.name }}
                    </span>
                    <span v-if="!employee.branches?.length" class="text-sm text-slate-400">-</span>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <div class="flex flex-wrap gap-1.5">
                    <span
                      v-for="role in employee.roles"
                      :key="role.id"
                      class="inline-flex px-2.5 py-0.5 rounded-lg bg-primary-100 text-primary-700 text-xs font-semibold"
                    >
                      {{ roleLabel(role.name) }}
                    </span>
                    <span v-if="!employee.roles?.length" class="text-sm text-slate-400">-</span>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <div class="flex gap-2 justify-center">
                    <button @click="openReport(employee)" class="p-2 rounded-xl bg-indigo-100 text-indigo-700 hover:bg-indigo-200 transition-all duration-200" title="تقرير المهام">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                      </svg>
                    </button>
                    <button @click="openEditModal(employee)" class="p-2 rounded-xl bg-primary-100 text-primary-700 hover:bg-primary-200 transition-all duration-200" title="تعديل">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                    </button>
                    <button @click="confirmDelete(employee)" class="p-2 rounded-xl bg-red-100 text-red-700 hover:bg-red-200 transition-all duration-200" title="حذف">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="filteredList.length === 0">
                <td colspan="6" class="px-6 py-12 text-center text-slate-500">لا يوجد موظفون بعد</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Report Modal -->
    <Teleport to="body">
      <div v-if="showReportModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="closeReportModal">
        <div class="fixed inset-0 bg-black/30 backdrop-blur-sm" @click="closeReportModal"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl p-8 animate-scale-in max-h-[90vh] overflow-y-auto">
          <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                </svg>
              </div>
              <div>
                <h3 class="text-xl font-bold text-slate-900">تقرير مهام الموظف</h3>
                <p class="text-sm text-slate-500">{{ reportData?.employee?.name }} · {{ reportData?.employee?.email }}</p>
              </div>
            </div>
            <button @click="closeReportModal" class="p-2 rounded-xl hover:bg-surface-100 transition-colors">
              <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <div class="flex items-center gap-4 mb-6 p-4 bg-surface-50 rounded-xl">
            <label class="text-sm font-semibold text-slate-700">التاريخ:</label>
            <input
              type="date"
              v-model="reportDate"
              @change="loadReport"
              class="input-base w-auto"
            />
            <div v-if="reportData" class="flex gap-3 mr-auto">
              <span class="text-sm text-slate-500">تم: <strong class="text-emerald-600">{{ reportData.completed_count }}</strong></span>
              <span class="text-sm text-slate-500">المتبقي: <strong class="text-amber-600">{{ reportData.total_count - reportData.completed_count }}</strong></span>
              <span class="text-sm text-slate-500">الإجمالي: <strong class="text-slate-900">{{ reportData.total_count }}</strong></span>
            </div>
          </div>

          <div v-if="reportLoading" class="text-center py-12 text-slate-400">جاري التحميل...</div>

          <div v-else-if="reportData?.tasks?.length">
            <div class="space-y-2">
              <div
                v-for="task in reportData.tasks"
                :key="task.id"
                class="flex items-center gap-4 p-4 rounded-xl transition-all duration-200"
                :class="task.completed ? 'bg-emerald-50 border border-emerald-200' : 'bg-surface-50 border border-surface-200'"
              >
                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                  :class="task.completed ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-200 text-slate-400'"
                >
                  <svg v-if="task.completed" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="font-medium text-slate-900" :class="task.completed ? 'line-through text-slate-400' : ''">{{ task.name }}</p>
                  <p v-if="task.description" class="text-xs text-slate-500 truncate">{{ task.description }}</p>
                </div>
                <div class="flex flex-wrap gap-1 shrink-0">
                  <span
                    v-for="role in task.roles"
                    :key="role.id"
                    class="px-2 py-0.5 rounded-lg text-xs font-medium"
                    :class="task.completed ? 'bg-emerald-100 text-emerald-700' : 'bg-primary-100 text-primary-700'"
                  >
                    {{ roleLabel(role.name) }}
                  </span>
                </div>
                <div v-if="task.completed_at" class="text-xs text-slate-400 shrink-0 min-w-[4rem] text-left" dir="ltr">
                  {{ formatTime(task.completed_at) }}
                </div>
              </div>
            </div>
          </div>

          <div v-else class="text-center py-12 text-slate-400">لا توجد مهام لهذا الموظف في هذا التاريخ</div>

          <div class="mt-6 flex justify-end">
            <button @click="closeReportModal" class="btn-ghost px-6">إغلاق</button>
          </div>
        </div>
      </div>
    </Teleport>

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
            <h3 class="text-xl font-bold text-slate-900">{{ editingEmployee ? 'تعديل الموظف' : 'إضافة موظف جديد' }}</h3>
          </div>

          <form @submit.prevent="submitForm">
            <div class="mb-4">
              <label class="block text-sm font-semibold text-slate-700 mb-1.5">الاسم <span class="text-red-500">*</span></label>
              <input v-model="form.name" type="text" class="input-base" placeholder="أدخل اسم الموظف" required />
            </div>

            <div class="mb-4">
              <label class="block text-sm font-semibold text-slate-700 mb-1.5">البريد الإلكتروني <span class="text-red-500">*</span></label>
              <input v-model="form.email" type="email" class="input-base" placeholder="employee@example.com" required />
            </div>

            <div class="mb-4">
              <label class="block text-sm font-semibold text-slate-700 mb-1.5">كلمة المرور (اتركها فارغة إذا لم ترد التغيير)
                <span v-if="!editingEmployee" class="text-red-500">*</span>
              </label>
              <input v-model="form.password" type="password" class="input-base" placeholder="********" :required="!editingEmployee" />
            </div>

            <div class="mb-4">
              <label class="block text-sm font-semibold text-slate-700 mb-2">الفروع <span class="text-red-500">*</span></label>
              <div class="space-y-2 max-h-40 overflow-y-auto p-3 bg-surface-50 rounded-xl">
                <label
                  v-for="branch in branches"
                  :key="branch.id"
                  class="flex items-center gap-3 cursor-pointer group"
                >
                  <input
                    type="checkbox"
                    :value="branch.id"
                    v-model="form.branch_ids"
                    class="w-4 h-4 rounded border-surface-300 text-primary-600 focus:ring-primary-500"
                  />
                  <span class="text-sm text-slate-700 group-hover:text-slate-900">{{ branch.name }}</span>
                </label>
                <p v-if="branches.length === 0" class="text-sm text-slate-400 text-center py-2">لا توجد فروع. قم بإضافة فرع أولاً</p>
              </div>
            </div>

            <div class="mb-6">
              <label class="block text-sm font-semibold text-slate-700 mb-2">الأدوار <span class="text-red-500">*</span></label>
              <div class="space-y-2 max-h-40 overflow-y-auto p-3 bg-surface-50 rounded-xl">
                <label
                  v-for="role in roles.filter(r => r.name != 'teacher')"
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

            <div class="flex gap-3">
              <button type="submit" class="btn-primary flex-1" :disabled="form.processing">
                {{ form.processing ? 'جاري الحفظ...' : (editingEmployee ? 'تحديث' : 'إضافة') }}
              </button>
              <button type="button" @click="closeModal" class="btn-ghost px-6">إلغاء</button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

    <!-- Add Teacher Modal -->
    <Teleport to="body">
      <div v-if="showTeacherModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="closeTeacherModal">
        <div class="fixed inset-0 bg-black/30 backdrop-blur-sm" @click="closeTeacherModal"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg p-8 animate-scale-in max-h-[90vh] overflow-y-auto">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
              <span class="text-lg">👨‍🏫</span>
            </div>
            <h3 class="text-xl font-bold text-slate-900">إضافة مدرس جديد</h3>
          </div>

          <form @submit.prevent="submitTeacherForm">
            <div class="mb-4">
              <label class="block text-sm font-semibold text-slate-700 mb-1.5">الاسم <span class="text-red-500">*</span></label>
              <input v-model="teacherForm.name" type="text" class="input-base" placeholder="أدخل اسم المدرس" required />
            </div>

            <div class="mb-4">
              <label class="block text-sm font-semibold text-slate-700 mb-1.5">البريد الإلكتروني <span class="text-red-500">*</span></label>
              <input v-model="teacherForm.email" type="email" class="input-base" placeholder="teacher@example.com" required />
            </div>

            <div class="mb-4">
              <label class="block text-sm font-semibold text-slate-700 mb-1.5">كلمة المرور <span class="text-red-500">*</span></label>
              <input v-model="teacherForm.password" type="password" class="input-base" placeholder="********" required />
            </div>

            <div class="mb-4">
              <label class="block text-sm font-semibold text-slate-700 mb-1.5">رقم الهاتف</label>
              <input v-model="teacherForm.phone" type="text" class="input-base" placeholder="05XXXXXXXX" />
            </div>

            <div class="mb-4">
              <label class="block text-sm font-semibold text-slate-700 mb-1.5">المبحث <span class="text-red-500">*</span></label>
              <select v-model="teacherForm.subject_id" class="input-base" required>
                <option value="" disabled>اختر المبحث</option>
                <option v-for="subject in subjects" :key="subject.id" :value="subject.id">{{ subject.name }}</option>
              </select>
            </div>

            <div class="mb-4">
              <label class="block text-sm font-semibold text-slate-700 mb-1.5">سنوات الخبرة</label>
              <input v-model="teacherForm.experience_years" type="number" min="0" class="input-base" placeholder="0" />
            </div>

            <div class="mb-6">
              <label class="block text-sm font-semibold text-slate-700 mb-2">الفروع <span class="text-red-500">*</span></label>
              <div class="space-y-2 max-h-40 overflow-y-auto p-3 bg-surface-50 rounded-xl">
                <label
                  v-for="branch in branches"
                  :key="branch.id"
                  class="flex items-center gap-3 cursor-pointer group"
                >
                  <input
                    type="checkbox"
                    :value="branch.id"
                    v-model="teacherForm.branch_ids"
                    class="w-4 h-4 rounded border-surface-300 text-primary-600 focus:ring-primary-500"
                  />
                  <span class="text-sm text-slate-700 group-hover:text-slate-900">{{ branch.name }}</span>
                </label>
                <p v-if="branches.length === 0" class="text-sm text-slate-400 text-center py-2">لا توجد فروع. قم بإضافة فرع أولاً</p>
              </div>
            </div>

            <div class="flex gap-3">
              <button type="submit" class="btn-primary flex-1" style="background-color: #059669;" :disabled="teacherForm.processing">
                {{ teacherForm.processing ? 'جاري الإنشاء...' : 'إنشاء وتوظيف المدرس' }}
              </button>
              <button type="button" @click="closeTeacherModal" class="btn-ghost px-6">إلغاء</button>
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
import axios from 'axios'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import Alert from '@/Components/Alert.vue'
import TableSearch from '@/Components/TableSearch.vue'

const props = defineProps({
  employees: Array,
  branches: Array,
  roles: Array,
  subjects: Array,
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

const filteredList = computed(() => props.employees.filter(item => matchesSearch(item, search.value)))

const showModal = ref(false)
const showTeacherModal = ref(false)
const editingEmployee = ref(null)
const showReportModal = ref(false)
const reportEmployee = ref(null)
const reportDate = ref(new Date().toISOString().split('T')[0])
const reportData = ref(null)
const reportLoading = ref(false)

const form = useForm({
  name: '',
  email: '',
  password: '',
  branch_ids: [],
  role_ids: [],
})

const teacherForm = useForm({
  name: '',
  email: '',
  password: '',
  phone: '',
  subject_id: '',
  experience_years: 0,
  branch_ids: [],
})

const roleLabel = (name) => {
  const labels = {
    admin: 'إداري',
    school: 'مدرسة',
    teacher: 'مدرس',
    employee: 'موظف',
  }
  return labels[name] || name
}

const openAddModal = () => {
  editingEmployee.value = null
  form.name = ''
  form.email = ''
  form.password = ''
  form.branch_ids = []
  form.role_ids = []
  showModal.value = true
}

const openEditModal = (employee) => {
  editingEmployee.value = employee
  form.name = employee.name
  form.email = employee.email
  form.password = ''
  form.branch_ids = employee.branches?.map(b => b.id) || []
  form.role_ids = employee.roles?.map(r => r.id) || []
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  form.reset()
}

const openTeacherModal = () => {
  teacherForm.name = ''
  teacherForm.email = ''
  teacherForm.password = ''
  teacherForm.phone = ''
  teacherForm.subject_id = ''
  teacherForm.experience_years = 0
  teacherForm.branch_ids = []
  showTeacherModal.value = true
}

const closeTeacherModal = () => {
  showTeacherModal.value = false
  teacherForm.reset()
}

const openReport = async (employee) => {
  reportEmployee.value = employee
  reportDate.value = new Date().toISOString().split('T')[0]
  reportData.value = null
  showReportModal.value = true
  await loadReport()
}

const closeReportModal = () => {
  showReportModal.value = false
  reportEmployee.value = null
  reportData.value = null
}

const loadReport = async () => {
  if (!reportEmployee.value) return
  reportLoading.value = true
  try {
    const res = await axios.get(`/admin/employees/${reportEmployee.value.id}/tasks-report`, {
      params: { date: reportDate.value }
    })
    reportData.value = res.data
  } catch (e) {
    console.error(e)
  } finally {
    reportLoading.value = false
  }
}

const formatTime = (datetime) => {
  if (!datetime) return ''
  const d = new Date(datetime)
  const pad = (n) => String(n).padStart(2, '0')
  return `${pad(d.getHours())}:${pad(d.getMinutes())}`
}

const submitForm = () => {
  if (editingEmployee.value) {
    form.put(`/admin/employees/${editingEmployee.value.id}`, {
      onSuccess: () => closeModal(),
    })
  } else {
    form.post('/admin/employees', {
      onSuccess: () => closeModal(),
    })
  }
}

const submitTeacherForm = () => {
  teacherForm.post('/admin/employees/teacher', {
    onSuccess: () => closeTeacherModal(),
  })
}

const confirmDelete = (employee) => {
  if (confirm(`هل أنت متأكد من حذف "${employee.name}"؟`)) {
    useForm({}).delete(`/admin/employees/${employee.id}`, {
      onSuccess: () => {},
    })
  }
}
</script>
