<template>
  <DashboardLayout>
    <div>
      <div class="flex items-center justify-between mb-8 animate-fade-in-up">
        <div>
          <h1 class="text-3xl font-extrabold text-slate-900">دعوات التوظيف</h1>
          <p class="text-slate-500 mt-1">الدعوات المرسلة إليك من المدارس</p>
        </div>
      </div>

      <div v-if="employments.length === 0" class="card p-10 text-center animate-fade-in-up">
        <div class="w-16 h-16 rounded-2xl bg-surface-100 flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
          </svg>
        </div>
        <h3 class="text-lg font-bold text-slate-900 mb-1">لا توجد دعوات</h3>
        <p class="text-sm text-slate-500">لم تصلك أي دعوات توظيف بعد</p>
      </div>

      <div v-else class="space-y-4">
        <div
          v-for="employment in employments"
          :key="employment.id"
          class="card p-5 animate-fade-in-up"
        >
          <div class="flex items-start gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-100 to-surface-100 overflow-hidden flex-shrink-0 shadow-sm">
              <img v-if="employment.school?.school_profile?.logo" :src="`/storage/${employment.school.school_profile.logo}`" :alt="employment.school?.school_profile?.school_name" class="w-full h-full object-cover" />
              <div v-else class="w-full h-full flex items-center justify-center text-xl">🏫</div>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-start justify-between gap-4">
                <div>
                  <h3 class="text-lg font-bold text-slate-900">{{ employment.school?.school_profile?.school_name || 'مدرسة' }}</h3>
                  <span class="badge-primary text-xs">{{ employment.subject?.name }}</span>
                </div>
                <span
                  class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold whitespace-nowrap"
                  :class="{
                    'bg-amber-100 text-amber-700': employment.status === 'invited',
                    'bg-emerald-100 text-emerald-700': employment.status === 'accepted',
                    'bg-blue-100 text-blue-700': employment.status === 'interviewed',
                  }"
                >
                  <span class="w-1.5 h-1.5 rounded-full" :class="{
                    'bg-amber-500': employment.status === 'invited',
                    'bg-emerald-500': employment.status === 'accepted',
                    'bg-blue-500': employment.status === 'interviewed',
                  }"></span>
                  {{ statusLabel(employment.status) }}
                </span>
              </div>

              <p v-if="employment.message" class="text-sm text-slate-600 mt-3 bg-surface-50 p-3 rounded-xl border border-surface-100">
                {{ employment.message }}
              </p>

              <template v-if="employment.status === 'accepted' || employment.status === 'interviewed'">
                <div class="mt-3 p-3 rounded-xl bg-emerald-50 border border-emerald-200">
                  <p class="text-sm font-semibold text-emerald-800 mb-1">📞 معلومات التواصل مع المدرسة</p>
                  <div class="flex flex-wrap gap-4 mt-1 text-sm">
                    <span v-if="employment.contact_phone" class="font-medium text-slate-700" dir="ltr">
                      <a :href="`https://wa.me/${employment.contact_phone.replace(/[^0-9]/g, '')}`" target="_blank" class="text-emerald-600 hover:underline">واتساب</a> · {{ employment.contact_phone }}
                    </span>
                    <span v-if="employment.contact_email" class="font-medium text-slate-700">{{ employment.contact_email }}</span>
                  </div>
                </div>
              </template>

              <div class="mt-3 flex flex-wrap gap-2">
                <template v-if="employment.status === 'invited'">
                  <button @click="acceptInvitation(employment.id)" class="btn-primary text-sm px-6">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                    قبول الدعوة
                  </button>
                  <button @click="declineInvitation(employment.id)" class="btn-ghost text-sm px-4 text-red-600 hover:bg-red-50">
                    رفض
                  </button>
                </template>

                <template v-if="employment.status === 'accepted'">
                  <p class="text-sm text-emerald-700 font-medium">⏳ في انتظار تأكيد المقابلة من المدرسة</p>
                </template>

                <template v-if="employment.status === 'interviewed'">
                  <p class="text-sm text-blue-700 font-medium">🔄 تمت المقابلة، في انتظار نتيجة التوظيف</p>
                </template>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const props = defineProps({
  employments: Array,
})

const statusLabel = (status) => {
  const map = {
    invited: 'دعوة جديدة',
    accepted: 'تم القبول',
    interviewed: 'تمت المقابلة',
  }
  return map[status] || status
}

const acceptInvitation = (id) => {
  useForm({}).patch(`/teacher/employments/${id}/accept`, {
    preserveScroll: true,
    onSuccess: () => location.reload(),
  })
}

const declineInvitation = (id) => {
  useForm({}).patch(`/teacher/employments/${id}/decline`, {
    preserveScroll: true,
    onSuccess: () => location.reload(),
  })
}
</script>
