<template>
  <DashboardLayout>
    <div>
      <div class="mb-8 animate-fade-in-up">
        <h1 class="text-3xl font-extrabold text-slate-900">المهام العامة</h1>
        <p class="text-slate-500 mt-1">المهام الموكلة إليك</p>
      </div>

      <Alert v-if="flash.success" variant="success" :title="flash.success" closeable class="mb-6 animate-fade-in-down" />
      <Alert v-if="flash.error" variant="error" :title="flash.error" closeable class="mb-6 animate-fade-in-down" />

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 animate-fade-in-up animate-delay-50">
        <div class="card p-5 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-primary-100 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
          </div>
          <div>
            <p class="text-sm text-slate-500">إجمالي المهام</p>
            <p class="text-2xl font-bold text-slate-900">{{ totalCount }}</p>
          </div>
        </div>
        <div class="card p-5 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div>
            <p class="text-sm text-slate-500">تم الإنجاز</p>
            <p class="text-2xl font-bold text-emerald-600">{{ completedCount }}</p>
          </div>
        </div>
        <div class="card p-5 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div>
            <p class="text-sm text-slate-500">المتبقي</p>
            <p class="text-2xl font-bold text-amber-600">{{ totalCount - completedCount }}</p>
          </div>
        </div>
      </div>

      <div class="space-y-3 animate-fade-in-up animate-delay-100">
        <div
          v-for="task in tasks"
          :key="task.id"
          class="card p-5 transition-all duration-200"
          :class="task.completed ? 'bg-emerald-50/50 border-emerald-200' : ''"
        >
          <div class="flex items-start gap-4">
            <div
              v-if="!task.attachment_required || task.completed"
              class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 cursor-pointer transition-all duration-200 mt-0.5"
              :class="task.completed ? 'bg-emerald-100 text-emerald-600' : 'bg-surface-100 text-slate-400 hover:bg-primary-100 hover:text-primary-600'"
              @click="task.completed ? undoTask(task) : completeTask(task)"
            >
              <svg v-if="task.completed" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
            </div>
            <div v-else class="w-10 h-10 shrink-0"></div>

            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 flex-wrap">
                <h3 class="font-bold text-slate-900" :class="task.completed ? 'line-through text-slate-400' : ''">{{ task.name }}</h3>
                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium" :class="statusClass(task)">
                  {{ statusLabel(task) }}
                </span>
                <span v-if="task.attachment_required && !task.completed" class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-600">مرفق مطلوب</span>
              </div>
              <p v-if="task.description" class="text-sm text-slate-500 mt-0.5">{{ task.description }}</p>
              <div class="flex gap-3 mt-1.5 text-xs text-slate-400" dir="ltr">
                <span>من {{ formatDateTime(task.start_at) }}</span>
                <span>إلى {{ formatDateTime(task.end_at) }}</span>
              </div>

              <!-- Attachment Names (labels for upload) -->
              <div v-if="task.attachments?.length && !task.completed" class="mt-2 flex flex-wrap gap-2">
                <span
                  v-for="att in task.attachments"
                  :key="att.id"
                  class="inline-flex items-center gap-1.5 text-sm text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg"
                >
                  <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                  </svg>
                  {{ att.name }}
                </span>
              </div>

              <!-- Completion Attachments (when completed) -->
              <div v-if="task.completed && task.attachments?.length" class="mt-2 space-y-1">
                <div v-for="att in task.attachments" :key="att.id" class="flex items-center gap-2">
                  <span class="text-xs text-slate-500">{{ att.name }}:</span>
                  <a
                    v-if="task.user_completions?.[att.id]"
                    :href="`/storage/${task.user_completions[att.id].file_path}`"
                    target="_blank"
                    class="inline-flex items-center gap-1 text-sm text-emerald-600 hover:text-emerald-800 transition-colors"
                  >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    المرفق المرسل
                  </a>
                  <span v-else class="text-xs text-slate-400">—</span>
                </div>
              </div>

              <!-- Upload Fields (when not completed, attachment_required with attachments) -->
              <div v-if="!task.completed && task.attachment_required && task.attachments?.length && uploadingTaskId === task.id" class="mt-3 p-3 bg-amber-50 rounded-xl border border-amber-200 space-y-3">
                <p class="text-sm font-semibold text-slate-700">ارفع الملفات المطلوبة:</p>
                <div v-for="att in task.attachments" :key="att.id" class="flex items-center gap-3">
                  <label class="text-sm text-slate-600 min-w-[120px]">{{ att.name }}</label>
                  <input
                    type="file"
                    @change="(e) => setCompletionFile(task.id, att.id, e.target.files[0])"
                    class="flex-1 text-sm text-slate-600 file:ml-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-100 file:text-amber-700 hover:file:bg-amber-200 transition-all"
                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.zip"
                  />
                </div>
                <div class="flex gap-2 pt-1">
                  <button @click="submitWithAttachments(task)" class="btn-primary text-sm px-4 py-2" :disabled="uploading">
                    {{ uploading ? 'جاري الرفع...' : 'إرسال وإتمام المهمة' }}
                  </button>
                  <button @click="cancelUpload" class="btn-ghost text-sm px-4 py-2">إلغاء</button>
                </div>
              </div>
            </div>

            <div class="shrink-0 text-left">
              <p v-if="task.completed && task.completed_at" class="text-xs text-slate-400" dir="ltr">
                تم في {{ formatTime(task.completed_at) }}
              </p>
              <button
                v-if="!task.completed && task.attachment_required && task.attachments?.length && uploadingTaskId !== task.id"
                @click="startUpload(task)"
                class="btn-primary text-sm px-4 py-2"
              >إنجاز</button>
              <button
                v-else-if="!task.completed && task.attachment_required && !task.attachments?.length"
                @click="completeTask(task)"
                class="btn-primary text-sm px-4 py-2"
              >إنجاز</button>
              <button
                v-else-if="!task.completed && !task.attachment_required"
                @click="completeTask(task)"
                class="btn-primary text-sm px-4 py-2"
              >إنجاز</button>
              <button
                v-else-if="task.completed"
                @click="undoTask(task)"
                class="btn-ghost text-sm px-4 py-2 text-amber-600"
              >تراجع</button>
            </div>
          </div>
        </div>

        <div v-if="tasks.length === 0" class="card p-12 text-center">
          <p class="text-slate-500">لا توجد مهام موكلة إليك حالياً</p>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import Alert from '@/Components/Alert.vue'

const props = defineProps({
  tasks: Array,
  completedCount: Number,
  totalCount: Number,
  now: String,
})

const page = usePage()
const flash = page.props.flash || {}

const uploadingTaskId = ref(null)
const completionFiles = ref({})
const uploading = ref(false)

const statusClass = (task) => {
  const now = new Date(props.now)
  const start = new Date(task.start_at)
  const end = new Date(task.end_at)
  if (now < start) return 'bg-amber-100 text-amber-700'
  if (now > end) return 'bg-red-100 text-red-700'
  return 'bg-emerald-100 text-emerald-700'
}

const statusLabel = (task) => {
  const now = new Date(props.now)
  const start = new Date(task.start_at)
  const end = new Date(task.end_at)
  if (now < start) return 'قادمة'
  if (now > end) return 'منتهية'
  return 'نشط'
}

const formatDateTime = (dt) => {
  if (!dt) return '-'
  const d = new Date(dt)
  const pad = (n) => String(n).padStart(2, '0')
  return `${d.getFullYear()}/${pad(d.getMonth() + 1)}/${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}`
}

const formatTime = (dt) => {
  if (!dt) return ''
  const d = new Date(dt)
  const pad = (n) => String(n).padStart(2, '0')
  return `${pad(d.getHours())}:${pad(d.getMinutes())}`
}

const startUpload = (task) => {
  uploadingTaskId.value = task.id
  completionFiles.value = {}
}

const cancelUpload = () => {
  uploadingTaskId.value = null
  completionFiles.value = {}
}

const setCompletionFile = (taskId, attachmentId, file) => {
  if (!completionFiles.value[taskId]) {
    completionFiles.value[taskId] = {}
  }
  completionFiles.value[taskId][attachmentId] = file
}

const completeTask = (task) => {
  useForm({}).post(`/general-tasks/${task.id}/complete`, { preserveScroll: true })
}

const submitWithAttachments = (task) => {
  const files = completionFiles.value[task.id] || {}
  const formData = {}
  for (const [attId, file] of Object.entries(files)) {
    formData[`completion_files[${attId}]`] = file
  }

  uploading.value = true
  const form = useForm(formData)
  form.post(`/general-tasks/${task.id}/complete`, {
    preserveScroll: true,
    onFinish: () => {
      uploading.value = false
      uploadingTaskId.value = null
      completionFiles.value = {}
    },
  })
}

const undoTask = (task) => {
  useForm({}).post(`/general-tasks/${task.id}/undo`, { preserveScroll: true })
}
</script>
