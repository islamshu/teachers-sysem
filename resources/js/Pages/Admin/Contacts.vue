<template>
  <DashboardLayout>
    <div>
      <div class="relative mb-8 overflow-hidden rounded-3xl bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 shadow-2xl shadow-primary-500/20 animate-fade-in-up">
        <div class="absolute inset-0 bg-grid opacity-20"></div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-primary-400/20 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
        <div class="relative px-8 py-10">
          <div class="flex items-center gap-4 mb-3">
            <div class="w-14 h-14 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center text-3xl shadow-lg">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
            </div>
            <div>
              <h1 class="text-3xl font-extrabold text-white">رسائل التواصل</h1>
              <p class="text-primary-200">جميع الرسائل الواردة من الزوار</p>
            </div>
          </div>
        </div>
      </div>

      <div class="card animate-fade-in-up">
        <div class="p-6 border-b border-surface-200 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
            </div>
            <h2 class="text-lg font-bold text-slate-900">الرسائل</h2>
          </div>
          <div class="text-sm font-semibold text-slate-500">{{ contacts.length }} رسالة</div>
        </div>

        <div v-if="contacts.length > 0" class="divide-y divide-surface-100">
          <div
            v-for="contact in contacts"
            :key="contact.id"
            class="p-6 hover:bg-surface-50 transition-colors"
            :class="{ 'bg-primary-50/30': !contact.is_read }"
          >
            <div class="flex items-start gap-4">
              <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary-100 to-surface-100 flex items-center justify-center flex-shrink-0">
                <span class="text-lg font-bold text-primary-600">{{ contact.name.charAt(0) }}</span>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                  <h3 class="font-bold text-slate-900">{{ contact.name }}</h3>
                  <span v-if="!contact.is_read" class="px-2 py-0.5 rounded-full bg-red-100 text-red-700 text-xs font-bold">جديد</span>
                </div>
                <div class="flex items-center gap-3 text-sm text-slate-500 mb-3">
                  <span>{{ contact.email }}</span>
                  <span v-if="contact.phone">· {{ contact.phone }}</span>
                  <span>· {{ contact.created_at }}</span>
                </div>
                <p class="text-slate-700 leading-relaxed whitespace-pre-line">{{ contact.message }}</p>
                <div class="mt-3 flex items-center gap-2">
                  <button
                    v-if="!contact.is_read"
                    @click="markAsRead(contact.id)"
                    class="btn-ghost px-3 py-1.5 text-xs"
                  >
                    تحديد كمقروء
                  </button>
                  <a
                    :href="`mailto:${contact.email}`"
                    class="btn-primary px-3 py-1.5 text-xs inline-flex items-center gap-1"
                  >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    رد
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="p-16 text-center">
          <div class="w-20 h-20 rounded-3xl bg-surface-100 flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
          </div>
          <h3 class="text-xl font-bold text-slate-900 mb-2">لا توجد رسائل</h3>
          <p class="text-slate-500">لم يتم استقبال أي رسائل من الزوار بعد</p>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const props = defineProps({
  contacts: Array,
})

const markAsRead = (id) => {
  fetch(`/admin/contacts/${id}/read`, { method: 'PATCH', headers: { Accept: 'application/json' } })
    .then(() => {
      const contact = props.contacts.find(c => c.id === id)
      if (contact) contact.is_read = true
    })
}
</script>
