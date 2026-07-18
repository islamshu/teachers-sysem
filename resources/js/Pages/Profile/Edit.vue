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
              <label class="block text-sm font-bold text-slate-700 mb-1.5">الاسم الكامل <span class="text-red-500">*</span></label>
              <input v-model="profileForm.name" type="text" class="input-base" required />
              <p v-if="profileForm.errors.name" class="text-red-500 text-xs mt-1">{{ profileForm.errors.name }}</p>
            </div>
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-1.5">البريد الإلكتروني <span class="text-red-500">*</span></label>
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

      <!-- Telegram Connection -->
      <div class="card p-6 mb-6 animate-fade-in-up animate-delay-100">
        <h2 class="text-xl font-bold text-slate-900 mb-2">ربط Telegram</h2>
        <p class="text-sm text-slate-500 mb-6">استقبل إشعارات المدرسة مباشرة على Telegram</p>

        <div v-if="user?.telegram_chat_id" class="flex items-center gap-4 p-4 bg-green-50 rounded-xl border border-green-200">
          <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
            </svg>
          </div>
          <div>
            <p class="font-bold text-green-800">Telegram مربوط ✅</p>
            <p v-if="user?.telegram_username" class="text-sm text-green-600">@{{ user.telegram_username }}</p>
            <p v-if="user?.telegram_linked_at" class="text-xs text-green-500 mt-0.5">تاريخ الربط: {{ new Date(user.telegram_linked_at).toLocaleDateString('ar-SA') }}</p>
          </div>
        </div>

        <div v-else class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-200">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.74-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z"/>
              </svg>
            </div>
            <div>
              <p class="font-bold text-slate-800">اربط حساب Telegram</p>
              <p class="text-sm text-slate-500">استقبل جميع إشعارات المدرسة مباشرة</p>
            </div>
          </div>
          <button @click="connectTelegram" :disabled="connecting" class="btn-primary">
            <svg v-if="connecting" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            ربط الآن
          </button>
        </div>
      </div>

      <!-- Update Password -->
      <div class="card p-6 mb-6 animate-fade-in-up animate-delay-100">
        <h2 class="text-xl font-bold text-slate-900 mb-2">تغيير كلمة المرور</h2>
        <p class="text-sm text-slate-500 mb-6">تأكد من استخدام كلمة مرور قوية</p>

        <form @submit.prevent="updatePassword">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-1.5">كلمة المرور الحالية <span class="text-red-500">*</span></label>
              <input v-model="passwordForm.current_password" type="password" class="input-base" required />
              <p v-if="passwordForm.errors.current_password" class="text-red-500 text-xs mt-1">{{ passwordForm.errors.current_password }}</p>
            </div>
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-1.5">كلمة المرور الجديدة <span class="text-red-500">*</span></label>
              <input v-model="passwordForm.password" type="password" class="input-base" required />
              <p v-if="passwordForm.errors.password" class="text-red-500 text-xs mt-1">{{ passwordForm.errors.password }}</p>
            </div>
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-1.5">تأكيد كلمة المرور <span class="text-red-500">*</span></label>
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
            <label class="block text-sm font-bold text-slate-700 mb-1.5">كلمة المرور لتأكيد الحذف <span class="text-red-500">*</span></label>
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
import { useForm, usePage, router } from '@inertiajs/vue3'
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

const connecting = ref(false)

const connectTelegram = async () => {
  connecting.value = true
  try {
    const response = await fetch('/telegram/connect')
    const data = await response.json()
    if (data.link) {
      window.open(data.link, '_blank')
    }
  } catch (error) {
    console.error(error)
  } finally {
    connecting.value = false
  }
}

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
