<template>
  <DashboardLayout>
    <div>
      <div class="relative mb-8 overflow-hidden rounded-3xl bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 shadow-2xl shadow-primary-500/20 animate-fade-in-up">
        <div class="absolute inset-0 bg-grid opacity-20"></div>
        <div class="relative px-8 py-10">
          <div class="flex items-center gap-4 mb-3">
            <div class="w-14 h-14 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center text-3xl shadow-lg">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-2a6 6 0 0112 0v2zm0 0h6v-2a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
            </div>
            <div>
              <h1 class="text-3xl font-extrabold text-white">إدارة المستخدمين</h1>
              <p class="text-primary-200">عرض وإدارة جميع مستخدمي المنصة</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6 animate-fade-in-up animate-delay-100">
        <div class="card p-4 text-center">
          <p class="text-2xl font-extrabold text-slate-900">{{ stats.total }}</p>
          <p class="text-xs font-semibold text-slate-500 mt-1">الإجمالي</p>
        </div>
        <div class="card p-4 text-center">
          <p class="text-2xl font-extrabold text-primary-600">{{ stats.teachers }}</p>
          <p class="text-xs font-semibold text-slate-500 mt-1">مدرسون</p>
        </div>
        <div class="card p-4 text-center">
          <p class="text-2xl font-extrabold text-emerald-600">{{ stats.schools }}</p>
          <p class="text-xs font-semibold text-slate-500 mt-1">مدارس</p>
        </div>
        <div class="card p-4 text-center">
          <p class="text-2xl font-extrabold text-amber-600">{{ stats.admins }}</p>
          <p class="text-xs font-semibold text-slate-500 mt-1">إداريون</p>
        </div>
      </div>

      <!-- Role Tabs -->
      <div class="flex flex-wrap gap-2 mb-6 animate-fade-in-up animate-delay-100">
        <Link
          href="/admin/users"
          class="px-5 py-2.5 rounded-xl font-bold text-sm transition-all duration-200"
          :class="!currentRole ? 'bg-primary-500 text-white shadow-md shadow-primary-500/20' : 'bg-white text-slate-600 hover:bg-surface-100 border border-surface-200'"
        >
          الكل ({{ stats.total }})
        </Link>
        <Link
          href="/admin/users?role=teacher"
          class="px-5 py-2.5 rounded-xl font-bold text-sm transition-all duration-200"
          :class="currentRole === 'teacher' ? 'bg-primary-500 text-white shadow-md shadow-primary-500/20' : 'bg-white text-slate-600 hover:bg-surface-100 border border-surface-200'"
        >
          مدرسون ({{ stats.teachers }})
        </Link>
        <Link
          href="/admin/users?role=school"
          class="px-5 py-2.5 rounded-xl font-bold text-sm transition-all duration-200"
          :class="currentRole === 'school' ? 'bg-emerald-500 text-white shadow-md shadow-emerald-500/20' : 'bg-white text-slate-600 hover:bg-surface-100 border border-surface-200'"
        >
          مدارس ({{ stats.schools }})
        </Link>
        <Link
          href="/admin/users?role=admin"
          class="px-5 py-2.5 rounded-xl font-bold text-sm transition-all duration-200"
          :class="currentRole === 'admin' ? 'bg-amber-500 text-white shadow-md shadow-amber-500/20' : 'bg-white text-slate-600 hover:bg-surface-100 border border-surface-200'"
        >
          إداريون ({{ stats.admins }})
        </Link>
      </div>

      <!-- Users Table -->
      <div class="card animate-fade-in-up animate-delay-200">
        <div v-if="usersList.length > 0" class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-surface-50 border-b border-surface-200">
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">المستخدم</th>
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">البريد الإلكتروني</th>
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">النوع</th>
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">تاريخ التسجيل</th>
                <th class="px-6 py-4 text-center text-sm font-bold text-slate-700">الإجراءات</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-surface-100">
              <tr
                v-for="user in usersList"
                :key="user.id"
                class="hover:bg-primary-50/50 transition-colors duration-150"
              >
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary-100 to-surface-100 flex items-center justify-center text-sm font-bold text-primary-600 flex-shrink-0">
                      {{ user.name?.charAt(0) }}
                    </div>
                    <div>
                      <p class="font-bold text-slate-900 text-sm">{{ user.name }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 text-sm text-slate-600">{{ user.email }}</td>
                <td class="px-6 py-4">
                  <span
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold"
                    :class="roleBadgeClass(user.role)"
                  >
                    <span class="w-1.5 h-1.5 rounded-full" :class="roleDotClass(user.role)"></span>
                    {{ roleLabel(user.role) }}
                  </span>
                </td>
                <td class="px-6 py-4 text-sm text-slate-600">{{ formatDate(user.created_at) }}</td>
                <td class="px-6 py-4 text-center">
                  <button
                    @click="deleteUser(user)"
                    class="p-2 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 transition-all duration-200"
                    title="حذف"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-else class="p-10 text-center">
          <div class="w-16 h-16 rounded-2xl bg-surface-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </div>
          <h3 class="text-lg font-bold text-slate-900 mb-1">لا يوجد مستخدمون</h3>
          <p class="text-sm text-slate-500">لم يتم العثور على مستخدمين في هذا القسم</p>
        </div>

        <div ref="sentinel" class="flex justify-center py-6">
          <div v-if="loading" class="flex items-center gap-3 text-slate-500">
            <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <span class="text-sm font-medium">جاري التحميل...</span>
          </div>
          <p v-else-if="!nextPage && usersList.length > 0" class="text-sm text-slate-400">لا يوجد المزيد</p>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import Swal from 'sweetalert2'

const props = defineProps({
  users: Array,
  nextPage: Number,
  currentRole: String,
  stats: Object,
})

const usersList = ref([...props.users])
const nextPage = ref(props.nextPage)
const loading = ref(false)
const sentinel = ref(null)
let observer = null

const roleBadgeClass = (role) => {
  const map = {
    teacher: 'bg-primary-100 text-primary-700',
    school: 'bg-emerald-100 text-emerald-700',
    admin: 'bg-amber-100 text-amber-700',
  }
  return map[role] || 'bg-surface-100 text-slate-600'
}

const roleDotClass = (role) => {
  const map = {
    teacher: 'bg-primary-500',
    school: 'bg-emerald-500',
    admin: 'bg-amber-500',
  }
  return map[role] || 'bg-slate-400'
}

const roleLabel = (role) => {
  const map = {
    teacher: 'مدرس',
    school: 'مدرسة',
    admin: 'إداري',
  }
  return map[role] || role
}

const formatDate = (date) => {
  if (!date) return '-'
  const d = new Date(date)
  return d.toLocaleDateString('ar-SA', { year: 'numeric', month: 'short', day: 'numeric' })
}

const loadMore = async () => {
  if (loading.value || !nextPage.value) return
  loading.value = true

  try {
    const params = new URLSearchParams()
    if (props.currentRole) params.append('role', props.currentRole)
    params.append('page', nextPage.value)

    const res = await fetch(`/admin/users?${params.toString()}`, {
      headers: { Accept: 'application/json' },
    })
    const json = await res.json()
    usersList.value.push(...json.data)
    nextPage.value = json.current_page < json.last_page ? json.current_page + 1 : null
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const deleteUser = (user) => {
  Swal.fire({
    icon: 'warning',
    title: 'حذف المستخدم',
    text: `سيتم حذف المستخدم "${user.name}" وجميع بياناته المرتبطة نهائياً. لا يمكن التراجع عن هذا الإجراء.`,
    showCancelButton: true,
    confirmButtonText: 'نعم، احذف',
    cancelButtonText: 'إلغاء',
    confirmButtonColor: '#ef4444',
  }).then((result) => {
    if (result.isConfirmed) {
      useForm({}).delete(`/admin/users/${user.id}`, {
        onSuccess: () => location.reload(),
      })
    }
  })
}

onMounted(() => {
  observer = new IntersectionObserver(
    (entries) => { if (entries[0].isIntersecting) loadMore() },
    { threshold: 0.1 }
  )
  if (sentinel.value) observer.observe(sentinel.value)
})

onUnmounted(() => {
  if (observer) observer.disconnect()
})
</script>
