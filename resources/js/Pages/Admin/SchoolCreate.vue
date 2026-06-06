<template>
  <DashboardLayout>
    <div class="max-w-3xl mx-auto">
      <div class="relative mb-8 overflow-hidden rounded-3xl bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 shadow-2xl shadow-primary-500/20 animate-fade-in-up">
        <div class="absolute inset-0 bg-grid opacity-20"></div>
        <div class="relative px-8 py-10">
          <div class="flex items-center gap-4 mb-3">
            <div class="w-14 h-14 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center text-3xl shadow-lg">
              🏫
            </div>
            <div>
              <h1 class="text-3xl font-extrabold text-white">إضافة مدرسة جديدة</h1>
              <p class="text-primary-200">إنشاء حساب مدرسة جديدة مع البيانات كاملة</p>
            </div>
          </div>
        </div>
      </div>

      <Alert v-if="form.errors.name || form.errors.email || form.errors.password" variant="error" title="خطأ في بيانات المستخدم" class="mb-6 animate-fade-in-down" />

      <form @submit.prevent="submit" class="space-y-6">
        <!-- Account Information -->
        <div class="card p-8 animate-fade-in-up animate-delay-100">
          <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
            بيانات الحساب
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-1.5">الاسم <span class="text-red-500">*</span></label>
              <input v-model="form.name" type="text" class="input-base" :class="{ 'input-error': form.errors.name }" placeholder="اسم المستخدم" />
              <p v-if="form.errors.name" class="mt-1.5 text-sm text-red-500 font-medium">{{ form.errors.name }}</p>
            </div>
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-1.5">البريد الإلكتروني <span class="text-red-500">*</span></label>
              <input v-model="form.email" type="email" class="input-base" :class="{ 'input-error': form.errors.email }" placeholder="email@example.com" />
              <p v-if="form.errors.email" class="mt-1.5 text-sm text-red-500 font-medium">{{ form.errors.email }}</p>
            </div>
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-1.5">كلمة المرور <span class="text-red-500">*</span></label>
              <input v-model="form.password" type="password" class="input-base" :class="{ 'input-error': form.errors.password }" placeholder="أدخل كلمة المرور" />
              <p v-if="form.errors.password" class="mt-1.5 text-sm text-red-500 font-medium">{{ form.errors.password }}</p>
            </div>
          </div>
        </div>

        <!-- School Info -->
        <div class="card p-8 animate-fade-in-up animate-delay-100">
          <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
            بيانات المدرسة
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
              <label class="block text-sm font-bold text-slate-700 mb-1.5">اسم المدرسة <span class="text-red-500">*</span></label>
              <input v-model="form.school_name" type="text" class="input-base" :class="{ 'input-error': form.errors.school_name }" placeholder="مدرسة الأندلس الأهلية" />
              <p v-if="form.errors.school_name" class="mt-1.5 text-sm text-red-500 font-medium">{{ form.errors.school_name }}</p>
            </div>

            <div>
              <label class="block text-sm font-bold text-slate-700 mb-1.5">السجل التجاري <span class="text-red-500">*</span></label>
              <input v-model="form.commercial_register" type="text" class="input-base" :class="{ 'input-error': form.errors.commercial_register }" placeholder="رقم السجل التجاري" />
              <p v-if="form.errors.commercial_register" class="mt-1.5 text-sm text-red-500 font-medium">{{ form.errors.commercial_register }}</p>
            </div>

            <div>
              <label class="block text-sm font-bold text-slate-700 mb-1.5">الرقم الضريبي</label>
              <input v-model="form.tax_id" type="text" class="input-base" placeholder="رقم الضريبة (اختياري)" />
            </div>

            <div>
              <label class="block text-sm font-bold text-slate-700 mb-1.5">رقم الترخيص</label>
              <input v-model="form.license_number" type="text" class="input-base" placeholder="رقم ترخيص المدرسة (اختياري)" />
            </div>

            <div>
              <label class="block text-sm font-bold text-slate-700 mb-1.5">هاتف المدرسة</label>
              <input v-model="form.phone" type="text" class="input-base" placeholder="05xxxxxxxx" />
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-bold text-slate-700 mb-1.5">العنوان</label>
              <input v-model="form.address" type="text" class="input-base" placeholder="عنوان المدرسة" />
            </div>

            <div>
              <label class="block text-sm font-bold text-slate-700 mb-1.5">الموقع الإلكتروني</label>
              <input v-model="form.website" type="url" class="input-base" placeholder="https://example.com" />
              <p v-if="form.errors.website" class="mt-1.5 text-sm text-red-500 font-medium">{{ form.errors.website }}</p>
            </div>

            <div>
              <label class="block text-sm font-bold text-slate-700 mb-1.5">مدير المدرسة</label>
              <input v-model="form.principal_name" type="text" class="input-base" placeholder="اسم مدير المدرسة" />
            </div>
          </div>
        </div>

        <!-- Logo & Bio -->
        <div class="card p-8 animate-fade-in-up animate-delay-200">
          <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-warm-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-warm-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
            الشعار والوصف
          </h2>

          <div class="mb-5">
            <label class="block text-sm font-bold text-slate-700 mb-1.5">شعار المدرسة</label>
            <div class="flex items-center gap-4">
              <div class="w-24 h-24 rounded-2xl bg-surface-100 overflow-hidden flex-shrink-0 border-2 border-dashed border-surface-300">
                <img v-if="logoPreview" :src="logoPreview" class="w-full h-full object-cover" />
                <div v-else class="w-full h-full flex items-center justify-center text-3xl">🏫</div>
              </div>
              <div class="flex-1">
                <input type="file" @change="onLogoChange" accept="image/jpg,image/jpeg,image/png,image/webp" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" />
                <p class="text-xs text-slate-400 mt-1.5">JPG, JPEG, PNG, WebP</p>
              </div>
            </div>
            <p v-if="form.errors.logo" class="mt-1.5 text-sm text-red-500 font-medium">{{ form.errors.logo }}</p>
          </div>

          <div>
            <label class="block text-sm font-bold text-slate-700 mb-1.5">نبذة عن المدرسة</label>
            <textarea v-model="form.bio" rows="4" class="input-base resize-none" placeholder="اكتب نبذة تعريفية عن المدرسة..."></textarea>
          </div>
        </div>

        <!-- Status -->
        <div class="card p-8 animate-fade-in-up animate-delay-300">
          <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            الحالة
          </h2>

          <div class="flex gap-4">
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="radio" v-model="form.status" value="approved" class="w-4 h-4 text-emerald-600 focus:ring-emerald-500" />
              <span class="text-sm font-medium text-slate-700">مقبولة</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="radio" v-model="form.status" value="pending" class="w-4 h-4 text-amber-600 focus:ring-amber-500" />
              <span class="text-sm font-medium text-slate-700">قيد المراجعة</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="radio" v-model="form.status" value="rejected" class="w-4 h-4 text-red-600 focus:ring-red-500" />
              <span class="text-sm font-medium text-slate-700">مرفوضة</span>
            </label>
          </div>
        </div>

        <div class="flex flex-wrap items-center gap-3 animate-fade-in-up animate-delay-300">
          <button type="submit" :disabled="form.processing" class="btn-primary">
            <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            {{ form.processing ? 'جاري الحفظ...' : 'إضافة المدرسة' }}
          </button>
          <Link href="/admin/schools" class="btn-ghost px-6">إلغاء</Link>
        </div>
      </form>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import Alert from '@/Components/Alert.vue'

const logoPreview = ref(null)

const form = useForm({
  name: '',
  email: '',
  password: '',
  school_name: '',
  commercial_register: '',
  tax_id: '',
  license_number: '',
  phone: '',
  address: '',
  website: '',
  principal_name: '',
  logo: null,
  bio: '',
  status: 'approved',
})

const onLogoChange = (e) => {
  const file = e.target.files[0]
  if (!file) return
  form.logo = file
  logoPreview.value = URL.createObjectURL(file)
}

const submit = () => {
  form.post('/admin/schools', {
    forceFormData: true,
    onSuccess: () => form.reset('logo', 'password'),
  })
}
</script>
