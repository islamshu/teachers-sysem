<template>
  <DashboardLayout>
    <div>
      <div class="mb-8 animate-fade-in-up">
        <div class="flex items-center justify-between">
          <div>
            <Link href="/admin/general-tasks" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-700 mb-2 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
              العودة للمهام العامة
            </Link>
            <h1 class="text-3xl font-extrabold text-slate-900">{{ task.name }}</h1>
            <p class="text-slate-500 mt-1">{{ task.description || 'بدون وصف' }}</p>
          </div>
        </div>

        <div class="flex items-center gap-6 mt-4 p-4 bg-surface-50 rounded-xl flex-wrap">
          <span class="text-sm text-slate-500">
            البداية: <strong class="text-slate-700" dir="ltr">{{ formatDate(task.start_at) }}</strong>
          </span>
          <span class="text-sm text-slate-500">
            النهاية: <strong class="text-slate-700" dir="ltr">{{ formatDate(task.end_at) }}</strong>
          </span>
          <span class="text-sm text-slate-500">
            الحالة:
            <span
              class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold mr-1"
              :class="task.is_active ? (isOverdue ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700') : 'bg-slate-100 text-slate-500'"
            >
              {{ task.is_active ? (isOverdue ? 'منتهية' : 'نشط') : 'غير نشط' }}
            </span>
          </span>
          <span class="text-sm text-slate-500 mr-auto">
            إجمالي المستخدمين: <strong class="text-slate-700">{{ users.length }}</strong>
          </span>
        </div>

        <div class="flex items-center gap-2 mt-4 flex-wrap">
          <TableSearch v-model="search" placeholder="بحث بالاسم أو البريد..." />
          <span class="text-xs font-bold text-slate-500 ml-2">تصفية:</span>
          <button
            @click="setFilter(null)"
            class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all duration-200"
            :class="!filter ? 'bg-slate-800 text-white shadow-sm' : 'bg-surface-100 text-slate-600 hover:bg-surface-200'"
          >
            الكل
          </button>
          <button
            @click="setFilter('not_done')"
            class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all duration-200"
            :class="filter === 'not_done' ? 'bg-red-600 text-white shadow-sm' : 'bg-red-100 text-red-700 hover:bg-red-200'"
          >
            لم يتم
          </button>
          <button
            @click="setFilter('done_before_finish')"
            class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all duration-200"
            :class="filter === 'done_before_finish' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200'"
          >
            تم قبل النهاية
          </button>
          <button
            @click="setFilter('done_after_finish')"
            class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all duration-200"
            :class="filter === 'done_after_finish' ? 'bg-amber-600 text-white shadow-sm' : 'bg-amber-100 text-amber-700 hover:bg-amber-200'"
          >
            تم بعد النهاية
          </button>
        </div>
      </div>

      <div class="card overflow-hidden animate-fade-in-up animate-delay-100">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-surface-50 border-b border-surface-200">
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">
                  <button @click="toggleSort('name')" class="flex items-center gap-1 hover:text-primary-600 transition-colors">
                    المستخدم
                    <svg v-if="sort === 'name'" class="w-3 h-3" :class="{ 'rotate-180': order === 'desc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                    </svg>
                  </button>
                </th>
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">البريد الإلكتروني</th>
                <th class="px-6 py-4 text-center text-sm font-bold text-slate-700">
                  <button @click="toggleSort('status')" class="flex items-center gap-1 hover:text-primary-600 transition-colors">
                    الحالة
                    <svg v-if="sort === 'status'" class="w-3 h-3" :class="{ 'rotate-180': order === 'desc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                    </svg>
                  </button>
                </th>
                <th class="px-6 py-4 text-center text-sm font-bold text-slate-700">وقت الإنجاز</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-surface-100">
              <tr
                v-for="user in filteredUsers"
                :key="user.id"
                class="transition-colors"
                :class="statusRowClass(user.status)"
              >
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <div
                      class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-bold shrink-0"
                      :class="statusIconClass(user.status)"
                    >
                      {{ user.name.charAt(0) }}
                    </div>
                    <span class="font-medium text-slate-900">{{ user.name }}</span>
                  </div>
                </td>
                <td class="px-6 py-4 text-sm text-slate-600">{{ user.email }}</td>
                <td class="px-6 py-4 text-center">
                  <span
                    class="inline-flex px-3 py-1 rounded-full text-xs font-bold"
                    :class="statusBadgeClass(user.status)"
                  >
                    {{ statusLabel(user.status) }}
                  </span>
                </td>
                <td class="px-6 py-4 text-center">
                  <span v-if="user.completed_at" class="text-xs text-slate-500" dir="ltr">
                    {{ formatDateTime(user.completed_at) }}
                  </span>
                  <span v-else class="text-xs text-slate-400">—</span>
                </td>
              </tr>
              <tr v-if="filteredUsers.length === 0">
                <td colspan="4" class="px-6 py-12 text-center text-slate-500">لا يوجد مستخدمون مسندون لهذه المهمة</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="mt-6">
        <Link href="/admin/general-tasks" class="btn-ghost">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          العودة للمهام العامة
        </Link>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import TableSearch from '@/Components/TableSearch.vue'

const props = defineProps({
  task: Object,
  users: Array,
  sort: String,
  order: String,
  filter: String,
})

const search = ref('')

const filteredUsers = computed(() => {
  if (!search.value) return props.users
  const t = search.value.toLowerCase()
  return props.users.filter(u => (u.name && u.name.toLowerCase().includes(t)) || (u.email && u.email.toLowerCase().includes(t)))
})

const isOverdue = computed(() => {
  return new Date(props.task.end_at) < new Date()
})

const formatDate = (dt) => {
  if (!dt) return '-'
  const d = new Date(dt)
  return d.toLocaleDateString('ar-SA', { day: 'numeric', month: 'short', year: 'numeric' }) + ' ' +
    d.toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' })
}

const formatDateTime = (dt) => {
  if (!dt) return ''
  const d = new Date(dt)
  return d.toLocaleDateString('ar-SA', { day: 'numeric', month: 'short' }) + ' ' +
    d.toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' })
}

const statusLabel = (status) => {
  const labels = {
    not_done: 'لم يتم',
    done_before_finish: 'تم قبل النهاية',
    done_after_finish: 'تم بعد النهاية',
  }
  return labels[status] || status
}

const statusBadgeClass = (status) => {
  if (status === 'done_before_finish') return 'bg-emerald-100 text-emerald-700'
  if (status === 'done_after_finish') return 'bg-amber-100 text-amber-700'
  return 'bg-red-100 text-red-700'
}

const statusRowClass = (status) => {
  if (status === 'done_before_finish') return 'hover:bg-emerald-50/50'
  if (status === 'done_after_finish') return 'hover:bg-amber-50/50'
  return 'hover:bg-red-50/50'
}

const statusIconClass = (status) => {
  if (status === 'done_before_finish') return 'bg-emerald-100 text-emerald-600'
  if (status === 'done_after_finish') return 'bg-amber-100 text-amber-600'
  return 'bg-red-100 text-red-600'
}

const toggleSort = (column) => {
  const newOrder = props.sort === column && props.order === 'asc' ? 'desc' : 'asc'
  const params = { sort: column, order: newOrder }
  if (props.filter) params.filter = props.filter
  router.get(`/admin/general-tasks/${props.task.id}`, params, { preserveState: true })
}

const setFilter = (status) => {
  const params = { filter: status, sort: props.sort, order: props.order }
  router.get(`/admin/general-tasks/${props.task.id}`, params, { preserveState: true })
}
</script>