<template>
  <DashboardLayout>
    <div>
      <div class="flex items-center justify-between mb-8 animate-fade-in-up">
        <div>
          <h1 class="text-3xl font-extrabold text-slate-900">شرائح الصفحة الرئيسية</h1>
          <p class="text-slate-500 mt-1">إدارة شرائح السلايدر في الصفحة الرئيسية</p>
        </div>
        <button @click="openAddModal" class="btn-primary">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          إضافة شريحة
        </button>
      </div>

      <Alert v-if="flash.success" variant="success" :title="flash.success" closeable class="mb-6 animate-fade-in-down" />

      <div class="card overflow-hidden animate-fade-in-up animate-delay-100">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-surface-50 border-b border-surface-200">
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700 w-10"></th>
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">الصورة</th>
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">العنوان</th>
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">النص الفرعي</th>
                <th class="px-6 py-4 text-center text-sm font-bold text-slate-700">فعال</th>
                <th class="px-6 py-4 text-center text-sm font-bold text-slate-700">الإجراءات</th>
              </tr>
            </thead>
            <tbody ref="tbodyRef" class="divide-y divide-surface-100">
              <tr v-for="(slide, index) in slides" :key="slide.id" :data-id="slide.id" class="hover:bg-primary-50/50 transition-colors">
                <td class="px-6 py-4">
                  <span class="drag-handle cursor-grab active:cursor-grabbing text-slate-400 hover:text-slate-600 select-none text-lg">⠿</span>
                </td>
                <td class="px-6 py-4">
                  <img :src="`/storage/${slide.image}`" alt="Slide" class="w-24 h-16 rounded-lg object-cover border border-surface-200" />
                </td>
                <td class="px-6 py-4 font-medium text-slate-900 max-w-[200px] truncate">{{ slide.title || '-' }}</td>
                <td class="px-6 py-4 text-slate-600 max-w-[200px] truncate">{{ slide.subtitle || '-' }}</td>
                <td class="px-6 py-4 text-center">
                  <span :class="slide.is_active ? 'badge-success' : 'badge bg-surface-200 text-surface-600'">
                    {{ slide.is_active ? 'نعم' : 'لا' }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <div class="flex gap-2 justify-center">
                    <button @click="openEditModal(slide)" class="p-2 rounded-xl bg-primary-100 text-primary-700 hover:bg-primary-200 transition-all duration-200" title="تعديل">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                    </button>
                    <button @click="confirmDelete(slide)" class="p-2 rounded-xl bg-red-100 text-red-700 hover:bg-red-200 transition-all duration-200" title="حذف">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="slides.length === 0">
                <td colspan="6" class="px-6 py-12 text-center text-slate-500">لا توجد شرائح مضافة بعد</td>
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
              <h3 class="text-xl font-bold text-slate-900">{{ editingSlide ? 'تعديل الشريحة' : 'إضافة شريحة جديدة' }}</h3>
            </div>

            <form @submit.prevent="submitForm">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1.5">الصورة <span class="text-red-500">*</span></label>
                  <input
                    ref="fileInput"
                    @change="onImageChange"
                    type="file"
                    accept="image/png,image/jpg,image/jpeg,image/webp"
                    class="input-base"
                    :required="!editingSlide"
                  />
                  <div v-if="imagePreview || (editingSlide && editingSlide.image)" class="mt-3">
                    <img :src="imagePreview || `/storage/${editingSlide.image}`" alt="Preview" class="h-24 rounded-lg border border-surface-200" />
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1.5">العنوان</label>
                  <input
                    v-model="form.title"
                    type="text"
                    class="input-base"
                    placeholder="عنوان الشريحة"
                  />
                </div>

                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1.5">النص الفرعي</label>
                  <textarea
                    v-model="form.subtitle"
                    class="input-base min-h-[60px]"
                    placeholder="نص وصفي للشريحة"
                  ></textarea>
                </div>

                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1.5">الرابط</label>
                  <input
                    v-model="form.link"
                    type="text"
                    class="input-base"
                    placeholder="https://"
                    dir="ltr"
                  />
                </div>

                <div class="flex items-center gap-3">
                  <input
                    v-model="form.is_active"
                    type="checkbox"
                    id="is_active"
                    class="w-5 h-5 rounded border-surface-300 text-primary-600 focus:ring-primary-500"
                  />
                  <label for="is_active" class="text-sm font-semibold text-slate-700">شريحة نشطة</label>
                </div>
              </div>

              <div class="flex gap-3 mt-8">
                <button type="submit" class="btn-primary flex-1" :disabled="form.processing">
                  {{ form.processing ? 'جاري الحفظ...' : (editingSlide ? 'تحديث' : 'إضافة') }}
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
import { ref, onMounted, onUnmounted, nextTick } from 'vue'
import { useForm, usePage, router } from '@inertiajs/vue3'
import Sortable from 'sortablejs'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import Alert from '@/Components/Alert.vue'

const props = defineProps({
  slides: Array,
})

const page = usePage()
const flash = page.props.flash || {}

const tbodyRef = ref(null)
const fileInput = ref(null)
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
          router.post('/admin/slides/reorder', { items }, {
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

const showModal = ref(false)
const editingSlide = ref(null)
const imagePreview = ref(null)

const form = useForm({
  image: null,
  title: '',
  subtitle: '',
  link: '',
  is_active: true,
})

const onImageChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    form.image = file
    const reader = new FileReader()
    reader.onload = (ev) => { imagePreview.value = ev.target.result }
    reader.readAsDataURL(file)
  }
}

const openAddModal = () => {
  editingSlide.value = null
  form.reset()
  form.is_active = true
  imagePreview.value = null
  showModal.value = true
}

const openEditModal = (slide) => {
  editingSlide.value = slide
  form.image = null
  form.title = slide.title || ''
  form.subtitle = slide.subtitle || ''
  form.link = slide.link || ''
  form.is_active = slide.is_active
  imagePreview.value = null
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  form.reset()
  imagePreview.value = null
}

const submitForm = () => {
  if (editingSlide.value) {
    form.post(`/admin/slides/${editingSlide.value.id}`, {
      onSuccess: () => closeModal(),
      preserveScroll: true,
    })
  } else {
    form.post('/admin/slides', {
      onSuccess: () => closeModal(),
      preserveScroll: true,
    })
  }
}

const confirmDelete = (slide) => {
  if (confirm(`هل أنت متأكد من حذف "${slide.title || 'هذه الشريحة'}"؟`)) {
    useForm({}).delete(`/admin/slides/${slide.id}`, {
      preserveScroll: true,
    })
  }
}
</script>
