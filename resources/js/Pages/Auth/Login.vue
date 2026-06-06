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
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
          </div>
          <h1 class="text-2xl font-extrabold text-slate-900">تسجيل الدخول</h1>
          <p class="text-slate-500 mt-1">أهلاً بك! قم بتسجيل الدخول إلى حسابك</p>
        </div>

        <Alert v-if="status" variant="success" :title="status" class="mb-6" />
        <Alert v-if="form.errors.email" variant="error" title="خطأ" :description="form.errors.email" class="mb-6" />

        <form @submit.prevent="submit">
          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-bold text-slate-700 mb-1.5">البريد الإلكتروني <span class="text-red-500">*</span></label>
            <input id="email" v-model="form.email" type="email" class="input-base" placeholder="teacher@example.com" required autofocus autocomplete="username" />
          </div>

          <!-- Password -->
          <div class="mt-5">
            <label for="password" class="block text-sm font-bold text-slate-700 mb-1.5">كلمة المرور <span class="text-red-500">*</span></label>
            <input id="password" v-model="form.password" type="password" class="input-base" placeholder="********" required autocomplete="current-password" />
          </div>

          <!-- Remember & Forgot -->
          <div class="flex flex-wrap items-center justify-between gap-2 mt-5">
            <label for="remember_me" class="flex items-center gap-2 cursor-pointer">
              <input id="remember_me" v-model="form.remember" type="checkbox" class="w-4 h-4 rounded border-surface-300 text-primary-600 focus:ring-primary-500" />
              <span class="text-sm text-slate-600">تذكرني</span>
            </label>
            <Link href="/forgot-password" class="text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors">
              نسيت كلمة المرور؟
            </Link>
          </div>

          <!-- Submit -->
          <div class="mt-6">
            <button type="submit" :disabled="form.processing" class="btn-primary w-full">
              <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
              </svg>
              تسجيل الدخول
            </button>
          </div>
        </form>
      </div>

      <!-- Footer -->
      <p class="text-center mt-6 text-sm text-slate-500">
        ليس لديك حساب؟
        <Link href="/register" class="font-semibold text-primary-600 hover:text-primary-700 transition-colors">
          سجل الآن
        </Link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { useForm, usePage, Link } from '@inertiajs/vue3'
import Alert from '@/Components/Alert.vue'

const page = usePage()
const status = page.props?.flash?.status || ''

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const submit = () => {
  form.post('/login', {
    onError: () => form.reset('password'),
  })
}
</script>
