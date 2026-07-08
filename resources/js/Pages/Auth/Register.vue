<template>
  <div class="min-h-screen bg-surface-50 flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
    <!-- Background decoration -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden">
      <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary-500/10 rounded-full blur-3xl"></div>
      <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-warm-500/10 rounded-full blur-3xl"></div>
    </div>

    <!-- Logo -->
    <div class="relative mb-8">
      <Link href="/" class="flex items-center gap-3">
        <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-700 rounded-2xl flex items-center justify-center shadow-lg shadow-primary-500/20">
          <span class="text-white font-extrabold text-xl">م</span>
        </div>
        <span class="text-2xl font-extrabold bg-gradient-to-l from-primary-600 to-primary-800 bg-clip-text text-transparent">
          {{ $page.props.settings?.site_name || $t('app.name') }}
        </span>
      </Link>
    </div>

    <!-- Card -->
    <div class="relative w-full sm:max-w-md px-6 sm:px-0">
      <div class="card p-8 animate-fade-in-up">
        <div class="text-center mb-8">
          <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-100 to-primary-50 flex items-center justify-center mx-auto mb-4 shadow-lg shadow-primary-500/10">
            <svg class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
          </div>
          <h1 class="text-2xl font-extrabold text-slate-900">إنشاء حساب جديد</h1>
          <p class="text-slate-500 mt-1">قم بإنشاء حساب للانضمام إلى المنصة</p>
        </div>

        <Alert v-if="form.errors.email" variant="error" title="خطأ" :description="form.errors.email" class="mb-6" />
        <Alert v-if="form.errors.password" variant="error" title="خطأ" :description="form.errors.password" class="mb-6" />
        <Alert v-if="form.errors.name" variant="error" title="خطأ" :description="form.errors.name" class="mb-6" />

        <form @submit.prevent="submit">
          <!-- Name -->
          <div>
            <label for="name" class="block text-sm font-bold text-slate-700 mb-1.5">
              الاسم الكامل <span class="text-red-500">*</span>
            </label>
            <input id="name" v-model="form.name" type="text" class="input-base" placeholder="محمد أحمد" required autofocus autocomplete="name" />
          </div>

          <!-- Email -->
          <div class="mt-5">
            <label for="email" class="block text-sm font-bold text-slate-700 mb-1.5">البريد الإلكتروني <span class="text-red-500">*</span></label>
            <input id="email" v-model="form.email" type="email" class="input-base" placeholder="teacher@example.com" required autocomplete="username" />
          </div>

          <!-- Password -->
          <div class="mt-5">
            <label for="password" class="block text-sm font-bold text-slate-700 mb-1.5">كلمة المرور <span class="text-red-500">*</span></label>
            <input id="password" v-model="form.password" type="password" class="input-base" placeholder="********" required autocomplete="new-password" />
          </div>

          <!-- Confirm Password -->
          <div class="mt-5">
            <label for="password_confirmation" class="block text-sm font-bold text-slate-700 mb-1.5">تأكيد كلمة المرور <span class="text-red-500">*</span></label>
            <input id="password_confirmation" v-model="form.password_confirmation" type="password" class="input-base" placeholder="********" required autocomplete="new-password" />
          </div>

          <!-- Submit -->
          <div class="mt-6">
            <button type="submit" :disabled="form.processing" class="btn-primary w-full">
              <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
              </svg>
              إنشاء الحساب
            </button>
          </div>
        </form>
      </div>

      <!-- Footer -->
      <p class="text-center mt-6 text-sm text-slate-500">
        لديك حساب بالفعل؟
        <Link href="/login" class="font-semibold text-primary-600 hover:text-primary-700 transition-colors">
          تسجيل الدخول
        </Link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { useForm, usePage, Link } from '@inertiajs/vue3'
import Alert from '@/Components/Alert.vue'

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const submit = () => {
  form.post('/register', {
    onError: () => form.reset('password', 'password_confirmation'),
  })
}
</script>
