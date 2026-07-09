<template>
  <DashboardLayout>
    <div>
      <div class="flex items-center justify-between mb-8 animate-fade-in-up">
        <div>
          <h1 class="text-3xl font-extrabold text-slate-900">أسئلة المقابلات</h1>
          <p class="text-slate-500 mt-1">إدارة أسئلة مقابلات التوظيف</p>
        </div>
        <button @click="openAddModal" class="btn-primary">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          إضافة سؤال
        </button>
      </div>

      <Alert v-if="flash.success" variant="success" :title="flash.success" closeable class="mb-6 animate-fade-in-down" />

      <div class="card overflow-hidden animate-fade-in-up animate-delay-100">
        <div class="p-6 border-b border-surface-200 flex items-center justify-between">
          <TableSearch v-model="search" />
        </div>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-surface-50 border-b border-surface-200">
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">#</th>
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">السؤال</th>
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">التصنيف</th>
                <th class="px-6 py-4 text-center text-sm font-bold text-slate-700">الترتيب</th>
                <th class="px-6 py-4 text-center text-sm font-bold text-slate-700">الإجراءات</th>
              </tr>
            </thead>
            <tbody ref="tbodyRef" class="divide-y divide-surface-100">
              <tr v-for="(q, index) in filteredList" :key="q.id" :data-id="q.id" class="hover:bg-primary-50/50 transition-colors">
                <td class="px-6 py-4">
                  <span class="drag-handle cursor-grab active:cursor-grabbing ml-2 text-slate-400 hover:text-slate-600 select-none text-lg" title="اسحب لإعادة الترتيب">⠿</span>
                  <span class="text-slate-500 font-medium">{{ index + 1 }}</span>
                </td>
                <td class="px-6 py-4 font-medium text-slate-900">{{ q.question }}</td>
                <td class="px-6 py-4 text-slate-600">{{ q.category || '-' }}</td>
                <td class="px-6 py-4 text-center text-slate-600">{{ q.sort_order }}</td>
                <td class="px-6 py-4">
                  <div class="flex gap-2 justify-center">
                    <button @click="openEditModal(q)" class="p-2 rounded-xl bg-primary-100 text-primary-700 hover:bg-primary-200 transition-all duration-200" title="تعديل">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                    </button>
                    <button @click="confirmDelete(q)" class="p-2 rounded-xl bg-red-100 text-red-700 hover:bg-red-200 transition-all duration-200" title="حذف">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="filteredList.length === 0">
                <td colspan="5" class="px-6 py-12 text-center text-slate-500">لا توجد أسئلة مضافة بعد</td>
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
              <h3 class="text-xl font-bold text-slate-900">{{ editingQuestion ? 'تعديل السؤال' : 'إضافة سؤال جديد' }}</h3>
            </div>

            <form @submit.prevent="submitForm">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1.5">السؤال <span class="text-red-500">*</span></label>
                  <textarea
                    v-model="form.question"
                    class="input-base min-h-[80px]"
                    placeholder="أدخل نص السؤال"
                    required
                  ></textarea>
                </div>

                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1.5">التصنيف</label>
                  <input
                    v-model="form.category"
                    type="text"
                    class="input-base"
                    placeholder="مثال: مهارات تدريسية, شخصية, ..."
                  />
                </div>
              </div>

              <div class="flex gap-3 mt-8">
                <button type="submit" class="btn-primary flex-1" :disabled="form.processing">
                  {{ form.processing ? 'جاري الحفظ...' : (editingQuestion ? 'تحديث' : 'إضافة') }}
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
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'
import { useForm, usePage, router } from '@inertiajs/vue3'
import Sortable from 'sortablejs'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import Alert from '@/Components/Alert.vue'
import TableSearch from '@/Components/TableSearch.vue'

const props = defineProps({
  questions: Array,
})

const page = usePage()
const flash = page.props.flash || {}

const search = ref('')
const tbodyRef = ref(null)
let sortableInstance = null

onMounted(() => {
  nextTick(() => {
    if (tbodyRef.value) {
      sortableInstance = new Sortable(tbodyRef.value, {
        handle: '.drag-handle',
        animation: 150,
        easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
        ghostClass: 'bg-primary-100',
        dragClass: 'opacity-80',
        onEnd() {
          const items = [...tbodyRef.value.querySelectorAll('tr[data-id]')].map((tr, i) => ({
            id: parseInt(tr.dataset.id),
            sort_order: i + 1,
          }))
          router.put('/admin/interview-questions/reorder', { items }, {
            preserveScroll: true,
          })
        },
      })
    }
  })
})

onUnmounted(() => {
  if (sortableInstance) {
    sortableInstance.destroy()
    sortableInstance = null
  }
})

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

const filteredList = computed(() => props.questions.filter(item => matchesSearch(item, search.value)))

const showModal = ref(false)
const editingQuestion = ref(null)

const form = useForm({
  question: '',
  category: '',
  sort_order: 0,
})

const openAddModal = () => {
  editingQuestion.value = null
  form.reset()
  form.sort_order = props.questions.length > 0
    ? Math.max(...props.questions.map(q => q.sort_order)) + 1
    : 1
  showModal.value = true
}

const openEditModal = (q) => {
  editingQuestion.value = q
  form.question = q.question
  form.category = q.category || ''
  form.sort_order = q.sort_order
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  form.reset()
}

const submitForm = () => {
  if (editingQuestion.value) {
    form.put(`/admin/interview-questions/${editingQuestion.value.id}`, {
      onSuccess: () => closeModal(),
    })
  } else {
    form.post('/admin/interview-questions', {
      onSuccess: () => closeModal(),
    })
  }
}

const confirmDelete = (q) => {
  if (confirm(`هل أنت متأكد من حذف "${q.question}"؟`)) {
    useForm({}).delete(`/admin/interview-questions/${q.id}`)
  }
}
</script>
