<template>
  <div class="relative">
    <button
      @click="toggleDropdown"
      class="relative p-2 rounded-xl hover:bg-surface-100 transition-all duration-200"
      title="الإشعارات"
    >
      <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
      </svg>
      <span
        v-if="unreadCount > 0"
        class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] flex items-center justify-center px-1 text-[10px] font-bold text-white bg-red-500 rounded-full shadow-sm animate-pulse-once"
      >
        {{ unreadCount > 99 ? '99+' : unreadCount }}
      </span>
    </button>

    <div v-if="open" @click="open = false" class="fixed inset-0 z-30 sm:bg-transparent bg-black/20 backdrop-blur-sm"></div>
    <div
      v-if="open"
      class="fixed sm:absolute left-2 sm:left-auto sm:right-0 top-16 sm:top-full sm:mt-2 right-2 z-40 glass rounded-2xl shadow-xl border border-surface-200/50 overflow-hidden animate-fade-in-down sm:w-80"
    >
      <div class="flex items-center justify-between px-3 sm:px-4 py-3 border-b border-surface-100">
        <h3 class="text-sm font-bold text-slate-900">الإشعارات</h3>
        <button
          v-if="notifications.length > 0"
          @click.stop="markAllRead"
          class="text-xs text-primary-600 hover:text-primary-800 transition-colors"
        >
          تحديد الكل كمقروء
        </button>
      </div>

      <div class="max-h-80 overflow-y-auto">
        <div v-if="loading" class="flex items-center justify-center py-8">
          <div class="w-6 h-6 border-2 border-primary-500 border-t-transparent rounded-full animate-spin"></div>
        </div>
        <div v-else-if="notifications.length === 0" class="text-center py-8 text-sm text-slate-400">
          <svg class="w-10 h-10 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
          </svg>
          لا توجد إشعارات
        </div>
        <template v-else>
          <div
            v-for="notification in notifications"
            :key="notification.id"
            class="flex items-start gap-2 sm:gap-3 px-3 sm:px-4 py-2.5 sm:py-3 transition-colors duration-150"
            :class="[notification.read_at ? 'hover:bg-surface-50' : 'bg-primary-50/40 hover:bg-primary-50']"
          >
            <div
              class="w-1.5 h-1.5 sm:w-2 sm:h-2 mt-2 rounded-full flex-shrink-0"
              :class="notification.read_at ? 'bg-transparent' : 'bg-primary-500'"
            ></div>
            <div class="flex-1 min-w-0">
              <a
                :href="notification.data.url || '#'"
                @click="markAsRead(notification.id)"
                class="block"
              >
                <p class="text-xs sm:text-sm font-medium text-slate-900 truncate">{{ notification.data.title }}</p>
                <p class="text-xs text-slate-500 mt-0.5 line-clamp-2">{{ notification.data.body }}</p>
                <p class="text-[10px] text-slate-400 mt-1">{{ timeAgo(notification.created_at) }}</p>
              </a>
            </div>
            <button
              v-if="!notification.read_at"
              @click.stop="markAsRead(notification.id)"
              class="flex-shrink-0 p-1 rounded-lg hover:bg-surface-200 transition-colors"
              title="تحديد كمقروء"
            >
              <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
            </button>
          </div>
        </template>
      </div>

      <Link
        v-if="notifications.length > 0"
        href="/notifications"
        class="block text-center text-xs text-primary-600 hover:text-primary-800 py-3 border-t border-surface-100 transition-colors"
      >
        عرض كل الإشعارات
      </Link>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { apiFetch } from '@/utils/api'

const props = defineProps({
  unreadCount: { type: Number, default: 0 },
})

const open = ref(false)
const notifications = ref([])
const loading = ref(false)

const toggleDropdown = async () => {
  open.value = !open.value
  if (open.value && notifications.value.length === 0) {
    await fetchNotifications()
  }
}

const fetchNotifications = async () => {
  loading.value = true
  try {
    const countData = await apiFetch('/notifications/unread-count')
    if (countData.count > 0) {
      const data = await apiFetch('/notifications?page=1')
      notifications.value = data.notifications || []
    }
  } catch {
    // ignore
  } finally {
    loading.value = false
  }
}

const markAsRead = async (id) => {
  try {
    await apiFetch(`/notifications/${id}/read`, { method: 'POST' })
    const n = notifications.value.find(n => n.id === id)
    if (n) n.read_at = new Date().toISOString()
  } catch {
    // ignore
  }
}

const markAllRead = async () => {
  try {
    await apiFetch('/notifications/mark-all-read', { method: 'POST' })
    notifications.value.forEach(n => n.read_at = new Date().toISOString())
  } catch {
    // ignore
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
  const d = new Date(date)
  const pad = (n) => String(n).padStart(2, '0')
  return `${d.getFullYear()}/${pad(d.getMonth() + 1)}/${pad(d.getDate())}`
}
</script>
