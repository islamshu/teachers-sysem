<template>
  <DashboardLayout>
    <div class="max-w-4xl mx-auto">
      <div class="relative mb-8 overflow-hidden rounded-3xl bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 shadow-2xl shadow-primary-500/20 animate-fade-in-up">
        <div class="absolute inset-0 bg-grid opacity-20"></div>
        <div class="relative px-8 py-10">
          <div class="flex items-center gap-4 mb-3">
            <div class="w-14 h-14 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center text-3xl shadow-lg">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </div>
            <div>
              <h1 class="text-3xl font-extrabold text-white">إضافة مدرس جديد</h1>
              <p class="text-primary-200">إنشاء حساب مدرس جديد مع بياناته الشخصية والمهنية</p>
            </div>
          </div>
        </div>
      </div>

      <Alert v-if="form.errors.name || form.errors.email || form.errors.password" variant="error" title="خطأ في بيانات المستخدم" class="mb-6 animate-fade-in-down" />

      <div class="card animate-fade-in-up animate-delay-100">
        <form @submit.prevent="submit" class="p-8 space-y-8">
          <!-- Account Information -->
          <div>
            <div class="flex items-center gap-3 mb-6">
              <div class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
              <h3 class="text-lg font-bold text-slate-900">بيانات الحساب</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">الاسم <span class="text-red-500">*</span></label>
                <input v-model="form.name" type="text" class="input-base" :class="{ 'input-error': form.errors.name }" placeholder="الاسم الكامل" />
                <p v-if="form.errors.name" class="mt-1.5 text-sm text-red-500 font-medium">{{ form.errors.name }}</p>
              </div>
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">البريد الإلكتروني <span class="text-red-500">*</span></label>
                <input v-model="form.email" type="email" class="input-base" :class="{ 'input-error': form.errors.email }" placeholder="email@example.com" />
                <p v-if="form.errors.email" class="mt-1.5 text-sm text-red-500 font-medium">{{ form.errors.email }}</p>
              </div>
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">كلمة المرور <span class="text-red-500">*</span></label>
                <input v-model="form.password" type="password" class="input-base" :class="{ 'input-error': form.errors.password }" placeholder="أدخل كلمة المرور" />
                <p v-if="form.errors.password" class="mt-1.5 text-sm text-red-500 font-medium">{{ form.errors.password }}</p>
              </div>
            </div>
          </div>

          <div class="divider"></div>

          <!-- Photo -->
          <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">الصورة الشخصية</label>
            <div class="relative">
              <div class="flex items-center gap-4">
                <div class="w-24 h-24 rounded-2xl bg-surface-100 overflow-hidden flex-shrink-0 border-2 border-dashed border-surface-300">
                  <img v-if="photoPreview" :src="photoPreview" class="w-full h-full object-cover" />
                  <div v-else class="w-full h-full flex items-center justify-center text-3xl text-surface-400">📷</div>
                </div>
                <div class="flex-1">
                  <label class="btn-secondary cursor-pointer inline-flex">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    اختر صورة
                    <input @change="handlePhotoChange" type="file" accept="image/*" class="hidden" />
                  </label>
                  <p class="text-xs text-slate-400 mt-2">JPG, JPEG, PNG, WebP</p>
                </div>
              </div>
            </div>
            <p v-if="form.errors.photo" class="mt-2 text-sm text-red-500 font-medium">{{ form.errors.photo }}</p>
          </div>

          <!-- Personal Information -->
          <div>
            <div class="flex items-center gap-3 mb-6">
              <div class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
              <h3 class="text-lg font-bold text-slate-900">البيانات الشخصية</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">الهاتف <span class="text-red-500">*</span></label>
                <input v-model="form.phone" type="tel" class="input-base" :class="{ 'input-error': form.errors.phone }" placeholder="05xxxxxxxxx" />
                <p v-if="form.errors.phone" class="mt-1.5 text-sm text-red-500 font-medium">{{ form.errors.phone }}</p>
              </div>
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">تاريخ الميلاد <span class="text-red-500">*</span></label>
                <input v-model="form.birth_date" type="date" class="input-base" :class="{ 'input-error': form.errors.birth_date }" />
                <p v-if="form.errors.birth_date" class="mt-1.5 text-sm text-red-500 font-medium">{{ form.errors.birth_date }}</p>
              </div>
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">الجنس <span class="text-red-500">*</span></label>
                <select v-model="form.gender" class="input-base" :class="{ 'input-error': form.errors.gender }">
                  <option value="">اختر</option>
                  <option value="male">ذكر</option>
                  <option value="female">أنثى</option>
                </select>
                <p v-if="form.errors.gender" class="mt-1.5 text-sm text-red-500 font-medium">{{ form.errors.gender }}</p>
              </div>
            </div>
          </div>

          <!-- Academic Information -->
          <div class="divider"></div>

          <div>
            <div class="flex items-center gap-3 mb-6">
              <div class="w-10 h-10 rounded-xl bg-warm-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-warm-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 7l-9-5 9-5 9 5-9 5z" />
                </svg>
              </div>
              <h3 class="text-lg font-bold text-slate-900">البيانات الأكاديمية</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">المادة <span class="text-red-500">*</span></label>
                <select v-model="form.subject_id" class="input-base" :class="{ 'input-error': form.errors.subject_id }">
                  <option value="">اختر المادة</option>
                  <option v-for="subject in subjects" :key="subject.id" :value="subject.id">{{ subject.name }}</option>
                </select>
                <p v-if="form.errors.subject_id" class="mt-1.5 text-sm text-red-500 font-medium">{{ form.errors.subject_id }}</p>
              </div>
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">سنوات الخبرة <span class="text-red-500">*</span></label>
                <input v-model="form.experience_years" type="number" min="0" max="60" class="input-base" :class="{ 'input-error': form.errors.experience_years }" />
                <p v-if="form.errors.experience_years" class="mt-1.5 text-sm text-red-500 font-medium">{{ form.errors.experience_years }}</p>
              </div>
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">المؤهل العلمي <span class="text-red-500">*</span></label>
                <input v-model="form.qualification" type="text" class="input-base" :class="{ 'input-error': form.errors.qualification }" placeholder="بكالوريوس ..." />
                <p v-if="form.errors.qualification" class="mt-1.5 text-sm text-red-500 font-medium">{{ form.errors.qualification }}</p>
              </div>
            </div>

            <div class="mt-5">
              <label class="block text-sm font-semibold text-slate-700 mb-3">الصفوف الدراسية <span class="text-red-500">*</span></label>
              <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                <label
                  v-for="grade in grades"
                  :key="grade.id"
                  class="relative flex items-center gap-3 p-3 rounded-xl border-2 border-surface-200 cursor-pointer transition-all duration-200"
                  :class="{ 'border-primary-400 bg-primary-50': form.grade_ids.includes(grade.id) }"
                >
                  <input type="checkbox" :value="grade.id" v-model="form.grade_ids" class="w-4 h-4 rounded border-surface-300 text-primary-600 focus:ring-primary-500" />
                  <span class="text-sm font-medium text-slate-700">{{ grade.name }}</span>
                </label>
              </div>
              <p v-if="form.errors.grade_ids" class="mt-1.5 text-sm text-red-500 font-medium">{{ form.errors.grade_ids }}</p>
            </div>
          </div>

          <!-- Location Information -->
          <div class="divider"></div>

          <div>
            <div class="flex items-center gap-3 mb-6">
              <div class="w-10 h-10 rounded-xl bg-rose-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
              <h3 class="text-lg font-bold text-slate-900">الموقع</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">مكان الإقامة <span class="text-red-500">*</span></label>
                <input v-model="form.residence_place" type="text" class="input-base" :class="{ 'input-error': form.errors.residence_place }" placeholder="المدينة / المحافظة" />
                <p v-if="form.errors.residence_place" class="mt-1.5 text-sm text-red-500 font-medium">{{ form.errors.residence_place }}</p>
              </div>
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">الموقع الحالي <span class="text-red-500">*</span></label>
                <input v-model="form.current_location" type="text" class="input-base" :class="{ 'input-error': form.errors.current_location }" placeholder="المدينة / المحافظة" />
                <p v-if="form.errors.current_location" class="mt-1.5 text-sm text-red-500 font-medium">{{ form.errors.current_location }}</p>
              </div>
            </div>
          </div>

          <!-- Practical Experience -->
          <div class="divider"></div>

          <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">الخبرات العملية <span class="text-red-500">*</span></label>
            <textarea v-model="form.practical_experience" rows="4" class="input-base resize-none" :class="{ 'input-error': form.errors.practical_experience }" placeholder="اذكر الخبرات العملية السابقة..."></textarea>
            <p v-if="form.errors.practical_experience" class="mt-1.5 text-sm text-red-500 font-medium">{{ form.errors.practical_experience }}</p>
          </div>

          <!-- Bio -->
          <div class="divider"></div>

          <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">نبذة تعريفية <span class="text-red-500">*</span></label>
            <textarea v-model="form.bio" rows="4" class="input-base resize-none" :class="{ 'input-error': form.errors.bio }" placeholder="اكتب نبذة تعريفية..."></textarea>
            <p v-if="form.errors.bio" class="mt-1.5 text-sm text-red-500 font-medium">{{ form.errors.bio }}</p>
          </div>

          <!-- Status -->
          <div class="divider"></div>

          <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">الحالة</label>
            <div class="flex gap-4">
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" v-model="form.status" value="approved" class="w-4 h-4 text-emerald-600 focus:ring-emerald-500" />
                <span class="text-sm font-medium text-slate-700">مقبول</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" v-model="form.status" value="pending" class="w-4 h-4 text-amber-600 focus:ring-amber-500" />
                <span class="text-sm font-medium text-slate-700">قيد المراجعة</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" v-model="form.status" value="rejected" class="w-4 h-4 text-red-600 focus:ring-red-500" />
                <span class="text-sm font-medium text-slate-700">مرفوض</span>
              </label>
            </div>
          </div>

          <div class="flex flex-wrap items-center gap-4 pt-6 border-t border-surface-200">
            <button type="submit" :disabled="form.processing" class="btn-primary flex-1 md:flex-none px-4 md:px-10">
              <svg v-if="form.processing" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              {{ form.processing ? 'جاري الحفظ...' : 'إضافة المدرس' }}
            </button>
            <Link href="/admin/teachers" class="btn-ghost px-8">إلغاء</Link>
          </div>
        </form>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import Alert from '@/Components/Alert.vue'

const props = defineProps({
  subjects: Array,
  grades: Array,
})

const photoPreview = ref(null)

const form = useForm({
  name: '',
  email: '',
  password: '',
  photo: null,
  phone: '',
  birth_date: '',
  gender: '',
  subject_id: '',
  grade_ids: [],
  experience_years: '',
  qualification: '',
  practical_experience: '',
  residence_place: '',
  current_location: '',
  bio: '',
  status: 'approved',
})

const handlePhotoChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    form.photo = file
    photoPreview.value = URL.createObjectURL(file)
  }
}

const submit = () => {
  form.post('/admin/teachers', {
    forceFormData: true,
    onSuccess: () => form.reset('photo', 'password'),
  })
}
</script>
