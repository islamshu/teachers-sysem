<template>
  <DashboardLayout>
    <div>
      <!-- Welcome Banner -->
      <div class="relative mb-8 overflow-hidden rounded-3xl bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 shadow-2xl shadow-primary-500/20 animate-fade-in-up">
        <div class="absolute inset-0 bg-grid opacity-20"></div>
        <div class="absolute top-0 left-0 w-48 h-48 bg-primary-400/20 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
        <div class="relative px-4 md:px-8 py-6 md:py-10">
          <div class="flex flex-col sm:flex-row items-center sm:items-start gap-3 sm:gap-4 mb-3">
            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center text-2xl sm:text-3xl shadow-lg flex-shrink-0">
              👋
            </div>
            <div class="text-center sm:text-right">
              <h1 class="text-xl sm:text-2xl md:text-3xl font-extrabold text-white">{{ $t('messages.welcome_back') }}, {{ $page.props.auth.user?.name }}</h1>
              <p class="text-primary-200 text-sm sm:text-base">{{ $t('messages.dashboard_desc') }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Profile Status Card -->
          <div class="card animate-fade-in-up animate-delay-100">
            <template v-if="!profile">
              <div class="p-10 text-center">
                <div class="w-24 h-24 rounded-3xl bg-gradient-to-br from-primary-100 to-primary-50 flex items-center justify-center mx-auto mb-6 shadow-lg shadow-primary-500/10">
                  <svg class="w-12 h-12 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                </div>
                <h3 class="text-2xl font-bold text-slate-900 mb-2">{{ $t('messages.no_profile_title') }}</h3>
                <p class="text-slate-500 mb-8 max-w-md mx-auto">{{ $t('messages.no_profile_desc') }}</p>
                <Link
                  href="/teacher-profile/create"
                  class="btn-primary text-base px-8 py-3"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                  </svg>
                  {{ $t('messages.add_profile_btn') }}
                </Link>
              </div>
            </template>

            <template v-else>
              <div class="p-4 md:p-8">
                <div class="flex flex-col md:flex-row items-center md:items-start gap-4 md:gap-6">
                    <div class="relative flex-shrink-0">
                    <div class="w-16 h-16 md:w-20 md:h-20 rounded-2xl bg-gradient-to-br from-primary-100 to-surface-100 overflow-hidden shadow-lg shadow-primary-500/10">
                      <img v-if="profile.photo" :src="`/storage/${profile.photo}`" :alt="$page.props.auth.user?.name" class="w-full h-full object-cover" />
                      <div v-else class="w-full h-full flex items-center justify-center text-2xl md:text-3xl">👨‍🏫</div>
                    </div>
                  </div>
                  <div class="flex-1 min-w-0 w-full">
                    <div class="flex flex-col sm:flex-row items-center sm:items-start justify-between gap-3 mb-4">
                      <div class="text-center sm:text-right w-full">
                        <h3 class="text-xl md:text-2xl font-bold text-slate-900">{{ $page.props.auth.user?.name }}</h3>

                        <!-- Status Badge -->
                        <div class="mt-3 p-3 md:p-4 rounded-2xl border-2 text-right" :class="statusCardClass">
                          <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center" :class="statusIconClass">
                              <svg v-if="profile.status === 'approved'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                              </svg>
                              <svg v-else-if="profile.status === 'rejected'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                              </svg>
                              <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                              </svg>
                            </div>
                            <div>
                              <p class="text-sm font-bold" :class="statusTextClass">{{ statusText }}</p>
                              <p class="text-xs mt-0.5" :class="statusDescClass">{{ statusDescription }}</p>
                            </div>
                          </div>
                        </div>
                      </div>
                      <Link
                        href="/teacher-profile/edit"
                        class="btn-ghost px-3 md:px-4 py-2 text-xs md:text-sm flex-shrink-0"
                      >
                        <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        {{ $t('messages.edit') }}
                      </Link>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                      <div class="p-3 rounded-xl bg-primary-50">
                        <p class="text-xs text-primary-600 font-semibold mb-0.5">{{ $t('messages.subject') }}</p>
                        <p class="text-sm font-bold text-slate-900">{{ profile.subject?.name || '-' }}</p>
                      </div>
                      <div class="p-3 rounded-xl bg-warm-50">
                        <p class="text-xs text-warm-600 font-semibold mb-0.5">{{ $t('messages.experience') }}</p>
                        <p class="text-sm font-bold text-slate-900">{{ profile.experience_years || 0 }} {{ $t('messages.year') }}</p>
                      </div>
                      <div class="p-3 rounded-xl bg-emerald-50">
                        <p class="text-xs text-emerald-600 font-semibold mb-0.5">{{ $t('messages.qualification') }}</p>
                        <p class="text-sm font-bold text-slate-900 truncate">{{ profile.qualification || '-' }}</p>
                      </div>
                      <div class="p-3 rounded-xl bg-rose-50">
                        <p class="text-xs text-rose-600 font-semibold mb-0.5">{{ $t('messages.location') }}</p>
                        <p class="text-sm font-bold text-slate-900 truncate">{{ profile.residence_place || '-' }}</p>
                      </div>
                    </div>

                    <div class="mt-4 text-center sm:text-right">
                      <p class="text-xs text-slate-500 font-semibold mb-2">{{ $t('messages.grades_teaching') }}</p>
                      <div class="flex flex-wrap gap-1.5 justify-center sm:justify-start">
                        <span
                          v-for="grade in profile.grades"
                          :key="grade.id"
                          class="inline-block px-3 py-1 bg-surface-100 text-surface-700 rounded-lg text-xs font-medium"
                        >
                          {{ grade.name }}
                        </span>
                        <span v-if="!profile.grades?.length" class="text-sm text-slate-400">-</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </template>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          <!-- Quick Links -->
          <div class="card p-6 animate-fade-in-up animate-delay-200">
            <h2 class="text-lg font-bold text-slate-900 mb-4">{{ $t('messages.quick_links') }}</h2>
            <div class="space-y-2">
              <Link
                href="/"
                class="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-slate-700 hover:bg-primary-50 hover:text-primary-700 transition-all duration-200 group"
              >
                <div class="w-9 h-9 rounded-xl bg-surface-100 group-hover:bg-primary-100 flex items-center justify-center transition-colors duration-200">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                  </svg>
                </div>
                <span class="font-medium">{{ $t('navigation.home') }}</span>
              </Link>

              <Link
                v-if="!profile"
                href="/teacher-profile/create"
                class="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-slate-700 hover:bg-primary-50 hover:text-primary-700 transition-all duration-200 group"
              >
                <div class="w-9 h-9 rounded-xl bg-surface-100 group-hover:bg-primary-100 flex items-center justify-center transition-colors duration-200">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                  </svg>
                </div>
                <span class="font-medium">{{ $t('navigation.add_profile') }}</span>
              </Link>

              <Link
                v-else
                href="/teacher-profile/edit"
                class="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-slate-700 hover:bg-primary-50 hover:text-primary-700 transition-all duration-200 group"
              >
                <div class="w-9 h-9 rounded-xl bg-surface-100 group-hover:bg-primary-100 flex items-center justify-center transition-colors duration-200">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                </div>
                <span class="font-medium">{{ $t('messages.edit_profile') }}</span>
              </Link>
            </div>
          </div>

          <!-- Info Card -->
          <div class="card p-6 bg-gradient-to-br from-primary-600 to-primary-800 text-white animate-fade-in-up animate-delay-300">
            <div class="flex items-center gap-3 mb-4">
              <div class="w-10 h-10 rounded-xl bg-white/15 backdrop-blur flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <h3 class="font-bold">{{ $t('messages.tip_title') }}</h3>
            </div>
            <p class="text-primary-100 text-sm leading-relaxed">
              {{ $t('messages.tip_desc') }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const page = usePage()
const profile = computed(() => page.props.auth.user?.teacher_profile)

const statusCardClass = computed(() => {
  const map = {
    pending: 'bg-amber-50 border-amber-200',
    approved: 'bg-emerald-50 border-emerald-200',
    rejected: 'bg-red-50 border-red-200',
  }
  return map[profile.value?.status] || ''
})

const statusIconClass = computed(() => {
  const map = {
    pending: 'bg-amber-100 text-amber-600',
    approved: 'bg-emerald-100 text-emerald-600',
    rejected: 'bg-red-100 text-red-600',
  }
  return map[profile.value?.status] || ''
})

const statusTextClass = computed(() => {
  const map = {
    pending: 'text-amber-800',
    approved: 'text-emerald-800',
    rejected: 'text-red-800',
  }
  return map[profile.value?.status] || ''
})

const statusDescClass = computed(() => {
  const map = {
    pending: 'text-amber-600',
    approved: 'text-emerald-600',
    rejected: 'text-red-600',
  }
  return map[profile.value?.status] || ''
})

const statusText = computed(() => {
  const map = {
    pending: 'قيد المراجعة',
    approved: 'مقبول',
    rejected: 'مرفوض',
  }
  return map[profile.value?.status] || ''
})

const statusDescription = computed(() => {
  const map = {
    pending: 'بياناتك قيد المراجعة من قبل الإدارة، سيتم إخطارك عند القبول',
    approved: 'تم قبول بياناتك وستظهر في قائمة المدرسين المتاحين',
    rejected: 'لم يتم قبول بياناتك، يمكنك تعديلها وإعادة التقديم',
  }
  return map[profile.value?.status] || ''
})
</script>
