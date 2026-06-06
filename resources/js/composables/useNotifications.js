import { ref } from 'vue'
import { apiFetch } from '@/utils/api'

const unreadCount = ref(0)
let interval = null

export function useNotifications() {
  const fetchUnread = async () => {
    try {
      const data = await apiFetch('/notifications/unread-count')
      unreadCount.value = data.count
    } catch {
      // ignore
    }
  }

  const startPolling = () => {
    fetchUnread()
    interval = setInterval(fetchUnread, 15000)
  }

  const stopPolling = () => {
    if (interval) {
      clearInterval(interval)
      interval = null
    }
  }

  return { unreadCount, fetchUnread, startPolling, stopPolling }
}
