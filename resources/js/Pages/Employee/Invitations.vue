<template>
  <DashboardLayout>
    <div>
      <div class="flex items-center justify-between mb-8 animate-fade-in-up">
        <div>
          <h1 class="text-3xl font-extrabold text-slate-900">دعوات التوظيف</h1>
          <p class="text-slate-500 mt-1">الدعوات المرسلة إليك من المدارس</p>
        </div>
      </div>

      <div v-if="invitations.length === 0" class="card p-10 text-center animate-fade-in-up">
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
          v-for="invitation in invitations"
          :key="invitation.id"
          class="card p-5 animate-fade-in-up"
        >
          <div class="flex items-start gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-100 to-surface-100 overflow-hidden flex-shrink-0 shadow-sm">
              <div class="w-full h-full flex items-center justify-center text-xl">🏫</div>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-start justify-between gap-4">
                <div>
                  <h3 class="text-lg font-bold text-slate-900">{{ invitation.school?.name || 'مدرسة' }}</h3>
                  <p class="text-sm text-slate-500">{{ invitation.school?.email }}</p>
                </div>
                <span
                  class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold whitespace-nowrap"
                  :class="{
                    'bg-amber-100 text-amber-700': invitation.status === 'invited',
                    'bg-emerald-100 text-emerald-700': invitation.status === 'accepted',
                    'bg-blue-100 text-blue-700': invitation.status === 'interviewed',
                    'bg-purple-100 text-purple-700': invitation.status === 'hired',
                  }"
                >
                  <span class="w-1.5 h-1.5 rounded-full" :class="{
                    'bg-amber-500': invitation.status === 'invited',
                    'bg-emerald-500': invitation.status === 'accepted',
                    'bg-blue-500': invitation.status === 'interviewed',
                    'bg-purple-500': invitation.status === 'hired',
                  }"></span>
                  {{ statusLabel(invitation.status) }}
                </span>
              </div>

              <p v-if="invitation.message" class="text-sm text-slate-600 mt-3 bg-surface-50 p-3 rounded-xl border border-surface-100">
                {{ invitation.message }}
              </p>

              <div class="mt-3 flex flex-wrap gap-2">
                <template v-if="invitation.status === 'invited'">
                  <button @click="acceptInvitation(invitation.id)" class="btn-primary text-sm px-6">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                    قبول الدعوة
                  </button>
                  <button @click="declineInvitation(invitation.id)" class="btn-ghost text-sm px-4 text-red-600 hover:bg-red-50">
                    رفض
                  </button>
                </template>

                <template v-if="invitation.status === 'accepted'">
                  <div class="w-full p-3 rounded-xl bg-blue-50 border border-blue-200">
                    <p class="text-sm font-semibold text-blue-800">تم قبول الدعوة، بانتظار إجراء المقابلة</p>
                  </div>
                </template>

                <template v-if="invitation.status === 'hired'">
                  <div class="w-full p-3 rounded-xl bg-emerald-50 border border-emerald-200">
                    <p class="text-sm font-semibold text-emerald-800">تم توظيفك بنجاح في {{ invitation.school?.name }}</p>
                  </div>
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
  invitations: Array,
})

const statusLabel = (status) => {
  const map = {
    invited: 'دعوة جديدة',
    accepted: 'تم القبول',
    interviewed: 'تمت المقابلة',
    hired: 'تم التوظيف',
  }
  return map[status] || status
}

const acceptInvitation = (id) => {
  useForm({}).patch(`/employee/invitations/${id}/accept`, {
    preserveScroll: true,
    onSuccess: () => location.reload(),
  })
}

const declineInvitation = (id) => {
  useForm({}).patch(`/employee/invitations/${id}/decline`, {
    preserveScroll: true,
    onSuccess: () => location.reload(),
  })
}
</script>
