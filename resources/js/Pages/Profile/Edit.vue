<template>
  <MainLayout>
    <div class="max-w-3xl mx-auto">
      <!-- Header -->
      <div class="flex items-center justify-between mb-8 animate-fade-in-up">
        <div>
          <h1 class="text-3xl font-extrabold text-slate-900">الملف الشخصي</h1>
          <p class="text-slate-500 mt-1">إدارة معلومات حسابك وكلمة المرور</p>
        </div>
      </div>

      <Alert v-if="flash.message" variant="success" :title="flash.message" closeable class="mb-6 animate-fade-in-down" />
      <Alert v-if="flash.status === 'profile-updated'" variant="success" title="تم الحفظ" description="تم تحديث المعلومات بنجاح" closeable class="mb-6 animate-fade-in-down" />
      <Alert v-if="flash.status === 'password-updated'" variant="success" title="تم التحديث" description="تم تغيير كلمة المرور بنجاح" closeable class="mb-6 animate-fade-in-down" />

      <!-- Profile Information -->
      <div class="card p-6 mb-6 animate-fade-in-up">
        <h2 class="text-xl font-bold text-slate-900 mb-2">معلومات الحساب</h2>
        <p class="text-sm text-slate-500 mb-6">تحديث الاسم والبريد الإلكتروني</p>

        <form @submit.prevent="updateProfile">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-1.5">الاسم الكامل</label>
              <input v-model="profileForm.name" type="text" class="input-base" required />
              <p v-if="profileForm.errors.name" class="text-red-500 text-xs mt-1">{{ profileForm.errors.name }}</p>
            </div>
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-1.5">البريد الإلكتروني</label>
              <input v-model="profileForm.email" type="email" class="input-base" dir="ltr" required />
              <p v-if="profileForm.errors.email" class="text-red-500 text-xs mt-1">{{ profileForm.errors.email }}</p>
            </div>
          </div>

          <div class="mt-6 flex justify-end">
            <button type="submit" :disabled="profileForm.processing" class="btn-primary">
              <svg v-if="profileForm.processing" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              حفظ التغييرات
            </button>
          </div>
        </form>
      </div>

      <!-- Update Password -->
      <div class="card p-6 mb-6 animate-fade-in-up animate-delay-100">
        <h2 class="text-xl font-bold text-slate-900 mb-2">تغيير كلمة المرور</h2>
        <p class="text-sm text-slate-500 mb-6">تأكد من استخدام كلمة مرور قوية</p>

        <form @submit.prevent="updatePassword">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-1.5">كلمة المرور الحالية</label>
              <input v-model="passwordForm.current_password" type="password" class="input-base" required />
              <p v-if="passwordForm.errors.current_password" class="text-red-500 text-xs mt-1">{{ passwordForm.errors.current_password }}</p>
            </div>
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-1.5">كلمة المرور الجديدة</label>
              <input v-model="passwordForm.password" type="password" class="input-base" required />
              <p v-if="passwordForm.errors.password" class="text-red-500 text-xs mt-1">{{ passwordForm.errors.password }}</p>
            </div>
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-1.5">تأكيد كلمة المرور</label>
              <input v-model="passwordForm.password_confirmation" type="password" class="input-base" required />
            </div>
          </div>

          <div class="mt-6 flex justify-end">
            <button type="submit" :disabled="passwordForm.processing" class="btn-primary">
              <svg v-if="passwordForm.processing" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              تغيير كلمة المرور
            </button>
          </div>
        </form>
      </div>

      <!-- Delete Account -->
      <div v-if="!user?.is_admin" class="card p-6 animate-fade-in-up animate-delay-200">
        <h2 class="text-xl font-bold text-red-600 mb-2">حذف الحساب</h2>
        <p class="text-sm text-slate-500 mb-6">بمجرد حذف حسابك، لا يمكن استرجاع البيانات</p>

        <form @submit.prevent="confirmDelete">
          <div class="max-w-md">
            <label class="block text-sm font-bold text-slate-700 mb-1.5">كلمة المرور لتأكيد الحذف</label>
            <input v-model="deleteForm.password" type="password" class="input-base" placeholder="أدخل كلمة المرور" required />
            <p v-if="deleteForm.errors.password" class="text-red-500 text-xs mt-1">{{ deleteForm.errors.password }}</p>
          </div>

          <div class="mt-6">
            <button type="submit" :disabled="deleteForm.processing" class="btn-base bg-red-600 text-white hover:bg-red-700 hover:shadow-lg hover:shadow-red-500/25 focus:ring-4 focus:ring-red-500/20">
              <svg v-if="deleteForm.processing" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              حذف الحساب
            </button>
          </div>
        </form>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import Alert from '@/Components/Alert.vue'

const page = usePage()
const flash = page.props.flash || {}
const user = page.props.auth.user

const profileForm = useForm({
  name: user?.name || '',
  email: user?.email || '',
})

const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
})

const deleteForm = useForm({
  password: '',
})

const updateProfile = () => {
  profileForm.patch('/profile', {
    preserveScroll: true,
  })
}

const updatePassword = () => {
  passwordForm.put('/password', {
    preserveScroll: true,
    onSuccess: () => {
      passwordForm.reset()
    },
  })
}

const confirmDelete = () => {
  if (!confirm('هل أنت متأكد؟ هذا الإجراء لا يمكن التراجع عنه.')) return
  deleteForm.delete('/profile')
}
</script>
