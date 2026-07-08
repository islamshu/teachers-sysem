<template>
  <DashboardLayout>
    <div>
      <div class="flex items-center justify-between mb-8 animate-fade-in-up">
        <div>
          <h1 class="text-3xl font-extrabold text-slate-900">تقرير تقدم المهام</h1>
          <p class="text-slate-500 mt-1">متابعة إنجاز المهام اليومية حسب الدور والتاريخ</p>
        </div>
        <Link href="/admin/fixed-tasks" class="btn-ghost">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          العودة للمهام
        </Link>
      </div>

      <Alert v-if="flash.success" variant="success" :title="flash.success" closeable class="mb-6 animate-fade-in-down" />

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 animate-fade-in-up animate-delay-50">
        <div class="card p-5 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-primary-100 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
          </div>
          <div>
            <p class="text-sm text-slate-500">إجمالي المهام</p>
            <p class="text-2xl font-bold text-slate-900">{{ summary.total_tasks }}</p>
          </div>
        </div>
        <div class="card p-5 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
            </svg>
          </div>
          <div>
            <p class="text-sm text-slate-500">المستخدمين المستهدفين</p>
            <p class="text-2xl font-bold text-slate-900">{{ summary.total_users }}</p>
          </div>
        </div>
        <div class="card p-5 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div>
            <p class="text-sm text-slate-500">إجمالي الإنجازات</p>
            <p class="text-2xl font-bold text-slate-900">{{ summary.total_completions }}</p>
          </div>
        </div>
      </div>

      <div class="card p-4 mb-6 animate-fade-in-up animate-delay-100">
        <div class="flex items-center gap-4">
          <label class="text-sm font-semibold text-slate-700">اختر التاريخ:</label>
          <input
            type="date"
            :value="date"
            @change="changeDate"
            class="input-base w-auto"
          />
        </div>
      </div>

      <div class="mb-6">
        <TableSearch v-model="search" placeholder="بحث في المهام..." />
      </div>
      <div v-for="roleData in filteredRolesData" :key="roleData.role.id" class="mb-8 animate-fade-in-up animate-delay-150">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-bold text-slate-900">
            دور: {{ roleLabel(roleData.role.name) }}
            <span class="text-sm font-normal text-slate-500 mr-2">({{ roleData.users.length }} مستخدم)</span>
          </h2>
          <div class="flex gap-2">
            <span
              v-for="user in roleData.users"
              :key="user.id"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-medium"
              :class="allCompleted(roleData.tasks, user.id) ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500'"
            >
              <span>{{ user.name }}</span>
              <span>{{ completedCount(roleData.tasks, user.id) }}/{{ roleData.tasks.length }}</span>
            </span>
          </div>
        </div>

        <div class="card overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="bg-surface-50 border-b border-surface-200">
                  <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">المهمة</th>
                  <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">الوصف</th>
                  <th class="px-6 py-4 text-center text-sm font-bold text-slate-700">الإنجاز</th>
                  <th class="px-6 py-4 text-center text-sm font-bold text-slate-700">المستخدمون المنجزون</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-surface-100">
                <tr v-for="task in roleData.tasks" :key="task.id" class="hover:bg-primary-50/50 transition-colors">
                  <td class="px-6 py-4 font-medium text-slate-900">{{ task.name }}</td>
                  <td class="px-6 py-4 text-sm text-slate-500 max-w-xs truncate">{{ task.description || '-' }}</td>
                  <td class="px-6 py-4 text-center">
                    <div class="flex items-center gap-2 justify-center">
                      <div class="w-full max-w-[120px] bg-slate-200 rounded-full h-2">
                        <div
                          class="h-2 rounded-full transition-all duration-500"
                          :class="completionRate(task) === 100 ? 'bg-emerald-500' : 'bg-primary-500'"
                          :style="{ width: completionRate(task) + '%' }"
                        ></div>
                      </div>
                      <span class="text-xs font-bold text-slate-600 min-w-[3rem] text-left">{{ task.completed_count }}/{{ task.total_users }}</span>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="flex flex-wrap gap-1.5 justify-center" v-if="task.completed_users.length">
                      <span
                        v-for="cu in task.completed_users"
                        :key="cu.id"
                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-emerald-100 text-emerald-700 text-xs font-medium"
                      >
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ cu.name }}
                      </span>
                    </div>
                    <p v-else class="text-center text-xs text-slate-400">لا يوجد</p>
                  </td>
                </tr>
                <tr v-if="roleData.tasks.length === 0 && !search">
                  <td colspan="4" class="px-6 py-12 text-center text-slate-500">لا توجد مهام لهذا الدور</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div v-if="rolesData.length === 0" class="card p-12 text-center animate-fade-in-up">
        <p class="text-slate-500">لا توجد مهام فعالة لأي دور</p>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePage, Link, router } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import Alert from '@/Components/Alert.vue'
import TableSearch from '@/Components/TableSearch.vue'

const props = defineProps({
  rolesData: Array,
  summary: Object,
  date: String,
})

const search = ref('')

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

const filteredRolesData = computed(() => {
  if (!search.value) return props.rolesData
  return props.rolesData.map(rd => ({
    ...rd,
    tasks: rd.tasks.filter(task => matchesSearch(task, search.value)),
  }))
})

const page = usePage()
const flash = page.props.flash || {}

const roleLabel = (name) => {
  const labels = { admin: 'إداري', school: 'مدرسة', teacher: 'مدرس', employee: 'موظف' }
  return labels[name] || name
}

const changeDate = (e) => {
  router.get('/admin/fixed-tasks/progress', { date: e.target.value }, { preserveState: true })
}

const completionRate = (task) => {
  if (task.total_users === 0) return 0
  return Math.round((task.completed_count / task.total_users) * 100)
}

const completedCount = (tasks, userId) => {
  return tasks.filter(t => t.completed_users.some(cu => cu.id === userId)).length
}

const allCompleted = (tasks, userId) => {
  return completedCount(tasks, userId) === tasks.length
}
</script>