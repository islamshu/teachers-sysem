<template>
  <div class="min-h-screen bg-surface-50">
    <!-- Background decoration -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden">
      <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary-500/10 rounded-full blur-3xl"></div>
      <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-warm-500/10 rounded-full blur-3xl"></div>
    </div>

    <!-- Navigation -->
    <nav class="sticky top-0 z-50 glass border-b border-surface-200/50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <!-- Logo -->
          <Link href="/dashboard" class="flex items-center gap-3 group">
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

          <div class="flex items-center gap-2">
            <Link href="/" class="btn-ghost px-4 py-2 text-sm hidden sm:flex" target="_blank">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
              </svg>
              {{ $t('navigation.home') }}
            </Link>

            <!-- Notifications -->
            <NotificationBell :unread-count="unreadCount" />

            <!-- Mobile Sidebar Toggle -->
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-xl hover:bg-surface-100 transition-all duration-200">
              <svg class="w-6 h-6 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>

            <!-- User Menu -->
            <div class="relative">
              <button
                @click="showMenu = !showMenu"
                class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-surface-100 transition-all duration-200"
              >
                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                  {{ $page.props.auth.user?.name?.charAt(0) }}
                </div>
                <svg class="w-4 h-4 text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': showMenu }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>
              <div v-if="showMenu" @click="showMenu = false" class="fixed inset-0 z-10"></div>
              <div
                v-if="showMenu"
                class="absolute left-0 mt-2 w-48 z-20 glass rounded-2xl shadow-xl border border-surface-200/50 py-2 animate-fade-in-down"
              >
                <div class="px-4 py-2 border-b border-surface-100">
                  <p class="text-sm font-medium text-slate-900 truncate">{{ $page.props.auth.user?.name }}</p>
                  <p class="text-xs text-slate-500 truncate">{{ $page.props.auth.user?.email }}</p>
                </div>
                <Link href="/profile" class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-primary-50 hover:text-primary-700 transition-colors">
                  {{ $t('messages.edit') }}
                </Link>
                <form @submit.prevent="logout">
                  <button type="submit" class="w-full text-right px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                    {{ $t('navigation.logout') }}
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <div class="flex max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 gap-6">
      <!-- Sidebar -->
      <aside
        class="w-64 flex-shrink-0"
        :class="sidebarOpen ? 'fixed inset-0 z-40 lg:relative lg:inset-auto' : 'hidden lg:block'"
      >
        <!-- Mobile backdrop -->
        <div v-if="sidebarOpen" class="fixed inset-0 bg-black/30 backdrop-blur-sm lg:hidden" @click="sidebarOpen = false"></div>

        <div
          class="relative bg-white rounded-2xl shadow-sm border border-surface-200 p-4 h-full lg:h-auto overflow-y-auto"
          :class="sidebarOpen ? 'w-72 max-w-[85vw] mt-4 mr-4' : ''"
        >
          <!-- Close button mobile -->
          <button
            v-if="sidebarOpen"
            @click="sidebarOpen = false"
            class="lg:hidden absolute top-3 left-3 p-2 rounded-xl hover:bg-surface-100 transition-colors"
          >
            <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>

          <!-- Admin Navigation -->
          <template v-if="$page.props.auth.user?.is_admin">
            <div class="mb-6">
              <div class="flex items-center gap-3 px-3 py-3 mb-2">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white font-bold text-sm">
                  ⚡
                </div>
                <div>
                  <p class="text-sm font-bold text-slate-900">لوحة الإدارة</p>
                  <p class="text-xs text-slate-500">مرحباً، {{ $page.props.auth.user?.name }}</p>
                </div>
              </div>
            </div>

            <nav class="space-y-1">
              <Link
                href="/dashboard"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-200"
                :class="isActive('/dashboard') ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
              >
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="isActive('/dashboard') ? 'bg-primary-100' : 'bg-surface-100'">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                  </svg>
                </div>
                <span>لوحة التحكم</span>
              </Link>

              <!-- Teachers with submenu -->
              <div>
                <button
                  @click="toggleSubmenu('teachers')"
                  class="flex items-center justify-between w-full px-4 py-3 rounded-xl font-medium transition-all duration-200"
                  :class="teachersActive ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
                >
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="teachersActive ? 'bg-primary-100' : 'bg-surface-100'">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                      </svg>
                    </div>
                    <span>المدرسون</span>
                  </div>
                  <svg class="w-3 h-3 transition-transform duration-200" :class="{ 'rotate-180': openSubmenu === 'teachers' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </button>
                <div v-if="openSubmenu === 'teachers'" class="mr-4 mt-1 space-y-1 border-r-2 border-primary-200 pr-3">
                  <Link
                    href="/admin/teachers"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200"
                    :class="isActive('/admin/teachers') && !$page.props.currentStatus ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
                  >
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    قيد المراجعة
                  </Link>
                  <Link
                    href="/admin/teachers?status=approved"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200"
                    :class="isActive('/admin/teachers') && $page.props.currentStatus === 'approved' ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
                  >
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    المقبولون
                  </Link>
                  <Link
                    href="/admin/teachers?status=rejected"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200"
                    :class="isActive('/admin/teachers') && $page.props.currentStatus === 'rejected' ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
                  >
                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                    المرفوضون
                  </Link>
                </div>
              </div>

              <Link
                href="/admin/schools"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-200"
                :class="isActive('/admin/schools') ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
              >
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="isActive('/admin/schools') ? 'bg-primary-100' : 'bg-surface-100'">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
                </div>
                <span>المدارس</span>
              </Link>

              <Link
                href="/admin/subjects"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-200"
                :class="isActive('/admin/subjects') ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
              >
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="isActive('/admin/subjects') ? 'bg-primary-100' : 'bg-surface-100'">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                  </svg>
                </div>
                <span>المواد</span>
              </Link>

              <Link
                href="/admin/grades"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-200"
                :class="isActive('/admin/grades') ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
              >
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="isActive('/admin/grades') ? 'bg-primary-100' : 'bg-surface-100'">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
                </div>
                <span>الصفوف</span>
              </Link>

              <div class="border-t border-surface-200 my-3"></div>

              <Link
                href="/admin/settings"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-200"
                :class="isActive('/admin/settings') ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
              >
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="isActive('/admin/settings') ? 'bg-primary-100' : 'bg-surface-100'">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                </div>
                <span>الإعدادات</span>
              </Link>
            </nav>
          </template>

          <!-- School Navigation -->
          <template v-else-if="$page.props.auth.user?.role === 'school'">
            <div class="mb-6">
              <div class="flex items-center gap-3 px-3 py-3 mb-2">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white font-bold text-sm">
                  🏫
                </div>
                <div>
                  <p class="text-sm font-bold text-slate-900">لوحة المدرسة</p>
                  <p class="text-xs text-slate-500">مرحباً، {{ $page.props.auth.user?.name }}</p>
                </div>
              </div>
            </div>

            <nav class="space-y-1">
              <Link
                href="/dashboard"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-200"
                :class="isActive('/dashboard') ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
              >
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="isActive('/dashboard') ? 'bg-primary-100' : 'bg-surface-100'">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                  </svg>
                </div>
                <span>لوحة التحكم</span>
              </Link>

              <template v-if="$page.props.auth.user?.school_profile">
                <Link
                  href="/school-profile/edit"
                  class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-200"
                  :class="isActive('/school-profile/edit') ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
                >
                  <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="isActive('/school-profile/edit') ? 'bg-primary-100' : 'bg-surface-100'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </div>
                  <span>تعديل بيانات المدرسة</span>
                </Link>
              </template>
              <template v-else>
                <Link
                  href="/school-profile/create"
                  class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-200"
                  :class="isActive('/school-profile/create') ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
                >
                  <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="isActive('/school-profile/create') ? 'bg-primary-100' : 'bg-surface-100'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                  </div>
                  <span>إضافة بيانات المدرسة</span>
                </Link>
              </template>

              <div class="border-t border-surface-200 my-3"></div>
              <p class="px-3 py-1 text-xs font-bold text-slate-400 uppercase tracking-wider">التوظيف</p>

              <Link
                href="/school/teachers"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-200"
                :class="isActive('/school/teachers') ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
              >
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="isActive('/school/teachers') ? 'bg-primary-100' : 'bg-surface-100'">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                </div>
                <span>تصفح المدرسين</span>
              </Link>

              <Link
                href="/school/invitations"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-200"
                :class="isActive('/school/invitations') ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
              >
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="isActive('/school/invitations') ? 'bg-primary-100' : 'bg-surface-100'">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                </div>
                <span>الدعوات المرسلة</span>
              </Link>

              <Link
                href="/school/employees"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-200"
                :class="isActive('/school/employees') ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
              >
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="isActive('/school/employees') ? 'bg-primary-100' : 'bg-surface-100'">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                </div>
                <span>المدرسون المعينون</span>
              </Link>
            </nav>
          </template>

          <!-- Teacher Navigation -->
          <template v-else-if="$page.props.auth.user?.role === 'teacher'">
            <div class="mb-6">
              <div class="flex items-center gap-3 px-3 py-3 mb-2">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white font-bold text-sm">
                  👨‍🏫
                </div>
                <div>
                  <p class="text-sm font-bold text-slate-900">لوحة المدرس</p>
                  <p class="text-xs text-slate-500">مرحباً، {{ $page.props.auth.user?.name }}</p>
                </div>
              </div>
            </div>

            <nav class="space-y-1">
              <Link
                href="/dashboard"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-200"
                :class="isActive('/dashboard') ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
              >
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="isActive('/dashboard') ? 'bg-primary-100' : 'bg-surface-100'">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                  </svg>
                </div>
                <span>لوحة التحكم</span>
              </Link>

              <template v-if="$page.props.auth.user?.teacher_profile">
                <Link
                  href="/teacher-profile/edit"
                  class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-200"
                  :class="isActive('/teacher-profile/edit') ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
                >
                  <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="isActive('/teacher-profile/edit') ? 'bg-primary-100' : 'bg-surface-100'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </div>
                  <span>الملف الشخصي</span>
                </Link>
              </template>
              <template v-else>
                <Link
                  href="/teacher-profile/create"
                  class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-200"
                  :class="isActive('/teacher-profile/create') ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
                >
                  <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="isActive('/teacher-profile/create') ? 'bg-primary-100' : 'bg-surface-100'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                  </div>
                  <span>إضافة بيانات المدرس</span>
                </Link>
              </template>

              <div class="border-t border-surface-200 my-3"></div>
              <p class="px-3 py-1 text-xs font-bold text-slate-400 uppercase tracking-wider">التوظيف</p>

              <Link
                href="/teacher/invitations"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-200"
                :class="isActive('/teacher/invitations') ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
              >
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="isActive('/teacher/invitations') ? 'bg-primary-100' : 'bg-surface-100'">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                </div>
                <span>دعوات التوظيف</span>
              </Link>

              <Link
                href="/teacher/my-school"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-200"
                :class="isActive('/teacher/my-school') ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
              >
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="isActive('/teacher/my-school') ? 'bg-primary-100' : 'bg-surface-100'">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
                </div>
                <span>جهة عملي</span>
              </Link>
            </nav>
          </template>
        </div>
      </aside>

      <!-- Main Content -->
      <main class="flex-1 min-w-0 animate-fade-in">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'
import NotificationBell from '@/Components/NotificationBell.vue'
import { useNotifications } from '@/composables/useNotifications'

const { unreadCount, startPolling, stopPolling } = useNotifications()

onMounted(() => startPolling())
onUnmounted(() => stopPolling())

const showMenu = ref(false)
const sidebarOpen = ref(false)
const openSubmenu = ref('teachers')

const page = usePage()

const teachersActive = computed(() =>
  ['Admin/TeachersIndex', 'Admin/TeacherDetails'].includes(page.component)
)

const isActive = (href) => {
  const path = window.location.pathname
  if (href === '/dashboard') return path === '/dashboard'
  return path.startsWith(href)
}

const toggleSubmenu = (name) => {
  openSubmenu.value = openSubmenu.value === name ? null : name
}

const form = useForm({})
const logout = () => form.post('/logout')
</script>
