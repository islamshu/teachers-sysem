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
        class="w-64 flex-shrink-0 lg:sticky lg:top-24 lg:h-[calc(100vh-8rem)]"
        :class="sidebarOpen ? 'fixed inset-0 z-40 lg:relative lg:inset-auto' : 'hidden lg:block'"
      >
        <!-- Mobile backdrop -->
        <div v-if="sidebarOpen" class="fixed inset-0 bg-black/30 backdrop-blur-sm lg:hidden" @click="sidebarOpen = false"></div>

        <div
          class="relative bg-white rounded-2xl shadow-sm border border-surface-200 p-4 h-full overflow-y-auto"
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
          <template v-if="$page.props.auth.user?.is_admin || $page.props.auth.permissions?.length > 0">
            <div class="mb-6">
              <div class="flex items-center gap-3 px-3 py-3 mb-2">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white font-bold text-sm">
                  ⚡
                </div>
                <div>
                  <p class="text-sm font-bold text-slate-900">{{ $page.props.auth.user?.is_admin ? 'لوحة الإدارة' : 'لوحة التحكم' }}</p>
                  <p class="text-xs text-slate-500">مرحباً، {{ $page.props.auth.user?.name }}</p>
                </div>
              </div>
            </div>

            <nav class="space-y-1">
              <Link
                v-if="$page.props.auth.permissions?.includes('عرض لوحة التحكم')"
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

              <template v-if="$page.props.auth.permissions?.includes('إدارة المدرسين')">
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
                    href="/admin/teachers?status=pending"
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
              </template>

              <Link
                v-if="$page.props.auth.permissions?.includes('إدارة المدارس')"
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
                v-if="$page.props.auth.permissions?.includes('إدارة المستخدمين')"
                href="/admin/users"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-200"
                :class="isActive('/admin/users') ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
              >
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="isActive('/admin/users') ? 'bg-primary-100' : 'bg-surface-100'">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-2a6 6 0 0112 0v2zm0 0h6v-2a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                  </svg>
                </div>
                <span>المستخدمون</span>
              </Link>

              <Link
                v-if="$page.props.auth.permissions?.includes('إدارة المواد')"
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
                v-if="$page.props.auth.permissions?.includes('إدارة الصفوف')"
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
                v-if="$page.props.auth.permissions?.includes('إدارة عقود الباص')"
                href="/admin/bus-contracts"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-200"
                :class="isActive('/admin/bus-contracts') ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
              >
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="isActive('/admin/bus-contracts') ? 'bg-primary-100' : 'bg-surface-100'">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                  </svg>
                </div>
                <span>عقود الباصات</span>
              </Link>

              <Link
                v-if="$page.props.auth.permissions?.includes('إدارة مرفقات العقود')"
                href="/admin/contract-attachments"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-200"
                :class="isActive('/admin/contract-attachments') ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
              >
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="isActive('/admin/contract-attachments') ? 'bg-primary-100' : 'bg-surface-100'">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                  </svg>
                </div>
                <span>مرفقات العقود</span>
              </Link>

              <Link
                v-if="$page.props.auth.user?.is_admin"
                href="/admin/frontend"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-200"
                :class="isActive('/admin/frontend') ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
              >
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="isActive('/admin/frontend') ? 'bg-primary-100' : 'bg-surface-100'">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                </div>
                <span>إعدادات الصفحة الرئيسية</span>
              </Link>

              <Link
                v-if="$page.props.auth.permissions?.includes('إدارة الإعدادات')"
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

              <div class="border-t border-surface-200 my-3"></div>

              <Link
                v-if="$page.props.auth.permissions?.includes('إدارة الأدوار')"
                href="/admin/roles"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-200"
                :class="isActive('/admin/roles') ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
              >
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="isActive('/admin/roles') ? 'bg-primary-100' : 'bg-surface-100'">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                  </svg>
                </div>
                <span>الأدوار والصلاحيات</span>
              </Link>

              <Link
                v-if="$page.props.auth.permissions?.includes('إدارة الفروع')"
                href="/admin/branches"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-200"
                :class="isActive('/admin/branches') ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
              >
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="isActive('/admin/branches') ? 'bg-primary-100' : 'bg-surface-100'">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                  </svg>
                </div>
                <span>الفروع</span>
              </Link>

              <Link
                v-if="$page.props.auth.permissions?.includes('إدارة الموظفين')"
                href="/admin/employees"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-200"
                :class="isActive('/admin/employees') ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
              >
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="isActive('/admin/employees') ? 'bg-primary-100' : 'bg-surface-100'">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                </div>
                <span>الموظفون</span>
              </Link>

              <Link
                v-if="$page.props.auth.permissions?.includes('إدارة أسئلة المقابلات')"
                href="/admin/interview-questions"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-200"
                :class="isActive('/admin/interview-questions') ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
              >
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="isActive('/admin/interview-questions') ? 'bg-primary-100' : 'bg-surface-100'">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                  </svg>
                </div>
                <span>أسئلة المقابلات</span>
              </Link>
            </nav>
          </template>

          <!-- School Navigation -->
          <template v-if="$page.props.auth.user?.role === 'school' || $page.props.auth.user?.roles?.some(r => r.name === 'school')">
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
                v-if="$page.props.auth.permissions?.includes('عرض لوحة التحكم')"
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

              <template v-if="$page.props.auth.permissions?.includes('إدارة الملف الشخصي')">
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
              </template>

              <template v-if="$page.props.auth.user?.school_profile?.status === 'approved'">
              <template v-if="$page.props.auth.permissions?.includes('إجراء المقابلات')">
                <div class="border-t border-surface-200 my-3"></div>
                <p class="px-3 py-1 text-xs font-bold text-slate-400 uppercase tracking-wider">التوظيف</p>

                <Link
                  href="/school/hiring/applicants"
                  class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-200"
                  :class="isActive('/school/hiring/applicants') ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
                >
                  <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="isActive('/school/hiring/applicants') ? 'bg-primary-100' : 'bg-surface-100'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                  </div>
                  <span>طالبي التوظيف</span>
                </Link>

                <Link
                  href="/school/hiring/invitations"
                  class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-200"
                  :class="isActive('/school/hiring/invitations') ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
                >
                  <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="isActive('/school/hiring/invitations') ? 'bg-primary-100' : 'bg-surface-100'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                  </div>
                  <span>الدعوات المرسلة</span>
                </Link>

                <Link
                  href="/school/hiring/hired"
                  class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-200"
                  :class="isActive('/school/hiring/hired') ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
                >
                  <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="isActive('/school/hiring/hired') ? 'bg-primary-100' : 'bg-surface-100'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                  </div>
                  <span>الموظفين المعينين</span>
                </Link>
              </template>
              </template>

            </nav>
          </template>

          <!-- Teacher Navigation -->
          <template v-if="$page.props.auth.user?.role === 'teacher' || $page.props.auth.user?.roles?.some(r => r.name === 'teacher')">
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
                v-if="$page.props.auth.permissions?.includes('عرض لوحة التحكم')"
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

              <template v-if="$page.props.auth.permissions?.includes('إدارة الملف الشخصي')">
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
              </template>

              <template v-if="$page.props.auth.permissions?.includes('الرد على الدعوات')">
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
              </template>

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

          <!-- Employee Navigation -->
          <template v-if="$page.props.auth.user?.role != 'teacher' && $page.props.auth.user?.role != 'school' && $page.props.auth.user?.role != 'admin' && !$page.props.auth.user?.is_admin">
            <div class="mb-6">
              <div class="flex items-center gap-3 px-3 py-3 mb-2">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white font-bold text-sm">
                  👷
                </div>
                <div>
                  <p class="text-sm font-bold text-slate-900">لوحة الموظف</p>
                  <p class="text-xs text-slate-500">مرحباً، {{ $page.props.auth.user?.name }}</p>
                </div>
              </div>
            </div>

            <nav class="space-y-1">
              <Link
                v-if="$page.props.auth.permissions?.includes('عرض لوحة التحكم')"
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

              <div class="border-t border-surface-200 my-3"></div>
              <p class="px-3 py-1 text-xs font-bold text-slate-400 uppercase tracking-wider">التوظيف</p>

              <Link
                href="/employee/invitations"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-200"
                :class="isActive('/employee/invitations') ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
              >
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="isActive('/employee/invitations') ? 'bg-primary-100' : 'bg-surface-100'">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                </div>
                <span>دعوات التوظيف</span>
              </Link>
            </nav>
          </template>

          <!-- Universal Navigation for all users -->
          <nav class="space-y-1 mt-3 border-t border-surface-200 pt-3">
            <div v-if="$page.props.auth.user?.role != 'school' && $page.props.auth.permissions?.includes('إجراء المقابلات')">
              <button
                @click="toggleSubmenu('hiring')"
                class="flex items-center justify-between w-full px-4 py-3 rounded-xl font-medium transition-all duration-200"
                :class="hiringActive ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
              >
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="hiringActive ? 'bg-primary-100' : 'bg-surface-100'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                  </div>
                  <span>التوظيف</span>
                </div>
                <svg class="w-3 h-3 transition-transform duration-200" :class="{ 'rotate-180': openSubmenu === 'hiring' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>
              <div v-if="openSubmenu === 'hiring'" class="mr-4 mt-1 space-y-1 border-r-2 border-primary-200 pr-3">
                <Link
                  href="/school/hiring/applicants"
                  class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200"
                  :class="isActive('/school/hiring/applicants') ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
                >
                  <span class="w-2 h-2 rounded-full bg-primary-500"></span>
                  طالبي التوظيف
                </Link>
                <Link
                  href="/school/hiring/invitations"
                  class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200"
                  :class="isActive('/school/hiring/invitations') ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
                >
                  <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                  الدعوات المرسلة
                </Link>
                <Link
                  href="/school/hiring/hired"
                  class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200"
                  :class="isActive('/school/hiring/hired') ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
                >
                  <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                  الموظفين المعينين
                </Link>
              </div>
            </div>
            <Link
              v-if="$page.props.auth.permissions?.includes('الرد على الدعوات')"
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
            <div>
              <button
                @click="toggleSubmenu('tasks')"
                class="flex items-center justify-between w-full px-4 py-3 rounded-xl font-medium transition-all duration-200"
                :class="tasksActive ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
              >
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="tasksActive ? 'bg-primary-100' : 'bg-surface-100'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                  </div>
                  <span>المهام</span>
                </div>
                <span v-if="$page.props.badges?.incompleteTasks > 0" class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full bg-primary-500 text-white text-[11px] font-bold leading-none">
                  {{ $page.props.badges.incompleteTasks }}
                </span>
                <svg class="w-3 h-3 transition-transform duration-200" :class="{ 'rotate-180': openSubmenu === 'tasks' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>
              <div v-if="openSubmenu === 'tasks'" class="mr-4 mt-1 space-y-1 border-r-2 border-primary-200 pr-3">
                <Link
                  href="/tasks"
                  class="flex items-center justify-between px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200"
                  :class="isActive('/tasks') ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
                >
                  <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-primary-500"></span>
                    مهامي اليومية
                  </div>
                  <span v-if="$page.props.badges?.incompleteFixedTasks > 0" class="inline-flex items-center justify-center min-w-[18px] h-4 px-1 rounded-full bg-primary-500 text-white text-[10px] font-bold leading-none">
                    {{ $page.props.badges.incompleteFixedTasks }}
                  </span>
                </Link>
                <Link
                  href="/general-tasks"
                  class="flex items-center justify-between px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200"
                  :class="isActive('/general-tasks') ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
                >
                  <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    المهام العامة
                  </div>
                  <span v-if="$page.props.badges?.incompleteGeneralTasks > 0" class="inline-flex items-center justify-center min-w-[18px] h-4 px-1 rounded-full bg-primary-500 text-white text-[10px] font-bold leading-none">
                    {{ $page.props.badges.incompleteGeneralTasks }}
                  </span>
                </Link>
                <div class="border-t border-surface-100 my-2"></div>
                <Link
                  v-if="$page.props.auth.permissions?.includes('إدارة المهام اليومية')"
                  href="/admin/fixed-tasks"
                  class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200"
                  :class="isActive('/admin/fixed-tasks') && !isActive('/admin/fixed-tasks/progress') ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
                >
                  <span class="w-2 h-2 rounded-full bg-primary-500"></span>
                  إدارة المهام اليومية
                </Link>
                <Link
                  v-if="$page.props.auth.permissions?.includes('إدارة المهام العامة')"
                  href="/admin/general-tasks"
                  class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200"
                  :class="isActive('/admin/general-tasks') ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
                >
                  <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                  إدارة المهام العامة
                </Link>
                <Link
                  v-if="$page.props.auth.permissions?.includes('عرض تقارير المهام')"
                  href="/admin/fixed-tasks/progress"
                  class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200"
                  :class="isActive('/admin/fixed-tasks/progress') ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
                >
                  <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                  تقرير التقدم
                </Link>
              </div>
            </div>

            <Link
              v-if="$page.props.hasBalance"
              href="/purchases"
              class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-200"
              :class="isActive('/purchases') ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
            >
              <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="isActive('/purchases') ? 'bg-primary-100' : 'bg-surface-100'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
              </div>
              <span>طلبات المشتريات</span>
              <span v-if="$page.props.badges?.incompletePurchases > 0" class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full bg-primary-500 text-white text-[11px] font-bold leading-none">
                {{ $page.props.badges.incompletePurchases }}
              </span>
            </Link>

              <Link
                v-if="$page.props.auth.permissions?.includes('إدارة المشتريات')"
                href="/admin/purchases"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-200"
                :class="isActive('/admin/purchases') ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
              >
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="isActive('/admin/purchases') ? 'bg-primary-100' : 'bg-surface-100'">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                </div>
                <span>إدارة المشتريات</span>
              </Link>

              <Link
                v-if="$page.props.auth.permissions?.includes('إدارة الحسابات المالية')"
                href="/admin/balances"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-200"
                :class="isActive('/admin/balances') ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-slate-600 hover:bg-surface-100 hover:text-slate-900'"
              >
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="isActive('/admin/balances') ? 'bg-primary-100' : 'bg-surface-100'">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <span>الحسابات المالية</span>
              </Link>
            </nav>
        </div>
      </aside>

      <!-- Main Content -->
      <main class="flex-1 min-w-0 animate-fade-in">
        <ValidationErrors />
        <slot />
      </main>
    </div>

    <!-- Floating Telegram Widget -->
    <div
      v-if="!$page.props.auth.user?.telegram_chat_id"
      class="fixed z-[9999] transition-all duration-500 ease-out"
      :class="widgetVisible ? 'left-0 opacity-100 translate-x-0' : '-left-80 opacity-0 -translate-x-full'"
      :style="widgetStyle"
    >
      <div class="w-72 bg-white rounded-2xl shadow-2xl shadow-blue-500/10 border border-slate-200 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-5 py-3 flex items-center gap-2">
          <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.74-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z"/>
          </svg>
          <span class="text-white font-bold text-sm">Telegram</span>
          <button @click="dismissWidget" class="mr-auto text-white/70 hover:text-white transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        <div class="p-5">
          <p class="font-bold text-slate-800 text-sm mb-1">اربط حسابك الآن</p>
          <p class="text-xs text-slate-500 mb-4 leading-relaxed">احصل على الإشعارات المهمة مباشرة على Telegram</p>
          <button
            @click="openTelegramLink"
            :disabled="widgetConnecting"
            class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white font-bold text-sm py-2.5 rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 disabled:opacity-50"
          >
            <span v-if="widgetConnecting" class="flex items-center justify-center gap-2">
              <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              جاري الربط...
            </span>
            <span v-else>ربط الآن</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'
import NotificationBell from '@/Components/NotificationBell.vue'
import ValidationErrors from '@/Components/ValidationErrors.vue'
import { useNotifications } from '@/composables/useNotifications'
import { useFlashAlert } from '@/composables/useFlashAlert'
import { applyPrimaryColor } from '@/utils/colors'

const { unreadCount, startListening, stopListening } = useNotifications()

const page = usePage()

useFlashAlert()

onMounted(() => {
  startListening()
  const color = page.props.settings?.primary_color
  if (color) {
    applyPrimaryColor(color)
  }
})
onUnmounted(() => stopListening())

const showMenu = ref(false)
const sidebarOpen = ref(false)
const openSubmenu = ref('teachers')

const teachersActive = computed(() =>
  ['Admin/TeachersIndex', 'Admin/TeacherDetails'].includes(page.component)
)

const tasksActive = computed(() =>
  ['Admin/FixedTasks/Index', 'Admin/GeneralTasks/Index', 'Admin/FixedTasks/Progress', 'Tasks/Index', 'GeneralTasks/Index'].includes(page.component)
)

const hiringActive = computed(() =>
  ['School/TeachersIndex', 'School/Invitations', 'School/ConductInterview', 'School/InterviewResult', 'School/Employees', 'School/EmployeesBrowse', 'School/EmployeeInvitations', 'School/EmployeeConductInterview', 'School/EmployeeInterviewResult', 'School/EmployeeHired'].includes(page.component)
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

const widgetVisible = ref(false)
const widgetConnecting = ref(false)
const widgetDismissed = ref(false)

const widgetStyle = computed(() => {
  return 'top: 50%; transform: translateY(-50%);'
})

onMounted(() => {
  setTimeout(() => {
    if (!widgetDismissed.value) {
      widgetVisible.value = true
    }
  }, 1000)
})

const dismissWidget = () => {
  widgetVisible.value = false
  widgetDismissed.value = true
}

const openTelegramLink = async () => {
  widgetConnecting.value = true
  try {
    const response = await fetch('/telegram/connect')
    const data = await response.json()
    if (data.link) {
      window.open(data.link, '_blank')
    }
  } catch (error) {
    console.error(error)
  } finally {
    widgetConnecting.value = false
  }
}
</script>

<style scoped>
@media (max-width: 768px) {
  .fixed.z-\[9999\] {
    top: auto !important;
    bottom: 20px !important;
    left: 20px !important;
    right: 20px !important;
    transform: none !important;
  }

  .fixed.z-\[9999\] .w-72 {
    width: 100% !important;
  }

  .fixed.z-\[9999\].-left-80 {
    left: 20px !important;
    bottom: -100px !important;
    transform: none !important;
  }

  .fixed.z-\[9999\].left-0 {
    left: 20px !important;
    bottom: 20px !important;
    transform: none !important;
  }
}
</style>
