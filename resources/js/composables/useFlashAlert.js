import { watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import Swal from 'sweetalert2'

export function useFlashAlert() {
  const page = usePage()

  watch(() => page.props.flash?.message, (message) => {
    if (!message) return

    const status = page.props.flash?.status || 'success'

    const isSuccess = status === 'success'
    const icon = isSuccess ? 'success' : 'error'

    Swal.fire({
      icon,
      title: isSuccess ? 'تم بنجاح' : 'خطأ',
      text: message,
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 4000,
      timerProgressBar: true,
      customClass: {
        popup: 'rounded-2xl shadow-xl font-sans',
        title: 'text-sm font-bold',
        htmlContainer: 'text-xs',
      },
    })
  })

  watch(() => page.props.errors, (errors) => {
    if (!errors || Object.keys(errors).length === 0) return

    const firstError = Object.values(errors)[0]
    const msg = Array.isArray(firstError) ? firstError[0] : firstError

    Swal.fire({
      icon: 'error',
      title: 'خطأ في التحقق',
      text: msg,
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 5000,
      timerProgressBar: true,
      customClass: {
        popup: 'rounded-2xl shadow-xl font-sans',
        title: 'text-sm font-bold',
        htmlContainer: 'text-xs',
      },
    })
  })
}
