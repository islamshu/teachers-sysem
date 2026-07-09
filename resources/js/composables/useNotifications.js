import { ref, onMounted, onUnmounted } from 'vue'
import { apiFetch } from '@/utils/api'

const unreadCount = ref(0)
let echo = null

export function useNotifications() {
  const fetchUnread = async () => {
    try {
      const data = await apiFetch('/notifications/unread-count')
      unreadCount.value = data.count
    } catch {
      //
    }
  }

  const startListening = () => {
    fetchUnread()
    if (window.Echo) {
      const userId = window.__userId
      if (userId) {
        echo = window.Echo.private(`App.Models.User.${userId}`)
          .notification((notification) => {
            unreadCount.value++
          })
      }
    }
  }

  const stopListening = () => {
    if (echo) {
      echo.stopListening('.notification')
      echo = null
    }
  }

  return { unreadCount, fetchUnread, startListening, stopListening }
}
