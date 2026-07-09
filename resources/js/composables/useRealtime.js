export function useRealtime() {
  const listenForTasks = (callback) => {
    if (!window.Echo) return
    window.Echo.private('tasks')
      .listen('.task.updated', (e) => {
        callback(e)
      })
  }

  const listenForPurchases = (callback) => {
    if (!window.Echo) return
    window.Echo.private('purchases')
      .listen('.purchase.updated', (e) => {
        callback(e)
      })
  }

  const listenForEmployment = (channel, callback) => {
    if (!window.Echo) return
    window.Echo.private(channel)
      .listen('.employment.updated', (e) => {
        callback(e)
      })
  }

  return { listenForTasks, listenForPurchases, listenForEmployment }
}
