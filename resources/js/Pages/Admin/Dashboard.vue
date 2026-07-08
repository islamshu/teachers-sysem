<template>
  <DashboardLayout>
    <div>
      <!-- Welcome Banner -->
      <div class="relative mb-8 overflow-hidden rounded-3xl bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 shadow-2xl shadow-primary-500/20 animate-fade-in-up">
        <div class="absolute inset-0 bg-grid opacity-20"></div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-primary-400/20 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
        <div class="relative px-8 py-10">
          <div class="flex items-center gap-4 mb-3">
            <div class="w-14 h-14 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center text-3xl shadow-lg">
              ⚡
            </div>
            <div>
              <h1 class="text-3xl font-extrabold text-white">لوحة تحكم الإدارة</h1>
              <p class="text-primary-200">مرحباً {{ $page.props.auth.user?.name }}، إليك ملخص المنصة</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="card p-6 animate-fade-in-up animate-delay-100">
          <div class="flex items-center justify-between mb-4">
            <p class="text-sm font-semibold text-slate-500">المدرسون قيد المراجعة</p>
            <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
          <div class="text-4xl font-extrabold text-slate-900">{{ stats.pending }}</div>
          <Link href="/admin/teachers" class="text-sm font-medium text-primary-600 hover:text-primary-700 mt-2 inline-block transition-colors">
            عرض الطلبات ←
          </Link>
        </div>

        <div class="card p-6 animate-fade-in-up animate-delay-200">
          <div class="flex items-center justify-between mb-4">
            <p class="text-sm font-semibold text-slate-500">المدرسون المقبولون</p>
            <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
          <div class="text-4xl font-extrabold text-slate-900">{{ stats.approved }}</div>
          <Link href="/" class="text-sm font-medium text-primary-600 hover:text-primary-700 mt-2 inline-block transition-colors">
            عرض المدرسين ←
          </Link>
        </div>

        <div class="card p-6 animate-fade-in-up animate-delay-300">
          <div class="flex items-center justify-between mb-4">
            <p class="text-sm font-semibold text-slate-500">المدرسون المرفوضون</p>
            <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
          <div class="text-4xl font-extrabold text-slate-900">{{ stats.rejected }}</div>
        </div>

        <div class="card p-6 animate-fade-in-up animate-delay-400">
          <div class="flex items-center justify-between mb-4">
            <p class="text-sm font-semibold text-slate-500">إجمالي المدرسين</p>
            <div class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-2a6 6 0 0112 0v2zm0 0h6v-2a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 0118 0z" />
              </svg>
            </div>
          </div>
          <div class="text-4xl font-extrabold text-slate-900">{{ stats.total }}</div>
        </div>
      </div>

      <!-- Schools Stats Row -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="card p-6 animate-fade-in-up animate-delay-100">
          <div class="flex items-center justify-between mb-4">
            <p class="text-sm font-semibold text-slate-500">المدارس قيد المراجعة</p>
            <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
          </div>
          <div class="text-4xl font-extrabold text-slate-900">{{ stats.schools_pending }}</div>
          <Link href="/admin/schools" class="text-sm font-medium text-primary-600 hover:text-primary-700 mt-2 inline-block transition-colors">
            عرض الطلبات ←
          </Link>
        </div>

        <div class="card p-6 animate-fade-in-up animate-delay-200">
          <div class="flex items-center justify-between mb-4">
            <p class="text-sm font-semibold text-slate-500">المدارس المقبولة</p>
            <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
          <div class="text-4xl font-extrabold text-slate-900">{{ stats.schools_approved }}</div>
        </div>

        <div class="card p-6 animate-fade-in-up animate-delay-300">
          <div class="flex items-center justify-between mb-4">
            <p class="text-sm font-semibold text-slate-500">إجمالي المدارس</p>
            <div class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
          </div>
          <div class="text-4xl font-extrabold text-slate-900">{{ stats.schools_total }}</div>
        </div>
      </div>

      <!-- Users Stats Row -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="card p-6 animate-fade-in-up animate-delay-100">
          <div class="flex items-center justify-between mb-4">
            <p class="text-sm font-semibold text-slate-500">إجمالي المستخدمين</p>
            <div class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-2a6 6 0 0112 0v2zm0 0h6v-2a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
            </div>
          </div>
          <div class="text-4xl font-extrabold text-slate-900">{{ stats.users_total }}</div>
          <Link href="/admin/users" class="text-sm font-medium text-primary-600 hover:text-primary-700 mt-2 inline-block transition-colors">
            عرض الكل ←
          </Link>
        </div>

        <div class="card p-6 animate-fade-in-up animate-delay-200">
          <div class="flex items-center justify-between mb-4">
            <p class="text-sm font-semibold text-slate-500">المدرسون</p>
            <div class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </div>
          </div>
          <div class="text-4xl font-extrabold text-slate-900">{{ stats.users_teachers }}</div>
        </div>

        <div class="card p-6 animate-fade-in-up animate-delay-300">
          <div class="flex items-center justify-between mb-4">
            <p class="text-sm font-semibold text-slate-500">المدارس</p>
            <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
          </div>
          <div class="text-4xl font-extrabold text-slate-900">{{ stats.users_schools }}</div>
        </div>

        <div class="card p-6 animate-fade-in-up animate-delay-400">
          <div class="flex items-center justify-between mb-4">
            <p class="text-sm font-semibold text-slate-500">الإداريون</p>
            <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
            </div>
          </div>
          <div class="text-4xl font-extrabold text-slate-900">{{ stats.users_admins }}</div>
        </div>
      </div>

      <!-- Recent Pending Teachers -->
      <div class="card mb-8 animate-fade-in-up animate-delay-500">
        <div class="p-6 border-b border-surface-200 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
            </div>
            <h2 class="text-lg font-bold text-slate-900">آخر الطلبات قيد المراجعة</h2>
          </div>
          <Link href="/admin/teachers" class="btn-ghost px-4 py-2 text-sm">
            عرض الكل
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </Link>
        </div>

        <div v-if="recentPending.length > 0" class="divide-y divide-surface-100">
          <div
            v-for="teacher in recentPending"
            :key="teacher.id"
            class="flex items-center gap-4 p-4 hover:bg-surface-50 transition-colors"
          >
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-100 to-surface-100 overflow-hidden flex-shrink-0">
              <img v-if="teacher.photo" :src="`/storage/${teacher.photo}`" :alt="teacher.user?.name" class="w-full h-full object-cover" />
              <div v-else class="w-full h-full flex items-center justify-center text-sm font-bold text-primary-600">
                {{ teacher.user?.name?.charAt(0) }}
              </div>
            </div>
            <div class="flex-1 min-w-0">
              <p class="font-bold text-slate-900 truncate">{{ teacher.user?.name }}</p>
              <p class="text-sm text-slate-500 truncate">{{ teacher.subject?.name }} · {{ teacher.experience_years }} سنوات خبرة</p>
            </div>
            <span class="badge-warning text-xs whitespace-nowrap">قيد المراجعة</span>
            <Link
              :href="`/admin/teachers/${teacher.id}`"
              class="btn-ghost px-3 py-1.5 text-xs"
            >
              عرض
            </Link>
          </div>
        </div>

        <div v-else class="p-10 text-center">
          <div class="w-16 h-16 rounded-2xl bg-emerald-50 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h3 class="text-lg font-bold text-slate-900 mb-1">لا توجد طلبات قيد المراجعة</h3>
          <p class="text-sm text-slate-500">جميع المدرسين تمت مراجعتهم</p>
        </div>
      </div>

      <!-- Quick Access Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <Link href="/admin/subjects" class="card p-6 group cursor-pointer hover:shadow-lg transition-all duration-300 animate-fade-in-up animate-delay-100">
          <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-primary-100 flex items-center justify-center group-hover:scale-110 transition-transform">
              <svg class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-bold text-slate-900">إدارة المواد الدراسية</h3>
              <p class="text-sm text-slate-500">{{ subjects.length }} مادة</p>
            </div>
          </div>
        </Link>

        <Link href="/admin/grades" class="card p-6 group cursor-pointer hover:shadow-lg transition-all duration-300 animate-fade-in-up animate-delay-200">
          <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-warm-100 flex items-center justify-center group-hover:scale-110 transition-transform">
              <svg class="w-7 h-7 text-warm-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-bold text-slate-900">إدارة الصفوف الدراسية</h3>
              <p class="text-sm text-slate-500">{{ grades.length }} صف</p>
            </div>
          </div>
        </Link>
      </div>

      <!-- All Teachers -->
      <div class="card mb-8 animate-fade-in-up animate-delay-300">
        <div class="p-6 border-b border-surface-200 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </div>
            <h2 class="text-lg font-bold text-slate-900">جميع المدرسين</h2>
          </div>
          <div class="flex items-center gap-3">
            <TableSearch v-model="teacherSearch" />
            <div class="text-sm font-semibold text-slate-500">{{ allTeachersList.length }} مدرس</div>
          </div>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-surface-50 border-b border-surface-200">
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">المدرس</th>
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">المادة</th>
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">الحالة</th>
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">الخبرة</th>
                <th class="px-6 py-4 text-center text-sm font-bold text-slate-700">الإجراءات</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-surface-100">
              <tr
                v-for="teacher in filteredTeachers"
                :key="teacher.id"
                class="hover:bg-primary-50/50 transition-colors duration-150"
              >
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary-100 to-surface-100 overflow-hidden flex-shrink-0">
                      <img v-if="teacher.photo" :src="`/storage/${teacher.photo}`" :alt="teacher.user?.name" class="w-full h-full object-cover" />
                      <div v-else class="w-full h-full flex items-center justify-center text-sm font-bold text-primary-600">
                        {{ teacher.user?.name?.charAt(0) }}
                      </div>
                    </div>
                    <div>
                      <p class="font-bold text-slate-900 text-sm">{{ teacher.user?.name }}</p>
                      <p class="text-xs text-slate-500">{{ teacher.user?.email }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <span class="badge-primary text-xs">{{ teacher.subject?.name }}</span>
                </td>
                <td class="px-6 py-4">
                  <span
                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold"
                    :class="{
                      'bg-emerald-100 text-emerald-700': teacher.status === 'approved',
                      'bg-amber-100 text-amber-700': teacher.status === 'pending',
                      'bg-red-100 text-red-700': teacher.status === 'rejected',
                    }"
                  >
                    <span
                      class="w-1.5 h-1.5 rounded-full"
                      :class="{
                        'bg-emerald-500': teacher.status === 'approved',
                        'bg-amber-500': teacher.status === 'pending',
                        'bg-red-500': teacher.status === 'rejected',
                      }"
                    ></span>
                    {{ teacher.status === 'approved' ? 'مقبول' : teacher.status === 'pending' ? 'قيد المراجعة' : 'مرفوض' }}
                  </span>
                </td>
                <td class="px-6 py-4 text-sm text-slate-900 font-medium">{{ teacher.experience_years }} سنة</td>
                <td class="px-6 py-4 text-center">
                  <Link
                    :href="`/admin/teachers/${teacher.id}`"
                    class="btn-ghost px-3 py-1.5 text-xs"
                  >
                    عرض
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div ref="allTeachersSentinel" class="flex justify-center py-6">
          <div v-if="allTeachersLoading" class="flex items-center gap-3 text-slate-500">
            <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <span class="text-sm font-medium">جاري التحميل...</span>
          </div>
          <p v-else-if="!allTeachersNextPage && allTeachersList.length > 0" class="text-sm text-slate-400">لا يوجد المزيد</p>
        </div>
      </div>

      <!-- Bus Contracts -->
      <div class="card mb-8 animate-fade-in-up animate-delay-300">
        <div class="p-6 border-b border-surface-200 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
              </svg>
            </div>
            <h2 class="text-lg font-bold text-slate-900">عقود الباصات</h2>
          </div>
          <div class="flex items-center gap-3">
            <TableSearch v-model="contractSearch" />
            <Link href="/bus-contracts/create" class="btn-primary text-sm px-4 py-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              إضافة عقد
            </Link>
            <div class="text-sm font-semibold text-slate-500">{{ busContracts.length }} عقد</div>
          </div>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-surface-50 border-b border-surface-200">
                <th class="px-4 py-3 text-right text-sm font-bold text-slate-700">#</th>
                <th class="px-4 py-3 text-right text-sm font-bold text-slate-700">المالك</th>
                <th class="px-4 py-3 text-right text-sm font-bold text-slate-700">هاتف المالك</th>
                <th class="px-4 py-3 text-right text-sm font-bold text-slate-700">السائق</th>
                <th class="px-4 py-3 text-right text-sm font-bold text-slate-700">هاتف السائق</th>
                <th class="px-4 py-3 text-right text-sm font-bold text-slate-700">الرخصة</th>
                <th class="px-4 py-3 text-right text-sm font-bold text-slate-700">اللون</th>
                <th class="px-4 py-3 text-right text-sm font-bold text-slate-700">الركاب</th>
                <th class="px-4 py-3 text-right text-sm font-bold text-slate-700">الموقع</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-surface-100">
              <tr
                v-for="(contract, index) in filteredContracts"
                :key="contract.id"
                class="hover:bg-primary-50/50 transition-colors duration-150"
              >
                <td class="px-4 py-3 text-sm text-slate-500">{{ index + 1 }}</td>
                <td class="px-4 py-3 text-sm font-bold text-slate-900">{{ contract.owner_name }}</td>
                <td class="px-4 py-3 text-sm text-slate-700" dir="ltr">{{ contract.owner_phone || '-' }}</td>
                <td class="px-4 py-3 text-sm text-slate-700">{{ contract.same_driver ? 'نفس المالك' : contract.driver_name || '-' }}</td>
                <td class="px-4 py-3 text-sm text-slate-700" dir="ltr">{{ contract.same_driver ? '-' : contract.driver_phone || '-' }}</td>
                <td class="px-4 py-3 text-sm text-slate-700">{{ contract.license }}</td>
                <td class="px-4 py-3 text-sm text-slate-700">
                  <div class="flex items-center gap-2">
                    <span class="inline-block w-4 h-4 rounded-full border border-surface-200" :style="{ backgroundColor: contract.van_color }"></span>
                    <span>{{ contract.van_color }}</span>
                  </div>
                </td>
                <td class="px-4 py-3 text-sm font-bold text-slate-900 text-center">{{ contract.seats_count }}</td>
                <td class="px-4 py-3 text-sm text-slate-700">{{ contract.location }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-if="filteredContracts.length === 0" class="p-10 text-center">
          <div class="w-16 h-16 rounded-2xl bg-surface-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
            </svg>
          </div>
          <h3 class="text-lg font-bold text-slate-900 mb-1">لا توجد عقود باصات</h3>
          <p class="text-sm text-slate-500">لم يتم إضافة أي عقود باصات بعد</p>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import TableSearch from '@/Components/TableSearch.vue'

const props = defineProps({
  stats: Object,
  recentPending: Array,
  subjects: Array,
  grades: Array,
  allTeachers: Array,
  allTeachersNextPage: Number,
  busContracts: Array,
})

const allTeachersList = ref([...props.allTeachers])
const allTeachersNextPage = ref(props.allTeachersNextPage)
const allTeachersLoading = ref(false)
const allTeachersSentinel = ref(null)
let allTeachersObserver = null
const teacherSearch = ref('')
const contractSearch = ref('')

const matchesSearch = (item, term) => {
  if (!term) return true
  const t = term.toLowerCase()
  return Object.values(item).some(val => {
    if (val == null) return false
    if (typeof val === 'string') return val.toLowerCase().includes(t)
    if (typeof val === 'number') return String(val).includes(t)
    if (typeof val === 'object') {
      if (Array.isArray(val)) return val.some(v => typeof v === 'object' ? matchesSearch(v, term) : String(v).toLowerCase().includes(t))
      return matchesSearch(val, term)
    }
    return false
  })
}

const filteredTeachers = computed(() => allTeachersList.value.filter(item => matchesSearch(item, teacherSearch.value)))
const filteredContracts = computed(() => props.busContracts.filter(item => matchesSearch(item, contractSearch.value)))

const loadMoreTeachers = async () => {
  if (allTeachersLoading.value || !allTeachersNextPage.value) return
  allTeachersLoading.value = true
  try {
    const res = await fetch(`/admin/dashboard?page=${allTeachersNextPage.value}`, {
      headers: { Accept: 'application/json' },
    })
    const json = await res.json()
    allTeachersList.value.push(...json.data)
    allTeachersNextPage.value = json.current_page < json.last_page ? json.current_page + 1 : null
  } catch (e) {
    console.error(e)
  } finally {
    allTeachersLoading.value = false
  }
}

onMounted(() => {
  allTeachersObserver = new IntersectionObserver(
    (entries) => { if (entries[0].isIntersecting) loadMoreTeachers() },
    { threshold: 0.1 }
  )
  if (allTeachersSentinel.value) allTeachersObserver.observe(allTeachersSentinel.value)
})

onUnmounted(() => {
  if (allTeachersObserver) allTeachersObserver.disconnect()
})
</script>
