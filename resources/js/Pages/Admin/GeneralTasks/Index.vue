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
                <td class="px-6 py-4 font-medium text-slate-900">
                  <div class="flex items-center gap-1.5">
                    {{ task.name }}
                    <span v-if="task.attachments?.length" class="inline-flex items-center gap-0.5 text-xs text-sky-600" :title="`${task.attachments.length} مرفق(ات)`">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                      </svg>
                      {{ task.attachments.length }}
                    </span>
                    <span v-if="task.attachment_required" class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-600">مطلوب</span>
                  </div>
                </td>
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
              <label class="block text-sm font-semibold text-slate-700 mb-1.5">الموظفون المستهدفون</label>
              <MultiSelect
                v-model="form.user_ids"
                :options="employees.map(e => ({ id: e.id, label: e.name + ' — ' + e.email }))"
                placeholder="اختر الموظفين..."
                searchPlaceholder="بحث بالاسم أو البريد..."
              />
            </div>

            <div class="mb-5">
              <label class="block text-sm font-semibold text-slate-700 mb-1.5">الأدوار المستهدفة</label>
              <MultiSelect
                v-model="form.role_ids"
                :options="roles.map(r => ({ id: r.id, label: roleLabel(r.name) }))"
                placeholder="اختر الأدوار..."
                searchPlaceholder="بحث..."
              />
              <p class="text-xs text-slate-400 mt-1">* يجب اختيار موظف واحد على الأقل أو دور واحد</p>
            </div>

            <!-- Attachments Section -->
            <div class="mb-4 p-4 bg-surface-50 rounded-xl border border-surface-200">
              <label class="block text-sm font-semibold text-slate-700 mb-3">هل تريد إضافة مرفقات؟</label>
              <div class="flex gap-4 mb-3">
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="radio" :value="true" v-model="form.has_attachments" class="w-4 h-4 text-primary-600" />
                  <span class="text-sm text-slate-700">نعم</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="radio" :value="false" v-model="form.has_attachments" class="w-4 h-4 text-primary-600" />
                  <span class="text-sm text-slate-700">لا</span>
                </label>
              </div>

              <div v-if="form.has_attachments" class="space-y-3">
                <!-- Existing Attachments (edit mode) — name only -->
                <div
                  v-for="(att, index) in form.existing_attachments"
                  :key="'existing-' + att.id"
                  class="flex items-center gap-2 p-3 bg-emerald-50 rounded-lg border border-emerald-200"
                >
                  <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                  </svg>
                  <span class="text-sm text-emerald-700 font-medium flex-1">{{ att.name }}</span>
                  <button
                    type="button"
                    @click="removeExistingAttachment(index)"
                    class="p-1 rounded-lg text-red-500 hover:bg-red-100 transition-colors shrink-0"
                    title="حذف المرفق"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>

                <!-- New Attachment Rows (name only, no file) -->
                <div
                  v-for="(att, index) in form.attachments"
                  :key="'new-' + index"
                  class="flex items-center gap-2 p-3 bg-white rounded-lg border border-surface-200"
                >
                  <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                  </svg>
                  <input
                    v-model="att.name"
                    type="text"
                    class="flex-1 input-base text-sm"
                    placeholder="اسم المرفق (مثال: نموذج التقرير)"
                  />
                  <button
                    type="button"
                    @click="removeAttachment(index)"
                    class="p-2 rounded-lg text-red-500 hover:bg-red-100 transition-colors shrink-0"
                    title="إزالة"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>

                <button
                  type="button"
                  @click="addAttachment"
                  class="flex items-center gap-2 text-sm font-semibold text-primary-600 hover:text-primary-700 transition-colors"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                  </svg>
                  إضافة مرفق
                </button>

                <div class="mt-3 pt-3 border-t border-surface-200">
                  <label class="flex items-center gap-3 cursor-pointer group">
                    <input
                      type="checkbox"
                      v-model="form.attachment_required"
                      class="w-4 h-4 rounded border-surface-300 text-primary-600 focus:ring-primary-500"
                    />
                    <span class="text-sm text-slate-700 group-hover:text-slate-900">المستخدم مطلوب منه رفع مرفق عند إتمام المهمة</span>
                  </label>
                </div>
              </div>
            </div>

            <div class="flex gap-3">
              <button type="submit" class="btn-primary flex-1" :disabled="submitting">
                {{ submitting ? 'جاري الحفظ...' : (editingTask ? 'تحديث' : 'إضافة') }}
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
import { ref, reactive, computed } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import Alert from '@/Components/Alert.vue'
import TableSearch from '@/Components/TableSearch.vue'
import MultiSelect from '@/Components/MultiSelect.vue'

const props = defineProps({
  tasks: Array,
  employees: Array,
  roles: Array,
  now: String,
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

const defaultForm = () => ({
  name: '',
  description: '',
  start_at: '',
  end_at: '',
  user_ids: [],
  role_ids: [],
  has_attachments: false,
  attachments: [{ name: '', file: null }],
  attachment_required: false,
  existing_attachments: [],
  remove_attachment_ids: [],
})

const form = reactive(defaultForm())
const submitting = ref(false)

const roleLabel = (name) => {
  const labels = { admin: 'إداري', school: 'مدرسة', teacher: 'مدرس', employee: 'موظف' }
  return labels[name] || name
}

const formatDate = (dt) => {
  if (!dt) return '-'
  const d = new Date(dt)
  const pad = (n) => String(n).padStart(2, '0')
  return `${d.getFullYear()}/${pad(d.getMonth() + 1)}/${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}`
}

const taskStatusClass = (task) => {
  if (!task.is_active) return 'bg-slate-100 text-slate-500'
  const now = new Date(props.now)
  const start = new Date(task.start_at)
  const end = new Date(task.end_at)
  if (now < start) return 'bg-amber-100 text-amber-700'
  if (now > end) return 'bg-red-100 text-red-700'
  return 'bg-emerald-100 text-emerald-700'
}

const taskStatusLabel = (task) => {
  if (!task.is_active) return 'غير نشط'
  const now = new Date(props.now)
  const start = new Date(task.start_at)
  const end = new Date(task.end_at)
  if (now < start) return 'قادمة'
  if (now > end) return 'منتهية'
  return 'نشط'
}

const addAttachment = () => {
  form.attachments.push({ name: '', file: null })
}

const removeAttachment = (index) => {
  form.attachments.splice(index, 1)
  if (form.attachments.length === 0) {
    form.attachments.push({ name: '', file: null })
  }
}

const removeExistingAttachment = (index) => {
  const att = form.existing_attachments[index]
  if (att) {
    form.remove_attachment_ids.push(att.id)
    form.existing_attachments.splice(index, 1)
  }
}

const openAddModal = () => {
  editingTask.value = null
  Object.assign(form, defaultForm())
  showModal.value = true
}

const openEditModal = (task) => {
  editingTask.value = task
  const existingAtts = task.attachments || []
  Object.assign(form, {
    name: task.name,
    description: task.description || '',
    start_at: task.start_at ? task.start_at.slice(0, 16) : '',
    end_at: task.end_at ? task.end_at.slice(0, 16) : '',
    user_ids: task.assigned_users?.map(u => u.id) || [],
    role_ids: task.roles?.map(r => r.id) || [],
    has_attachments: existingAtts.length > 0,
    attachments: [{ name: '', file: null }],
    attachment_required: task.attachment_required || false,
    existing_attachments: existingAtts.map(a => ({ id: a.id, name: a.name, file_path: a.file_path })),
    remove_attachment_ids: [],
  })
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  Object.assign(form, defaultForm())
}

const buildFormData = () => {
  const fd = new FormData()
  fd.append('name', form.name || '')
  fd.append('description', form.description || '')
  fd.append('start_at', form.start_at || '')
  fd.append('end_at', form.end_at || '')
  fd.append('is_active', form.is_active ? '1' : '0')
  fd.append('has_attachments', form.has_attachments ? '1' : '0')
  fd.append('attachment_required', form.attachment_required ? '1' : '0')

  ;(form.user_ids || []).forEach((id, i) => fd.append(`user_ids[${i}]`, id))
  ;(form.role_ids || []).forEach((id, i) => fd.append(`role_ids[${i}]`, id))

  if (form.has_attachments) {
    let fileIndex = 0
    ;(form.attachments || []).forEach((att) => {
      if (att.name && att.name.trim()) {
        fd.append(`attachment_names[${fileIndex}]`, att.name.trim())
        fileIndex++
      }
    })
  }

  if (editingTask.value) {
    ;(form.remove_attachment_ids || []).forEach((id, i) => fd.append(`remove_attachment_ids[${i}]`, id))
    ;(form.existing_attachments || []).forEach((att, i) => fd.append(`existing_attachment_ids[${i}]`, att.id))
  }

  return fd
}

const submitForm = () => {
  submitting.value = true
  const fd = buildFormData()

  if (editingTask.value) {
    fd.append('_method', 'PUT')
    router.post(`/admin/general-tasks/${editingTask.value.id}`, fd, {
      forceFormData: true,
      onFinish: () => { submitting.value = false },
      onSuccess: () => closeModal(),
    })
  } else {
    router.post('/admin/general-tasks', fd, {
      forceFormData: true,
      onFinish: () => { submitting.value = false },
      onSuccess: () => closeModal(),
    })
  }
}

const confirmDelete = (task) => {
  if (confirm(`هل أنت متأكد من حذف "${task.name}"؟`)) {
    router.delete(`/admin/general-tasks/${task.id}`)
  }
}
</script>
