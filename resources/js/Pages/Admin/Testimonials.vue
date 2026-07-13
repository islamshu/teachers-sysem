<template>
  <DashboardLayout>
    <div>
      <div class="flex items-center justify-between mb-8 animate-fade-in-up">
        <div>
          <h1 class="text-3xl font-extrabold text-slate-900">آراء المستخدمين</h1>
          <p class="text-slate-500 mt-1">إدارة شهادات وآراء المستخدمين في الصفحة الرئيسية</p>
        </div>
        <button @click="openAddModal" class="btn-primary">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          إضافة رأي
        </button>
      </div>

      <Alert v-if="flash.success" variant="success" :title="flash.success" closeable class="mb-6 animate-fade-in-down" />

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-fade-in-up animate-delay-100">
        <div v-for="testimonial in testimonials" :key="testimonial.id" class="card p-6 relative group">
          <div class="flex items-start gap-4 mb-4">
            <div class="w-14 h-14 rounded-full bg-primary-100 flex-shrink-0 overflow-hidden">
              <img v-if="testimonial.avatar" :src="`/storage/${testimonial.avatar}`" class="w-full h-full object-cover" />
              <div v-else class="w-full h-full flex items-center justify-center text-primary-600 text-xl font-bold">{{ testimonial.name.charAt(0) }}</div>
            </div>
            <div class="min-w-0 flex-1">
              <div class="font-bold text-slate-900 truncate">{{ testimonial.name }}</div>
              <div v-if="testimonial.role" class="text-sm text-slate-500 truncate">{{ testimonial.role }}</div>
            </div>
            <span :class="testimonial.is_active ? 'badge-success' : 'badge bg-surface-200 text-surface-600'" class="flex-shrink-0">
              {{ testimonial.is_active ? 'نشط' : 'غير نشط' }}
            </span>
          </div>
          <p class="text-slate-600 leading-relaxed text-sm line-clamp-4">{{ testimonial.content }}</p>
          <div class="absolute top-2 left-2 opacity-0 group-hover:opacity-100 transition-opacity flex gap-1">
            <button @click="openEditModal(testimonial)" class="p-1.5 rounded-lg bg-primary-100 text-primary-700 hover:bg-primary-200 transition-all" title="تعديل">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
            </button>
            <button @click="confirmDelete(testimonial)" class="p-1.5 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 transition-all" title="حذف">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            </button>
          </div>
        </div>
        <div v-if="testimonials.length === 0" class="col-span-full card p-12 text-center text-slate-500">
          لا توجد آراء مضافة بعد
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
              <h3 class="text-xl font-bold text-slate-900">{{ editingTestimonial ? 'تعديل الرأي' : 'إضافة رأي جديد' }}</h3>
            </div>

            <form @submit.prevent="submitForm">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1.5">الاسم <span class="text-red-500">*</span></label>
                  <input v-model="form.name" type="text" class="input-base" required />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1.5">المسمى / الدور</label>
                  <input v-model="form.role" type="text" class="input-base" placeholder="مثال: مدرس لغة عربية" />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1.5">النص <span class="text-red-500">*</span></label>
                  <textarea v-model="form.content" class="input-base min-h-[100px]" required></textarea>
                </div>
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1.5">الصورة الرمزية</label>
                  <input ref="fileInput" @change="onAvatarChange" type="file" accept="image/png,image/jpg,image/jpeg,image/webp" class="input-base" />
                  <div v-if="avatarPreview || (editingTestimonial && editingTestimonial.avatar)" class="mt-3">
                    <img :src="avatarPreview || `/storage/${editingTestimonial.avatar}`" class="h-16 w-16 rounded-full object-cover border border-surface-200" />
                  </div>
                </div>
                <div class="flex items-center gap-3">
                  <input v-model="form.is_active" type="checkbox" id="is_active" class="w-5 h-5 rounded border-surface-300 text-primary-600 focus:ring-primary-500" />
                  <label for="is_active" class="text-sm font-semibold text-slate-700">رأي نشط</label>
                </div>
              </div>

              <div class="flex gap-3 mt-8">
                <button type="submit" class="btn-primary flex-1" :disabled="form.processing">
                  {{ form.processing ? 'جاري الحفظ...' : (editingTestimonial ? 'تحديث' : 'إضافة') }}
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
  testimonials: Array,
})

const page = usePage()
const flash = page.props.flash || {}

const showModal = ref(false)
const editingTestimonial = ref(null)
const avatarPreview = ref(null)
const fileInput = ref(null)

const form = useForm({
  name: '',
  role: '',
  content: '',
  avatar: null,
  is_active: true,
})

const onAvatarChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    form.avatar = file
    const reader = new FileReader()
    reader.onload = (ev) => { avatarPreview.value = ev.target.result }
    reader.readAsDataURL(file)
  }
}

const openAddModal = () => {
  editingTestimonial.value = null
  form.reset()
  form.is_active = true
  avatarPreview.value = null
  showModal.value = true
}

const openEditModal = (testimonial) => {
  editingTestimonial.value = testimonial
  form.avatar = null
  form.name = testimonial.name || ''
  form.role = testimonial.role || ''
  form.content = testimonial.content || ''
  form.is_active = testimonial.is_active
  avatarPreview.value = null
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  form.reset()
  avatarPreview.value = null
}

const submitForm = () => {
  if (editingTestimonial.value) {
    form.post(`/admin/testimonials/${editingTestimonial.value.id}`, {
      onSuccess: () => closeModal(),
      preserveScroll: true,
    })
  } else {
    form.post('/admin/testimonials', {
      onSuccess: () => closeModal(),
      preserveScroll: true,
    })
  }
}

const confirmDelete = (testimonial) => {
  if (confirm(`هل أنت متأكد من حذف رأي "${testimonial.name}"؟`)) {
    useForm({}).delete(`/admin/testimonials/${testimonial.id}`, {
      preserveScroll: true,
    })
  }
}
</script>
