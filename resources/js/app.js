
import { createApp, h } from 'vue'
import { createInertiaApp, router } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createI18n } from 'vue-i18n'
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
import en from './i18n/en.json'
import ar from './i18n/ar.json'

window.Pusher = Pusher

if (import.meta.env.VITE_REVERB_APP_KEY) {
  window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST || 'localhost',
    wsPort: import.meta.env.VITE_REVERB_PORT || 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT || 8080,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME || 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
  })
}

// Initialize i18n
const i18n = createI18n({
  legacy: false,
  locale: localStorage.getItem('locale') || 'ar',
  fallbackLocale: 'en',
  messages: {
    en,
    ar,
  },
})

const appName = 'Teachers'

const titleMap = {
  'Home': 'المدرسين',
  'Dashboard': 'لوحة التحكم',
  'Admin/Dashboard': 'لوحة التحكم',
  'Admin/TeachersIndex': 'إدارة المدرسين',
  'Admin/TeacherDetails': 'تفاصيل المدرس',
  'Admin/Subjects': 'إدارة المواد',
  'Admin/Grades': 'إدارة الصفوف',
  'Admin/Settings': 'إعدادات النظام',
  'Teacher/ProfileForm': 'الملف الشخصي',
  'Profile/Edit': 'الملف الشخصي',
}

function updateTitle() {
  const page = router.page
  if (!page) return
  const siteName = page.props?.settings?.site_name || appName
  const pageTitle = titleMap[page.component]
  document.title = pageTitle ? `${pageTitle} - ${siteName}` : siteName
}

router.on('navigate', updateTitle)

createInertiaApp({
  resolve: (name) =>
    resolvePageComponent(
      `./Pages/${name}.vue`,
      import.meta.glob('./Pages/**/*.vue'),
    ),
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })

    app.use(plugin)
    app.use(i18n)

    // Expose user ID for Echo channel auth
    if (props.initialPage.props.auth?.user) {
      window.__userId = props.initialPage.props.auth.user.id
    }

    // Set locale and direction on app mount
    const locale = i18n.global.locale.value
    document.documentElement.lang = locale
    document.documentElement.dir = locale === 'ar' ? 'rtl' : 'ltr'

    // Set initial title
    updateTitle()

    app.mount(el)
  },
  progress: {
    color: '#14b8a6',
  },
})
