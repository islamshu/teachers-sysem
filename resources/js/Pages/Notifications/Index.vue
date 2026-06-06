<template>
  <DashboardLayout>
    <div class="card">
      <div class="p-4 sm:p-6 border-b border-surface-200">
        <h1 class="text-lg sm:text-2xl font-extrabold text-slate-900">كل الإشعارات</h1>
      </div>

      <div class="divide-y divide-surface-100">
        <div v-if="notifications.length === 0" class="text-center py-12 sm:py-16 text-slate-400">
          <svg class="w-12 h-12 sm:w-16 sm:h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
          </svg>
          <p>لا توجد إشعارات</p>
        </div>

        <div
          v-for="notification in notifications"
          :key="notification.id"
          class="flex items-start gap-2 sm:gap-4 px-3 sm:px-6 py-3 sm:py-4 transition-colors"
          :class="[notification.read_at ? 'hover:bg-surface-50' : 'bg-primary-50/30']"
        >
          <div
            class="w-2.5 h-2.5 sm:w-3 sm:h-3 mt-1.5 rounded-full flex-shrink-0"
            :class="notification.read_at ? 'bg-surface-300' : 'bg-primary-500'"
          ></div>
          <div class="flex-1 min-w-0">
            <a :href="notification.data.url || '#'" @click.prevent="markAsRead(notification.id)" class="block">
              <p class="text-xs sm:text-sm font-semibold text-slate-900">{{ notification.data.title }}</p>
              <p class="text-xs sm:text-sm text-slate-600 mt-0.5">{{ notification.data.body }}</p>
              <p class="text-xs text-slate-400 mt-1">{{ timeAgo(notification.created_at) }}</p>
            </a>
          </div>
          <button
            v-if="!notification.read_at"
            @click="markAsRead(notification.id)"
            class="btn-ghost px-2 sm:px-3 py-1 sm:py-1.5 text-xs flex-shrink-0"
          >تحديد كمقروء</button>
        </div>
      </div>

      <div v-if="nextPage" class="p-4 sm:p-6 text-center border-t border-surface-200">
        <button
          @click="loadMore"
          class="btn-ghost px-4 sm:px-6 py-2 text-xs sm:text-sm"
          :disabled="loading"
        >
          {{ loading ? 'جاري التحميل...' : 'تحميل المزيد' }}
        </button>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref } from 'vue'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { apiFetch } from '@/utils/api'

const props = defineProps({
  notifications: { type: Array, default: () => [] },
  nextPage: { type: Number, default: null },
})

const allNotifications = ref([...props.notifications])
const currentPage = ref(props.nextPage ? 2 : null)
const loading = ref(false)

const markAsRead = async (id) => {
  try {
    await apiFetch(`/notifications/${id}/read`, { method: 'POST' })
    const n = allNotifications.value.find(n => n.id === id)
    if (n) n.read_at = new Date().toISOString()
  } catch {
    // ignore
  }
}

const loadMore = async () => {
  if (!currentPage.value || loading.value) return
  loading.value = true
  try {
    const data = await apiFetch(`/notifications?page=${currentPage.value}`)
    allNotifications.value.push(...(data.notifications || []))
    currentPage.value = data.nextPage
  } catch {
    // ignore
  } finally {
    loading.value = false
  }
}

const timeAgo = (date) => {
  const diff = Date.now() - new Date(date).getTime()
  const mins = Math.floor(diff / 60000)
  if (mins < 1) return 'الآن'
  if (mins < 60) return `منذ ${mins} دقيقة`
  const hours = Math.floor(mins / 60)
  if (hours < 24) return `منذ ${hours} ساعة`
  const days = Math.floor(hours / 24)
  if (days < 7) return `منذ ${days} أيام`
  return new Date(date).toLocaleDateString('ar-SA')
}
</script>
