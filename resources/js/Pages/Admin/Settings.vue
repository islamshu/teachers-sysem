<template>
  <DashboardLayout>
    <div>
      <!-- Header -->
      <div class="flex items-center justify-between mb-8 animate-fade-in-up">
        <div>
          <h1 class="text-3xl font-extrabold text-slate-900">إعدادات النظام</h1>
          <p class="text-slate-500 mt-1">تعديل اسم الموقع، الشعار، الألوان، والإعدادات العامة</p>
        </div>
      </div>

      <!-- Flash Message -->
      <Alert v-if="flash.message" variant="success" :title="flash.message" closeable class="mb-6 animate-fade-in-down" />

      <form @submit.prevent="submit" class="space-y-8">
        <!-- Site Info Section -->
        <div class="card p-6 animate-fade-in-up">
          <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-primary-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            معلومات الموقع
          </h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-2">اسم الموقع</label>
              <input v-model="form.site_name" type="text" class="input-base" placeholder="اسم الموقع" />
              <p v-if="form.errors.site_name" class="text-red-500 text-xs mt-1">{{ form.errors.site_name }}</p>
            </div>
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-2">اللون الأساسي</label>
              <div class="flex gap-3 items-center">
                <input v-model="form.primary_color" type="color" class="w-10 h-10 rounded-xl border border-surface-200 cursor-pointer" />
                <input v-model="form.primary_color" type="text" class="input flex-1" placeholder="#10b981" dir="ltr" />
              </div>
              <p v-if="form.errors.primary_color" class="text-red-500 text-xs mt-1">{{ form.errors.primary_color }}</p>
            </div>
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-2">شعار الموقع</label>
              <input @change="onLogoChange" type="file" accept="image/png,image/jpg,image/jpeg,image/svg+xml" class="input-base" />
              <p v-if="form.errors.site_logo" class="text-red-500 text-xs mt-1">{{ form.errors.site_logo }}</p>
              <div v-if="logoPreview || settings.site_logo" class="mt-3">
                <img :src="logoPreview || `/storage/${settings.site_logo}`" alt="Logo" class="h-12 rounded-lg" />
              </div>
            </div>
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-2">وصف الموقع</label>
              <textarea v-model="form.description" class="input-base" rows="3" placeholder="وصف مختصر للموقع"></textarea>
              <p v-if="form.errors.description" class="text-red-500 text-xs mt-1">{{ form.errors.description }}</p>
            </div>
          </div>
        </div>

        <!-- Contact Section -->
        <div class="card p-6 animate-fade-in-up animate-delay-100">
          <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-primary-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
            </div>
            معلومات الاتصال
          </h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-2">البريد الإلكتروني</label>
              <input v-model="form.contact_email" type="email" class="input-base" placeholder="info@example.com" dir="ltr" />
              <p v-if="form.errors.contact_email" class="text-red-500 text-xs mt-1">{{ form.errors.contact_email }}</p>
            </div>
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-2">رقم الهاتف</label>
              <input v-model="form.contact_phone" type="text" class="input-base" placeholder="+966 5X XXX XXXX" dir="ltr" />
              <p v-if="form.errors.contact_phone" class="text-red-500 text-xs mt-1">{{ form.errors.contact_phone }}</p>
            </div>
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-2">العنوان</label>
              <input v-model="form.address" type="text" class="input-base" placeholder="العنوان" />
              <p v-if="form.errors.address" class="text-red-500 text-xs mt-1">{{ form.errors.address }}</p>
            </div>
          </div>
        </div>

        <!-- Hero Section -->
        <div class="card p-6 animate-fade-in-up animate-delay-150">
          <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-primary-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
            قسم البطل (Hero)
          </h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-2">العنوان الرئيسي</label>
              <input v-model="form.hero_title" type="text" class="input-base" placeholder="مثال: مرحباً بكم في منصتنا" />
              <p v-if="form.errors.hero_title" class="text-red-500 text-xs mt-1">{{ form.errors.hero_title }}</p>
            </div>
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-2">النص الفرعي</label>
              <input v-model="form.hero_subtitle" type="text" class="input-base" placeholder="وصف قصير يظهر تحت العنوان" />
              <p v-if="form.errors.hero_subtitle" class="text-red-500 text-xs mt-1">{{ form.errors.hero_subtitle }}</p>
            </div>
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-2">لون الخلفية - البداية</label>
              <div class="flex gap-3 items-center">
                <input v-model="form.hero_bg_from" type="color" class="w-10 h-10 rounded-xl border border-surface-200 cursor-pointer" />
                <input v-model="form.hero_bg_from" type="text" class="input flex-1" placeholder="#1e40af" dir="ltr" />
              </div>
              <p v-if="form.errors.hero_bg_from" class="text-red-500 text-xs mt-1">{{ form.errors.hero_bg_from }}</p>
            </div>
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-2">لون الخلفية - النهاية</label>
              <div class="flex gap-3 items-center">
                <input v-model="form.hero_bg_to" type="color" class="w-10 h-10 rounded-xl border border-surface-200 cursor-pointer" />
                <input v-model="form.hero_bg_to" type="text" class="input flex-1" placeholder="#7c3aed" dir="ltr" />
              </div>
              <p v-if="form.errors.hero_bg_to" class="text-red-500 text-xs mt-1">{{ form.errors.hero_bg_to }}</p>
            </div>
            <div class="flex items-center gap-3">
              <input
                v-model="form.show_stats"
                type="checkbox"
                id="show_stats"
                class="w-5 h-5 rounded border-surface-300 text-primary-600 focus:ring-primary-500"
                :true-value="'1'"
                :false-value="'0'"
              />
              <label for="show_stats" class="text-sm font-bold text-slate-700">إظهار إحصائيات (عدد المدرسين والمواد)</label>
            </div>
          </div>
        </div>

        <!-- About Section -->
        <div class="card p-6 animate-fade-in-up animate-delay-200">
          <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-primary-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </div>
            عن الموقع
          </h2>
          <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">نبذة عن الموقع</label>
            <textarea v-model="form.about" class="input-base" rows="5" placeholder="اكتب نبذة عن الموقع..."></textarea>
            <p v-if="form.errors.about" class="text-red-500 text-xs mt-1">{{ form.errors.about }}</p>
          </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-end animate-fade-in-up animate-delay-300">
          <button type="submit" :disabled="form.processing" class="btn-primary">
            <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            حفظ الإعدادات
          </button>
        </div>
      </form>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import Alert from '@/Components/Alert.vue'
import { applyPrimaryColor } from '@/utils/colors'

const props = defineProps({
  settings: Object,
  flash: Object,
})

const form = useForm({
  site_name: props.settings.site_name || '',
  site_logo: null,
  primary_color: props.settings.primary_color || '#10b981',
  description: props.settings.description || '',
  contact_email: props.settings.contact_email || '',
  contact_phone: props.settings.contact_phone || '',
  address: props.settings.address || '',
  about: props.settings.about || '',
  hero_title: props.settings.hero_title || '',
  hero_subtitle: props.settings.hero_subtitle || '',
  hero_bg_from: props.settings.hero_bg_from || '#1e40af',
  hero_bg_to: props.settings.hero_bg_to || '#7c3aed',
  show_stats: props.settings.show_stats ?? '1',
})

const logoPreview = ref(null)

const onLogoChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    form.site_logo = file
    const reader = new FileReader()
    reader.onload = (ev) => { logoPreview.value = ev.target.result }
    reader.readAsDataURL(file)
  }
}

const submit = () => {
  form.post('/admin/settings', {
    onSuccess: () => logoPreview.value = null,
  })
}

watch(() => form.primary_color, (val) => {
  if (/^#[0-9a-f]{6}$/i.test(val)) {
    applyPrimaryColor(val)
  }
})
</script>
