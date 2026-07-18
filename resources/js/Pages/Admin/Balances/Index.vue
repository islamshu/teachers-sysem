<template>
  <DashboardLayout>
    <div>
      <div class="relative mb-8 overflow-hidden rounded-3xl bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 shadow-2xl shadow-primary-500/20 animate-fade-in-up">
        <div class="absolute inset-0 bg-grid opacity-20"></div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-primary-400/20 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
        <div class="relative px-8 py-10">
          <div class="flex items-center gap-4 mb-3">
            <div class="w-14 h-14 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center text-3xl shadow-lg">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div>
              <h1 class="text-2xl font-bold text-white">الحسابات المالية</h1>
              <p class="text-white/70 text-sm">إدارة أرصدة المستخدمين</p>
            </div>
          </div>
        </div>
      </div>

      <div class="card animate-fade-in-up animate-delay-100">
        <div class="p-6 border-b border-surface-200 flex items-center justify-between">
          <div class="flex items-center gap-2">
            <span class="text-sm font-semibold text-slate-500">{{ balances.length }} حساب</span>
          </div>
          <TableSearch v-model="search" />
          <Link href="/admin/balances/create" class="btn-primary text-sm px-4 py-2 rounded-xl">
            <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            إضافة رصيد
          </Link>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-surface-50 border-b border-surface-200">
                <th class="px-4 py-3 text-right text-sm font-bold text-slate-700">#</th>
                <th class="px-4 py-3 text-right text-sm font-bold text-slate-700">المستخدم</th>
                <th class="px-4 py-3 text-right text-sm font-bold text-slate-700">البريد الإلكتروني</th>
                <th class="px-4 py-3 text-right text-sm font-bold text-slate-700">الدور</th>
                <th class="px-4 py-3 text-right text-sm font-bold text-slate-700">الرصيد الحالي</th>
                <th class="px-4 py-3 text-right text-sm font-bold text-slate-700">آخر إضافة</th>
                <th class="px-4 py-3 text-center text-sm font-bold text-slate-700">الإجراءات</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-surface-100">
              <tr
                v-for="(item, index) in filteredList"
                :key="item.id"
                class="hover:bg-primary-50/50 transition-colors duration-150"
              >
                <td class="px-4 py-3 text-sm text-slate-500">{{ index + 1 }}</td>
                <td class="px-4 py-3">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-bold text-sm">
                      {{ item.user?.name?.charAt(0) }}
                    </div>
                    <span class="text-sm font-bold text-slate-900">{{ item.user?.name }}</span>
                  </div>
                </td>
                <td class="px-4 py-3 text-sm text-slate-500">{{ item.user?.email }}</td>
                <td class="px-4 py-3">
                  <span class="inline-flex px-2.5 py-0.5 rounded-lg text-xs font-bold bg-surface-100 text-slate-600">
                    {{ roleLabel(item.user?.role) }}
                  </span>
                </td>
                <td class="px-4 py-3">
                  <span class="text-sm font-bold" :class="item.balance > 0 ? 'text-emerald-600' : 'text-slate-400'">
                    {{ Number(item.balance).toLocaleString('ar') }} ريال
                  </span>
                </td>
                <td class="px-4 py-3 text-sm text-slate-500">{{ formatDate(item.updated_at) }}</td>
                <td class="px-4 py-3 text-center">
                  <div class="flex items-center justify-center gap-1">
                    <Link :href="`/admin/balances/${item.user_id}`" class="p-2 rounded-lg text-slate-400 hover:text-primary-600 hover:bg-primary-50 transition-all duration-200 inline-block" title="عرض الحساب">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                    </Link>
                    <Link :href="`/admin/balances/create?user_id=${item.user_id}`" class="p-2 rounded-lg text-slate-400 hover:text-primary-600 hover:bg-primary-50 transition-all duration-200 inline-block" title="إضافة رصيد">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                      </svg>
                    </Link>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="filteredList.length === 0" class="p-10 text-center">
          <div class="w-16 h-16 rounded-2xl bg-surface-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h3 class="text-lg font-bold text-slate-900 mb-1">لا توجد أرصدة بعد</h3>
          <p class="text-sm text-slate-500">قم بإضافة رصيد لأحد المستخدمين</p>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import TableSearch from '@/Components/TableSearch.vue'

const props = defineProps({
  balances: Array,
})

const search = ref('')

const roleLabel = (role) => {
  const labels = {
    admin: 'مدير',
    teacher: 'مدرس',
    school: 'مدرسة',
    employee: 'موظف',
  }
  return labels[role] || role
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('ar', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const matchesSearch = (item, term) => {
  if (!term) return true
  const t = term.toLowerCase()
  const userName = item.user?.name?.toLowerCase() || ''
  const userEmail = item.user?.email?.toLowerCase() || ''
  const balance = String(item.balance)
  return userName.includes(t) || userEmail.includes(t) || balance.includes(t)
}

const filteredList = computed(() => props.balances.filter(item => matchesSearch(item, search.value)))
</script>
