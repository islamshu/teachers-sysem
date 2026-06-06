<template>
  <div class="min-h-screen bg-surface-50">
    <!-- Background decoration -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden">
      <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary-500/10 rounded-full blur-3xl"></div>
      <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-warm-500/10 rounded-full blur-3xl"></div>
      <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-primary-400/5 rounded-full blur-3xl"></div>
    </div>

    <!-- Navigation -->
    <nav class="sticky top-0 z-50 glass border-b border-surface-200/50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <!-- Logo -->
          <Link href="/" class="flex items-center gap-3 group">
            <template v-if="$page.props.settings?.site_logo">
              <div class="relative w-10 h-10 rounded-xl overflow-hidden shadow-lg shadow-primary-500/20 group-hover:shadow-primary-500/30 transition-all duration-300 group-hover:scale-105">
                <img :src="`/storage/${$page.props.settings.site_logo}`" alt="Logo" class="w-full h-full object-cover" />
              </div>
            </template>
            <template v-else>
              <div class="relative w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl flex items-center justify-center shadow-lg shadow-primary-500/20 group-hover:shadow-primary-500/30 transition-all duration-300 group-hover:scale-105">
                <span class="text-white font-extrabold text-lg">م</span>
              </div>
            </template>
            <span class="text-xl font-extrabold bg-gradient-to-l from-primary-600 to-primary-800 bg-clip-text text-transparent">
              {{ $page.props.settings?.site_name || $t('app.name') }}
            </span>
          </Link>

          <!-- Navigation Links & Controls -->
          <div class="flex items-center gap-2">
            <!-- Desktop Nav Links -->
            <nav class="hidden md:flex items-center gap-1 ml-4">
              <Link
                href="/"
                class="px-4 py-2 rounded-xl font-medium text-slate-600 hover:text-primary-700 hover:bg-primary-50 transition-all duration-200"
                :class="{ 'text-primary-700 bg-primary-50': $page.component === 'Home' }"
              >
                {{ $t('navigation.home') }}
              </Link>
              <Link
                v-if="$page.props.auth.user"
                href="/dashboard"
                class="px-4 py-2 rounded-xl font-medium text-slate-600 hover:text-primary-700 hover:bg-primary-50 transition-all duration-200"
                :class="{ 'text-primary-700 bg-primary-50': $page.component === 'Dashboard' || $page.component === 'Admin/Dashboard' }"
              >
                {{ $t('navigation.dashboard') }}
              </Link>
              
              <!-- Teachers Dropdown -->
              <div v-if="$page.props.auth.user?.is_admin" class="relative">
                <button
                  @click="teachersDropdown = !teachersDropdown"
                  class="flex items-center gap-1 px-4 py-2 rounded-xl font-medium text-slate-600 hover:text-primary-700 hover:bg-primary-50 transition-all duration-200"
                  :class="{ 'text-primary-700 bg-primary-50': showTeachersActive }"
                >
                  المدرسون
                  <svg class="w-3 h-3 transition-transform duration-200" :class="{ 'rotate-180': teachersDropdown }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </button>
                <div v-if="teachersDropdown" @click="teachersDropdown = false" class="fixed inset-0 z-10"></div>
                <div
                  v-if="teachersDropdown"
                  class="absolute top-full mt-1 right-0 w-52 z-20 glass rounded-2xl shadow-xl border border-surface-200/50 py-2 animate-fade-in-down"
                >
                  <Link href="/admin/teachers?status=pending" @click="teachersDropdown = false" class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-primary-50 hover:text-primary-700 transition-colors">
                    قيد المراجعة
                  </Link>
                  <Link href="/admin/teachers?status=approved" @click="teachersDropdown = false" class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-primary-50 hover:text-primary-700 transition-colors">
                    المقبولون
                  </Link>
                  <Link href="/admin/teachers?status=rejected" @click="teachersDropdown = false" class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-primary-50 hover:text-primary-700 transition-colors">
                    المرفوضون
                  </Link>
                </div>
              </div>
              <Link
                v-if="$page.props.auth.user?.is_admin"
                href="/admin/schools"
                class="px-4 py-2 rounded-xl font-medium text-slate-600 hover:text-primary-700 hover:bg-primary-50 transition-all duration-200"
                :class="{ 'text-primary-700 bg-primary-50': $page.component === 'Admin/SchoolsIndex' }"
              >
                المدارس
              </Link>
              <Link
                v-if="$page.props.auth.user?.is_admin"
                href="/admin/subjects"
                class="px-4 py-2 rounded-xl font-medium text-slate-600 hover:text-primary-700 hover:bg-primary-50 transition-all duration-200"
                :class="{ 'text-primary-700 bg-primary-50': $page.component === 'Admin/Subjects' }"
              >
                المواد
              </Link>
              <Link
                v-if="$page.props.auth.user?.is_admin"
                href="/admin/grades"
                class="px-4 py-2 rounded-xl font-medium text-slate-600 hover:text-primary-700 hover:bg-primary-50 transition-all duration-200"
                :class="{ 'text-primary-700 bg-primary-50': $page.component === 'Admin/Grades' }"
              >
                الصفوف
              </Link>
              <Link
                v-if="$page.props.auth.user?.is_admin"
                href="/admin/settings"
                class="px-4 py-2 rounded-xl font-medium text-slate-600 hover:text-primary-700 hover:bg-primary-50 transition-all duration-200"
                :class="{ 'text-primary-700 bg-primary-50': $page.component === 'Admin/Settings' }"
              >
                الإعدادات
              </Link>
            </nav>

            <!-- Mobile Hamburger -->
            <button @click="showMobileMenu = true" class="md:hidden p-2 rounded-xl hover:bg-surface-100 transition-all duration-200">
              <svg class="w-6 h-6 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>

            <!-- Auth & Language -->
            <div class="flex items-center gap-2 pr-4 border-r border-surface-200">
              <template v-if="!$page.props.auth.user">
                <Link
                  href="/login"
                  class="btn-ghost px-4 py-2 text-sm"
                >
                  {{ $t('navigation.login') }}
                </Link>
                <Link
                  href="/register"
                  class="btn-primary px-5 py-2 text-sm"
                >
                  {{ $t('navigation.register') }}
                </Link>
              </template>
              <template v-else>
                <template v-if="!$page.props.auth.user.is_admin">
                <Link
                  v-if="$page.props.auth.user.role === 'school' && !$page.props.auth.user.school_profile"
                  href="/school-profile/create"
                  class="btn-primary px-5 py-2 text-sm"
                >
                  إضافة بيانات المدرسة
                </Link>
                <Link
                  v-else-if="$page.props.auth.user.role === 'teacher' && !$page.props.auth.user.teacher_profile"
                  href="/teacher-profile/create"
                  class="btn-primary px-5 py-2 text-sm"
                >
                  {{ $t('navigation.add_profile') }}
                </Link>
              </template>
                <!-- Notifications -->
                <NotificationBell v-if="$page.props.auth.user" :unread-count="unreadCount" />
                <div class="relative" v-if="$page.props.auth.user">
                  <button
                    @click="showMenu = !showMenu"
                    class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-surface-100 transition-all duration-200"
                  >
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                      {{ $page.props.auth.user.name?.charAt(0) }}
                    </div>
                    <svg class="w-4 h-4 text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': showMenu }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </button>
                  <!-- Dropdown backdrop -->
                  <div v-if="showMenu" @click="showMenu = false" class="fixed inset-0 z-10"></div>
                  <div
                    v-if="showMenu"
                    class="absolute left-0 mt-2 w-48 z-20 glass rounded-2xl shadow-xl border border-surface-200/50 py-2 animate-fade-in-down"
                  >
                    <div class="px-4 py-2 border-b border-surface-100">
                      <p class="text-sm font-medium text-slate-900 truncate">{{ $page.props.auth.user.name }}</p>
                      <p class="text-xs text-slate-500 truncate">{{ $page.props.auth.user.email }}</p>
                    </div>
                    <Link href="/dashboard" class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-primary-50 hover:text-primary-700 transition-colors">
                      {{ $t('navigation.dashboard') }}
                    </Link>
                    <Link href="/profile" class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-primary-50 hover:text-primary-700 transition-colors">
                      الملف الشخصي
                    </Link>
                    <form @submit.prevent="logout">
                      <button type="submit" class="w-full text-right px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                        {{ $t('navigation.logout') }}
                      </button>
                    </form>
                  </div>
                </div>
              </template>

           
            </div>
          </div>
        </div>
      </div>

      <!-- Mobile Menu Overlay -->
      <Teleport to="body">
        <div v-if="showMobileMenu" class="fixed inset-0 z-50 md:hidden">
          <div class="fixed inset-0 bg-black/30 backdrop-blur-sm" @click="showMobileMenu = false"></div>
          <div class="fixed top-0 right-0 h-full w-72 max-w-[85vw] bg-white shadow-2xl animate-slide-in-left">
            <div class="flex items-center justify-between p-4 border-b border-surface-200">
              <span class="text-lg font-extrabold text-slate-900">القائمة</span>
              <button @click="showMobileMenu = false" class="p-2 rounded-xl hover:bg-surface-100 transition-colors">
                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
            <div class="p-4 space-y-1">
              <Link href="/" @click="showMobileMenu = false" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-slate-700 hover:bg-primary-50 hover:text-primary-700 transition-all duration-200"
                :class="{ 'text-primary-700 bg-primary-50': $page.component === 'Home' }">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                {{ $t('navigation.home') }}
              </Link>

              <template v-if="$page.props.auth.user">
                <Link href="/dashboard" @click="showMobileMenu = false" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-slate-700 hover:bg-primary-50 hover:text-primary-700 transition-all duration-200"
                  :class="{ 'text-primary-700 bg-primary-50': $page.component === 'Dashboard' || $page.component === 'Admin/Dashboard' }">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                  </svg>
                  {{ $t('navigation.dashboard') }}
                </Link>

                <div v-if="$page.props.auth.user.is_admin" class="border-t border-surface-100 pt-2 mt-2">
                  <p class="px-4 py-2 text-xs font-bold text-slate-400 uppercase tracking-wider">الإدارة</p>
                  <!-- Mobile Teachers Dropdown -->
                  <button @click="mobileTeachersOpen = !mobileTeachersOpen" class="flex items-center justify-between w-full px-4 py-3 rounded-xl font-medium text-slate-700 hover:bg-primary-50 hover:text-primary-700 transition-all duration-200">
                    <span class="flex items-center gap-3">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                      </svg>
                      المدرسون
                    </span>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': mobileTeachersOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </button>
                  <div v-if="mobileTeachersOpen" class="mr-8 space-y-1 border-r-2 border-primary-200 pr-3">
                    <Link href="/admin/teachers" @click="showMobileMenu = false" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-primary-50 hover:text-primary-700 transition-all duration-200"
                      :class="{ 'text-primary-700 bg-primary-50': $page.component === 'Admin/TeachersIndex' && $page.props.currentStatus !== 'approved' && $page.props.currentStatus !== 'rejected' }">
                      قيد المراجعة
                    </Link>
                    <Link href="/admin/teachers?status=approved" @click="showMobileMenu = false" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-primary-50 hover:text-primary-700 transition-all duration-200"
                      :class="{ 'text-primary-700 bg-primary-50': $page.component === 'Admin/TeachersIndex' && $page.props.currentStatus === 'approved' }">
                      المقبولون
                    </Link>
                    <Link href="/admin/teachers?status=rejected" @click="showMobileMenu = false" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-primary-50 hover:text-primary-700 transition-all duration-200"
                      :class="{ 'text-primary-700 bg-primary-50': $page.component === 'Admin/TeachersIndex' && $page.props.currentStatus === 'rejected' }">
                      المرفوضون
                    </Link>
                  </div>
                  <Link href="/admin/schools" @click="showMobileMenu = false" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-slate-700 hover:bg-primary-50 hover:text-primary-700 transition-all duration-200"
                    :class="{ 'text-primary-700 bg-primary-50': $page.component === 'Admin/SchoolsIndex' }">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    المدارس
                  </Link>
                  <Link href="/admin/subjects" @click="showMobileMenu = false" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-slate-700 hover:bg-primary-50 hover:text-primary-700 transition-all duration-200"
                    :class="{ 'text-primary-700 bg-primary-50': $page.component === 'Admin/Subjects' }">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    المواد
                  </Link>
                  <Link href="/admin/grades" @click="showMobileMenu = false" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-slate-700 hover:bg-primary-50 hover:text-primary-700 transition-all duration-200"
                    :class="{ 'text-primary-700 bg-primary-50': $page.component === 'Admin/Grades' }">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    الصفوف
                  </Link>
                  <Link href="/admin/settings" @click="showMobileMenu = false" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-slate-700 hover:bg-primary-50 hover:text-primary-700 transition-all duration-200"
                    :class="{ 'text-primary-700 bg-primary-50': $page.component === 'Admin/Settings' }">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    الإعدادات
                  </Link>
                </div>
              </template>
            </div>
          </div>
        </div>
      </Teleport>
    </nav>

    <!-- Page Content -->
    <main class="relative">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 animate-fade-in">
        <slot />
      </div>
    </main>

    <!-- Footer -->
    <footer class="relative border-t border-surface-200 bg-white/50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
          <p class="text-sm text-slate-500">
            © {{ new Date().getFullYear() }} {{ $page.props.settings?.site_name || $t('app.name') }}. {{ $t('messages.footer_rights') }}
          </p>
          <!-- <p class="text-sm text-slate-400">
            {{ $t('messages.made_with') }} ❤️
          </p> -->
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { applyPrimaryColor } from '@/utils/colors'
import NotificationBell from '@/Components/NotificationBell.vue'
import { useNotifications } from '@/composables/useNotifications'

const { unreadCount, startPolling, stopPolling } = useNotifications()

onMounted(() => {
  if (page.props.auth.user) startPolling()
})
onUnmounted(() => stopPolling())

const { locale } = useI18n()
const currentLocale = ref(locale.value)
const showMenu = ref(false)
const showMobileMenu = ref(false)
const teachersDropdown = ref(false)
const mobileTeachersOpen = ref(false)

const page = usePage()
const showTeachersActive = computed(() =>
  ['Admin/TeachersIndex', 'Admin/TeacherDetails'].includes(page.component)
)

onMounted(() => {
  const color = page.props.settings?.primary_color
  if (color) {
    applyPrimaryColor(color)
  }
})

const changeLocale = () => {
  locale.value = currentLocale.value
  localStorage.setItem('locale', currentLocale.value)
  document.documentElement.lang = currentLocale.value
  document.documentElement.dir = currentLocale.value === 'ar' ? 'rtl' : 'ltr'
}

const form = useForm({})
const logout = () => form.post('/logout')
</script>
