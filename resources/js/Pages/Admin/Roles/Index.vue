<template>
  <DashboardLayout>
    <div>
      <div class="relative mb-8 overflow-hidden rounded-3xl bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 shadow-2xl shadow-primary-500/20 animate-fade-in-up">
        <div class="absolute inset-0 bg-grid opacity-20"></div>
        <div class="relative px-8 py-10">
          <div class="flex items-center gap-4 mb-3">
            <div class="w-14 h-14 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center text-3xl shadow-lg">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
            </div>
            <div>
              <h1 class="text-3xl font-extrabold text-white">إدارة الأدوار</h1>
              <p class="text-primary-200">إدارة أدوار المستخدمين والصلاحيات المرتبطة بها</p>
            </div>
          </div>
        </div>
      </div>

      <div class="card p-6 animate-fade-in-up animate-delay-100">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-lg font-extrabold text-slate-900">جميع الأدوار</h2>
          <div class="flex items-center gap-3">
            <TableSearch v-model="search" />
            <button @click="showCreateModal = true" class="btn-primary text-sm px-4 py-2 rounded-xl">
            <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            دور جديد
          </button>
            </div>
          </div>

          <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-surface-50 border-b border-surface-200">
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">الدور</th>
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">الصلاحيات</th>
                <th class="px-6 py-4 text-center text-sm font-bold text-slate-700">عدد المستخدمين</th>
                <th class="px-6 py-4 text-center text-sm font-bold text-slate-700">الإجراءات</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-surface-100">
              <tr
                v-for="role in filteredList"
                :key="role.id"
                class="hover:bg-primary-50/50 transition-colors duration-150"
              >
                <td class="px-6 py-4">
                  <span class="inline-flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full" :class="roleDotClass(role.name)"></span>
                    <span class="font-bold text-slate-900">{{ roleLabel(role.name) }}</span>
                  </span>
                </td>
                <td class="px-6 py-4">
                  <div class="flex flex-wrap gap-1.5">
                    <span
                      v-for="perm in role.permissions"
                      :key="perm.id"
                      class="inline-flex px-2.5 py-0.5 rounded-lg bg-primary-100 text-primary-700 text-xs font-semibold"
                    >
                      {{ permissionLabel(perm.name) }}
                    </span>
                    <span v-if="role.permissions.length === 0" class="text-sm text-slate-400">لا توجد صلاحيات</span>
                  </div>
                </td>
                <td class="px-6 py-4 text-center">
                  <span class="font-bold text-slate-700">{{ role.users_count }}</span>
                </td>
                <td class="px-6 py-4 text-center">
                  <div class="flex items-center justify-center gap-2">
                    <button
                      @click="editRole(role)"
                      class="p-2 rounded-lg bg-amber-100 text-amber-700 hover:bg-amber-200 transition-all duration-200"
                      title="تعديل"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                    </button>
                    <button
                      @click="deleteRole(role)"
                      :disabled="isProtected(role.name)"
                      class="p-2 rounded-lg transition-all duration-200"
                      :class="isProtected(role.name) ? 'bg-surface-100 text-slate-300 cursor-not-allowed' : 'bg-red-100 text-red-700 hover:bg-red-200'"
                      :title="isProtected(role.name) ? 'لا يمكن حذف الأدوار الأساسية' : 'حذف'"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="card p-6 mt-6 animate-fade-in-up animate-delay-200">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-lg font-extrabold text-slate-900">الصلاحيات المتاحة</h2>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
          <div
            v-for="(perms, group) in groupedPermissions"
            :key="group"
            class="card p-4 bg-surface-50/50"
          >
            <h3 class="text-sm font-bold text-primary-700 mb-3">{{ groupLabel(group) }}</h3>
            <div class="space-y-2">
              <span
                v-for="perm in perms"
                :key="perm.id"
                class="block px-3 py-1.5 rounded-lg bg-white text-xs font-semibold text-slate-700 border border-surface-200"
              >
                {{ permissionLabel(perm.name) }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Role Modal -->
    <div v-if="showCreateModal || showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" @click.self="closeModals">
      <div class="bg-white rounded-3xl w-full max-w-lg mx-4 p-8 shadow-2xl animate-fade-in-up">
        <h3 class="text-xl font-extrabold text-slate-900 mb-6">
          {{ showEditModal ? 'تعديل الدور' : 'دور جديد' }}
        </h3>

        <form @submit.prevent="submitRole">
          <div class="mb-5">
            <label class="block text-sm font-bold text-slate-700 mb-1.5">اسم الدور <span class="text-red-500">*</span></label>
            <input v-model="roleForm.name" type="text" class="input-base" placeholder="مشرف" required />
            <p v-if="roleForm.errors.name" class="mt-1 text-xs text-red-500 font-semibold">{{ roleForm.errors.name }}</p>
          </div>

          <div class="mb-6">
            <label class="block text-sm font-bold text-slate-700 mb-3">الصلاحيات</label>
            <div class="space-y-4 max-h-64 overflow-y-auto">
              <div v-for="(perms, group) in groupedPermissions" :key="group">
                <h4 class="text-xs font-bold text-slate-500 mb-2">{{ groupLabel(group) }}</h4>
                <div class="space-y-2 mr-3">
                  <label
                    v-for="perm in perms"
                    :key="perm.id"
                    class="flex items-center gap-3 cursor-pointer group"
                  >
                    <input
                      type="checkbox"
                      :value="perm.name"
                      v-model="roleForm.permissions"
                      class="w-4 h-4 rounded border-surface-300 text-primary-600 focus:ring-primary-500"
                    />
                    <span class="text-sm text-slate-700 group-hover:text-slate-900">{{ permissionLabel(perm.name) }}</span>
                  </label>
                </div>
              </div>
            </div>
          </div>

          <div class="flex items-center gap-3">
            <button type="submit" :disabled="roleForm.processing" class="btn-primary flex-1">
              {{ roleForm.processing ? 'جاري الحفظ...' : (showEditModal ? 'تحديث' : 'إنشاء') }}
            </button>
            <button type="button" @click="closeModals" class="btn-ghost flex-1">إلغاء</button>
          </div>
        </form>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import TableSearch from '@/Components/TableSearch.vue'
import Swal from 'sweetalert2'

const props = defineProps({
  roles: Array,
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

const filteredList = computed(() => props.roles.filter(item => matchesSearch(item, search.value)))

const showCreateModal = ref(false)
const showEditModal = ref(false)
const editingRole = ref(null)

const roleForm = useForm({
  name: '',
  permissions: [],
})

const groupedPermissions = computed(() => {
  const allPerms = []
  props.roles.forEach(role => {
    role.permissions.forEach(p => {
      if (!allPerms.some(x => x.id === p.id)) {
        allPerms.push({ id: p.id, name: p.name })
      }
    })
  })

  const groups = {}
  allPerms.forEach(perm => {
    const parts = perm.name.split(' ')
    const group = parts[0] || 'other'
    if (!groups[group]) groups[group] = []
    groups[group].push(perm)
  })
  return groups
})

const groupLabel = (group) => {
  const labels = {
    عرض: 'عرض',
    إدارة: 'إدارة',
    الرد: 'استجابة',
  }
  return labels[group] || group
}

const permissionLabel = (name) => {
  return name
}

const roleLabel = (name) => {
  const labels = {
    admin: 'إداري',
    school: 'مدرسة',
    teacher: 'مدرس',
  }
  return labels[name] || name
}

const roleDotClass = (name) => {
  const map = {
    admin: 'bg-amber-500',
    school: 'bg-emerald-500',
    teacher: 'bg-primary-500',
  }
  return map[name] || 'bg-slate-400'
}

const isProtected = (name) => ['admin', 'teacher'].includes(name)

const editRole = (role) => {
  if (isProtected(role.name)) {
    Swal.fire({
      icon: 'info',
      title: 'دور أساسي',
      text: 'يمكن تعديل صلاحيات الأدوار الأساسية فقط',
      confirmButtonText: 'حسناً',
    })
    return
  }
  editingRole.value = role
  roleForm.name = role.name
  roleForm.permissions = role.permissions.map(p => p.name)
  roleForm.errors = {}
  showEditModal.value = true
}

const submitRole = () => {
  if (showEditModal.value && editingRole.value) {
    roleForm.put(`/admin/roles/${editingRole.value.id}`, {
      onSuccess: () => {
        closeModals()
        Swal.fire({ icon: 'success', title: 'تم التحديث', text: 'تم تحديث الدور بنجاح', confirmButtonText: 'حسناً' })
      },
    })
  } else {
    roleForm.post('/admin/roles', {
      onSuccess: () => {
        closeModals()
        Swal.fire({ icon: 'success', title: 'تم الإنشاء', text: 'تم إنشاء الدور بنجاح', confirmButtonText: 'حسناً' })
      },
    })
  }
}

const deleteRole = (role) => {
  if (isProtected(role.name)) return
  Swal.fire({
    icon: 'warning',
    title: 'حذف الدور',
    text: `سيتم حذف الدور "${roleLabel(role.name)}"`,
    showCancelButton: true,
    confirmButtonText: 'نعم، احذف',
    cancelButtonText: 'إلغاء',
    confirmButtonColor: '#ef4444',
  }).then((result) => {
    if (result.isConfirmed) {
      useForm({}).delete(`/admin/roles/${role.id}`, {
        onSuccess: () => { location.reload() },
      })
    }
  })
}

const closeModals = () => {
  showCreateModal.value = false
  showEditModal.value = false
  editingRole.value = null
  roleForm.reset()
  roleForm.clearErrors()
}
</script>
