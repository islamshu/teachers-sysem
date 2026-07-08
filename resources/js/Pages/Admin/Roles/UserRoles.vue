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
              <h1 class="text-3xl font-extrabold text-white">أدوار المستخدم</h1>
              <p class="text-primary-200">تعديل أدوار المستخدم: {{ user.name }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="card p-6 animate-fade-in-up animate-delay-100">
        <div class="flex items-center gap-4 mb-6 p-4 bg-surface-50 rounded-2xl">
          <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary-100 to-surface-100 flex items-center justify-center text-lg font-bold text-primary-600">
            {{ user.name?.charAt(0) }}
          </div>
          <div>
            <h2 class="font-extrabold text-slate-900">{{ user.name }}</h2>
            <p class="text-sm text-slate-500">{{ user.email }}</p>
          </div>
        </div>

        <form @submit.prevent="submitRoles">
          <div class="mb-6">
            <label class="block text-sm font-bold text-slate-700 mb-4">الأدوار المتاحة</label>
            <div class="space-y-3">
              <label
                v-for="role in roles"
                :key="role.id"
                class="flex items-center gap-4 p-4 rounded-2xl border-2 transition-all duration-200 cursor-pointer"
                :class="selectedRoles.includes(role.name) ? 'border-primary-500 bg-primary-50' : 'border-surface-200 hover:border-primary-200'"
              >
                <input
                  type="checkbox"
                  :value="role.name"
                  v-model="selectedRoles"
                  class="w-5 h-5 rounded border-surface-300 text-primary-600 focus:ring-primary-500"
                />
                <div>
                  <span class="font-bold text-slate-900">{{ roleLabel(role.name) }}</span>
                  <span class="text-xs text-slate-500 mr-2">({{ role.users_count || 0 }} مستخدم)</span>
                </div>
              </label>
            </div>
          </div>

          <div class="flex items-center gap-3">
            <button type="submit" :disabled="form.processing" class="btn-primary flex-1">
              {{ form.processing ? 'جاري الحفظ...' : 'حفظ التغييرات' }}
            </button>
            <Link href="/admin/users" class="btn-ghost flex-1 text-center">إلغاء</Link>
          </div>
        </form>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import Swal from 'sweetalert2'

const props = defineProps({
  user: Object,
  roles: Array,
  userRoles: Array,
})

const selectedRoles = ref([...props.userRoles])
const form = useForm({ roles: selectedRoles })

const roleLabel = (name) => {
  const labels = {
    admin: 'إداري',
    school: 'مدرسة',
    teacher: 'مدرس',
  }
  return labels[name] || name
}

const submitRoles = () => {
  form.roles = selectedRoles.value
  form.put(`/admin/users/${props.user.id}/roles`, {
    onSuccess: () => {
      Swal.fire({ icon: 'success', title: 'تم الحفظ', text: 'تم تحديث أدوار المستخدم بنجاح', confirmButtonText: 'حسناً' })
    },
  })
}
</script>
