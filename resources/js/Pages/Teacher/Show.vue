<template>
  <MainLayout>
    <div class="max-w-4xl mx-auto">
      <!-- Back Button -->
      <Link href="/" class="btn-ghost mb-6 animate-fade-in-up">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        العودة إلى الرئيسية
      </Link>

      <!-- Header Card -->
      <div class="card p-8 mb-6 animate-fade-in-up animate-delay-100 relative overflow-hidden">
        <!-- Watermark for employed teachers -->
        <div
          v-if="teacher.employment_status === 'employed'"
          class="absolute inset-0 flex items-center justify-center pointer-events-none select-none"
        >
          <span class="text-6xl font-extrabold text-primary-500/10 -rotate-[30deg] tracking-wider">موظف حالياً</span>
        </div>

        <div class="flex flex-col md:flex-row items-start gap-6 relative">
          <!-- Photo -->
          <div class="flex-shrink-0 mx-auto md:mx-0">
            <div class="w-32 h-32 rounded-3xl bg-gradient-to-br from-primary-100 to-surface-100 overflow-hidden shadow-lg shadow-primary-500/10 ring-4 ring-white">
              <img
                v-if="teacher.photo"
                :src="`/storage/${teacher.photo}`"
                :alt="teacher.user?.name"
                class="w-full h-full object-cover"
              />
              <div v-else class="w-full h-full flex items-center justify-center text-5xl">👨‍🏫</div>
            </div>
          </div>

          <!-- Info -->
          <div class="flex-1 min-w-0 text-center md:text-right">
            <div class="flex items-center justify-center md:justify-start gap-3 mb-1">
              <h1 class="text-3xl font-extrabold text-slate-900">{{ teacher.user?.name }}</h1>
              <span
                v-if="teacher.employment_status === 'employed'"
                class="badge bg-amber-500 text-white text-xs"
              >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                موظف حالياً
              </span>
            </div>
            <p class="text-slate-500 mb-4">{{ teacher.qualification || '-' }}</p>

            <div class="flex flex-wrap justify-center md:justify-start gap-3 mb-5">
              <span class="badge-primary text-sm">{{ teacher.subject?.name }}</span>
              <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-warm-50 text-warm-700 rounded-full text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ teacher.experience_years }} {{ $t('messages.years_exp') }}
              </span>
            </div>

            <!-- Contact Buttons -->
            <div class="flex flex-wrap justify-center md:justify-start gap-3">
              <template v-if="teacher.employment_status === 'employed'">
                <span class="inline-flex items-center gap-2 px-6 py-2.5 bg-surface-200 text-surface-500 rounded-xl text-sm font-medium cursor-not-allowed">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                  </svg>
                  غير متاح للتوظيف
                </span>
              </template>
              <a
                v-else
                :href="`tel:${teacher.phone}`"
                class="btn-primary"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                {{ $t('messages.contact_teacher') }}
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Details Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Personal Info -->
        <div class="card p-6 animate-fade-in-up animate-delay-200">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
            <h2 class="text-lg font-bold text-slate-900">{{ $t('messages.personal_details') }}</h2>
          </div>
          <div class="space-y-3">
            <div class="flex justify-between py-2 border-b border-surface-100">
              <span class="text-slate-500 text-sm">{{ $t('messages.phone') }}</span>
              <span
                v-if="teacher.employment_status === 'employed'"
                class="font-medium text-slate-400 text-sm"
              >غير متاح</span>
              <span
                v-else
                class="font-medium text-slate-900 ltr"
                dir="ltr"
              >{{ teacher.phone || '-' }}</span>
            </div>
            <div class="flex justify-between py-2 border-b border-surface-100">
              <span class="text-slate-500 text-sm">{{ $t('messages.birth_date') }}</span>
              <span class="font-medium text-slate-900">{{ formatDate(teacher.birth_date) }}</span>
            </div>
            <div class="flex justify-between py-2 border-b border-surface-100">
              <span class="text-slate-500 text-sm">{{ $t('messages.gender') }}</span>
              <span class="font-medium text-slate-900">{{ teacher.gender === 'male' ? $t('messages.male') : (teacher.gender === 'female' ? $t('messages.female') : '-') }}</span>
            </div>
            <div class="flex justify-between py-2">
              <span class="text-slate-500 text-sm">{{ $t('messages.location') }}</span>
              <span class="font-medium text-slate-900">{{ teacher.residence_place || '-' }}</span>
            </div>
          </div>
        </div>

        <!-- Academic Info -->
        <div class="card p-6 animate-fade-in-up animate-delay-300">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-warm-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-warm-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 7l-9-5 9-5 9 5-9 5z" />
              </svg>
            </div>
            <h2 class="text-lg font-bold text-slate-900">{{ $t('messages.academic_details') }}</h2>
          </div>
          <div class="space-y-3">
            <div class="flex justify-between py-2 border-b border-surface-100">
              <span class="text-slate-500 text-sm">{{ $t('messages.subject') }}</span>
              <span class="font-medium text-slate-900">{{ teacher.subject?.name || '-' }}</span>
            </div>
            <div class="py-2 border-b border-surface-100">
              <span class="text-slate-500 text-sm block mb-2">{{ $t('messages.grade') }}</span>
              <div class="flex flex-wrap gap-1.5">
                <span
                  v-for="grade in teacher.grades"
                  :key="grade.id"
                  class="badge-primary text-xs"
                >
                  {{ grade.name }}
                </span>
                <span v-if="!teacher.grades?.length" class="text-sm text-slate-400">-</span>
              </div>
            </div>
            <div class="flex justify-between py-2">
              <span class="text-slate-500 text-sm">{{ $t('messages.qualification') }}</span>
              <span class="font-medium text-slate-900">{{ teacher.qualification || '-' }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Practical Experience -->
      <div v-if="teacher.practical_experience" class="card p-6 mb-6 animate-fade-in-up animate-delay-350">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 rounded-xl bg-warm-100 flex items-center justify-center">
            <svg class="w-5 h-5 text-warm-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
          </div>
          <h2 class="text-lg font-bold text-slate-900">{{ $t('messages.practical_experience') }}</h2>
        </div>
        <p class="text-slate-700 leading-relaxed whitespace-pre-wrap">
          {{ teacher.practical_experience }}
        </p>
      </div>

      <!-- Bio -->
      <div class="card p-6 mb-6 animate-fade-in-up animate-delay-400">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 rounded-xl bg-rose-100 flex items-center justify-center">
            <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
            </svg>
          </div>
          <h2 class="text-lg font-bold text-slate-900">{{ $t('messages.bio') }}</h2>
        </div>
        <p class="text-slate-700 leading-relaxed whitespace-pre-wrap">
          {{ teacher.bio || $t('messages.no_qualification') }}
        </p>
      </div>

      <!-- Employment History -->
      <div v-if="history.length > 0" class="card p-6 mb-6 animate-fade-in-up animate-delay-450">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 rounded-xl bg-surface-100 flex items-center justify-center">
            <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h2 class="text-lg font-bold text-slate-900">السجل الوظيفي</h2>
        </div>
        <div class="space-y-3">
          <div
            v-for="item in history"
            :key="item.id"
            class="flex items-center justify-between p-4 rounded-xl bg-surface-50"
          >
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-lg flex-shrink-0 shadow-sm">🏫</div>
              <div>
                <p class="font-bold text-slate-900">{{ item.school?.school_profile?.school_name || 'مدرسة' }}</p>
                <p class="text-xs text-slate-500">
                  {{ item.subject?.name }}
                  <span class="mx-1">·</span>
                  {{ formatDate(item.hired_at) }}
                </p>
              </div>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 whitespace-nowrap">منتهي</span>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'

const props = defineProps({
  teacher: Object,
  history: { type: Array, default: () => [] },
})

const formatDate = (date) => {
  if (!date) return '-'
  const d = new Date(date)
  const pad = (n) => String(n).padStart(2, '0')
  return `${d.getFullYear()}/${pad(d.getMonth() + 1)}/${pad(d.getDate())}`
}
</script>
