<template>
  <DashboardLayout>
    <div>
      <!-- Back Button -->
      <button @click="goBack" class="btn-ghost mb-6 animate-fade-in-up">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        {{ $t('messages.back') }}
      </button>

      <!-- Header Card -->
      <div class="card p-8 mb-6 animate-fade-in-up animate-delay-100">
        <div class="flex items-start gap-6">
          <!-- Photo -->
          <div class="flex-shrink-0">
            <div class="w-28 h-28 rounded-2xl bg-gradient-to-br from-primary-100 to-surface-100 overflow-hidden shadow-lg shadow-primary-500/10">
              <img
                v-if="teacher.photo"
                :src="`/storage/${teacher.photo}`"
                :alt="teacher.user?.name"
                class="w-full h-full object-cover"
              />
              <div v-else class="w-full h-full flex items-center justify-center text-4xl">👨‍🏫</div>
            </div>
          </div>

          <!-- Info -->
          <div class="flex-1 min-w-0">
            <h1 class="text-3xl font-extrabold text-slate-900 mb-1">{{ teacher.user?.name }}</h1>
            <p class="text-slate-500 mb-4">{{ teacher.user?.email }}</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
              <div class="p-3 rounded-xl bg-primary-50">
                <p class="text-xs font-semibold text-primary-600 mb-0.5">{{ $t('messages.subject') }}</p>
                <p class="font-bold text-slate-900">{{ teacher.subject?.name }}</p>
              </div>
              <div class="p-3 rounded-xl bg-warm-50">
                <p class="text-xs font-semibold text-warm-600 mb-0.5">{{ $t('messages.experience') }}</p>
                <p class="font-bold text-slate-900">{{ teacher.experience_years }} {{ $t('messages.year') }}</p>
              </div>
              <div class="p-3 rounded-xl bg-emerald-50">
                <p class="text-xs font-semibold text-emerald-600 mb-0.5">{{ $t('messages.qualification') }}</p>
                <p class="font-bold text-slate-900">{{ teacher.qualification || '-' }}</p>
              </div>
              <div>
                <p class="text-xs font-semibold text-slate-500 mb-0.5">{{ $t('messages.status') }}</p>
                <span v-if="teacher.status === 'pending'" class="badge-warning">{{ $t('messages.pending') }}</span>
                <span v-else-if="teacher.status === 'approved'" class="badge-success">{{ $t('messages.approved') }}</span>
                <span v-else class="badge-error">{{ $t('messages.rejected') }}</span>
              </div>
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
              <span class="font-medium text-slate-900 ltr" dir="ltr">{{ teacher.phone || '-' }}</span>
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

        <!-- Practical Experience -->
        <div v-if="teacher.practical_experience" class="lg:col-span-2 card p-6 animate-fade-in-up animate-delay-400">
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
        <div class="lg:col-span-2 card p-6 animate-fade-in-up animate-delay-400">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-rose-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
              </svg>
            </div>
            <h2 class="text-lg font-bold text-slate-900">{{ $t('messages.bio') }}</h2>
          </div>
          <p class="text-slate-700 leading-relaxed whitespace-pre-wrap">
            {{ teacher.bio || 'لم يتم إضافة نبذة' }}
          </p>
        </div>

        <!-- Dates -->
        <div class="lg:col-span-2 card p-6 animate-fade-in-up animate-delay-500">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-surface-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-surface-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
            <h2 class="text-lg font-bold text-slate-900">{{ $t('messages.details') }}</h2>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-xs text-slate-500 font-semibold">{{ $t('messages.request_date') }}</p>
              <p class="font-medium text-slate-900">{{ formatDate(teacher.created_at) }}</p>
            </div>
            <div>
              <p class="text-xs text-slate-500 font-semibold">{{ $t('messages.last_update') }}</p>
              <p class="font-medium text-slate-900">{{ formatDate(teacher.updated_at) }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Delete -->
      <div class="card p-6 mb-6 animate-fade-in-up animate-delay-550">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center">
            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
          </div>
          <h2 class="text-lg font-bold text-slate-900">حذف المدرس</h2>
        </div>
        <p class="text-sm text-slate-600 mb-4">سيتم حذف جميع بيانات المدرس والمستخدم المرتبط به نهائياً.</p>
        <button
          @click="deleteTeacher"
          class="px-6 py-3 rounded-xl bg-red-100 text-red-700 font-semibold hover:bg-red-200 hover:shadow-lg hover:shadow-red-500/20 transition-all duration-200"
        >
          <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
          حذف المدرس
        </button>
      </div>

      <!-- Actions -->
      <div v-if="teacher.status === 'pending'" class="card p-6 animate-fade-in-up animate-delay-600">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 rounded-xl bg-warm-100 flex items-center justify-center">
            <svg class="w-5 h-5 text-warm-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
          </div>
          <h2 class="text-lg font-bold text-slate-900">{{ $t('messages.actions') }}</h2>
        </div>
        <div class="flex flex-wrap gap-4">
          <button
            @click="approveTeacher"
            class="btn-primary px-8"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
            </svg>
            {{ $t('messages.approve') }}
          </button>
          <button
            @click="rejectTeacher"
            class="px-6 py-3 rounded-xl bg-red-100 text-red-700 font-semibold hover:bg-red-200 hover:shadow-lg hover:shadow-red-500/20 transition-all duration-200"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
            </svg>
            {{ $t('messages.reject') }}
          </button>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import Swal from 'sweetalert2'

const props = defineProps({
  teacher: Object,
})

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('ar-EG', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const goBack = () => window.history.back()

const approveTeacher = () => {
  Swal.fire({
    icon: 'question',
    title: 'تأكيد القبول',
    text: 'هل تريد قبول هذا المدرس؟',
    showCancelButton: true,
    confirmButtonText: 'قبول',
    cancelButtonText: 'إلغاء',
    confirmButtonColor: '#10b981',
  }).then((result) => {
    if (result.isConfirmed) {
      useForm({}).patch(`/admin/teachers/${props.teacher.id}/approve`, {
        onSuccess: () => { window.location.href = '/admin/teachers' }
      })
    }
  })
}

const rejectTeacher = () => {
  Swal.fire({
    icon: 'question',
    title: 'تأكيد الرفض',
    text: 'هل تريد رفض هذا المدرس؟',
    showCancelButton: true,
    confirmButtonText: 'رفض',
    cancelButtonText: 'إلغاء',
    confirmButtonColor: '#ef4444',
  }).then((result) => {
    if (result.isConfirmed) {
      useForm({}).patch(`/admin/teachers/${props.teacher.id}/reject`, {
        onSuccess: () => { window.location.href = '/admin/teachers' }
      })
    }
  })
}

const deleteTeacher = () => {
  Swal.fire({
    icon: 'warning',
    title: 'حذف المدرس',
    text: 'سيتم حذف جميع بيانات المدرس والمستخدم المرتبط به نهائياً. لا يمكن التراجع عن هذا الإجراء.',
    showCancelButton: true,
    confirmButtonText: 'نعم، احذف',
    cancelButtonText: 'إلغاء',
    confirmButtonColor: '#ef4444',
  }).then((result) => {
    if (result.isConfirmed) {
      useForm({}).delete(`/admin/teachers/${props.teacher.id}`, {
        onSuccess: () => { window.location.href = '/admin/teachers' }
      })
    }
  })
}
</script>
