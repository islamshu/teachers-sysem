<template>
  <DashboardLayout>
    <div class="max-w-4xl mx-auto">
      <!-- Back Button -->
      <div class="mb-6">
        <button @click="goBack" class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-700 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
          العودة
        </button>
      </div>

      <!-- Profile Header -->
      <div class="card p-6 mb-6 animate-fade-in-up">
        <div class="flex items-start gap-5">
          <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-primary-100 to-surface-100 overflow-hidden flex-shrink-0">
            <div class="w-full h-full flex items-center justify-center text-3xl font-bold text-primary-600">
              {{ employee.name?.charAt(0) }}
            </div>
          </div>
          <div class="flex-1 min-w-0">
            <h1 class="text-2xl font-extrabold text-slate-900 mb-1">{{ employee.name }}</h1>
            <p class="text-sm text-slate-500 mb-2">{{ employee.email }}</p>
            <div v-if="employee.job_title" class="mb-2">
              <span class="inline-block px-3 py-1 text-xs rounded-lg bg-warm-100 text-warm-700 font-bold">
                {{ employee.job_title }}
              </span>
            </div>
            <div class="flex flex-wrap gap-2">
              <span v-for="role in employee.roles" :key="role.id" class="inline-block px-3 py-1 text-xs rounded-lg bg-primary-100 text-primary-700 font-bold">
                {{ role.name }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Registration Status -->
      <div class="card p-6 mb-6 animate-fade-in-up">
        <h2 class="text-lg font-bold text-slate-900 mb-4">حالة التسجيل</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="p-4 rounded-xl bg-surface-50 border border-surface-200">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl flex items-center justify-center" :class="hasTeacherProfile ? 'bg-emerald-100' : 'bg-surface-200'">
                <svg v-if="hasTeacherProfile" class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <svg v-else class="w-5 h-5 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div>
                <p class="text-sm font-bold text-slate-900">مسجل كمدرس</p>
                <p class="text-xs" :class="hasTeacherProfile ? 'text-emerald-600' : 'text-slate-500'">
                  {{ hasTeacherProfile ? 'نعم - لديه ملف تعريف مدرس' : 'لا' }}
                </p>
              </div>
            </div>
          </div>
          <div class="p-4 rounded-xl bg-surface-50 border border-surface-200">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl flex items-center justify-center" :class="hasEmployeeRole ? 'bg-emerald-100' : 'bg-surface-200'">
                <svg v-if="hasEmployeeRole" class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <svg v-else class="w-5 h-5 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div>
                <p class="text-sm font-bold text-slate-900">مسجل كموظف</p>
                <p class="text-xs" :class="hasEmployeeRole ? 'text-emerald-600' : 'text-slate-500'">
                  {{ hasEmployeeRole ? 'نعم - لديه دور موظف' : 'لا' }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Teacher Profile Details -->
      <div v-if="hasTeacherProfile" class="card p-6 mb-6 animate-fade-in-up">
        <h2 class="text-lg font-bold text-slate-900 mb-4">بيانات الملف المدرسية</h2>

        <div v-if="employee.teacher_profile" class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div class="p-3 rounded-xl bg-warm-50 text-center">
              <p class="text-xs text-warm-600 font-semibold">الخبرة</p>
              <p class="font-bold text-slate-900">{{ employee.teacher_profile.experience_years }} سنة</p>
            </div>
            <div class="p-3 rounded-xl bg-primary-50 text-center">
              <p class="text-xs text-primary-600 font-semibold">المؤهل</p>
              <p class="font-bold text-primary-700">{{ employee.teacher_profile.qualification || '-' }}</p>
            </div>
          </div>

          <div class="space-y-3 text-sm">
            <div v-if="employee.teacher_profile.phone" class="flex items-center gap-3 text-slate-700">
              <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
              </svg>
              <span>{{ employee.teacher_profile.phone }}</span>
            </div>
            <div v-if="employee.teacher_profile.birth_date" class="flex items-center gap-3 text-slate-700">
              <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <span>{{ employee.teacher_profile.birth_date }}</span>
            </div>
            <div v-if="employee.teacher_profile.gender" class="flex items-center gap-3 text-slate-700">
              <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              <span>{{ employee.teacher_profile.gender === 'male' ? 'ذكر' : 'أنثى' }}</span>
            </div>
            <div v-if="employee.teacher_profile.residence_place" class="flex items-center gap-3 text-slate-700">
              <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <span>{{ employee.teacher_profile.residence_place }}</span>
            </div>
            <div v-if="employee.teacher_profile.current_location" class="flex items-center gap-3 text-slate-700">
              <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <span>{{ employee.teacher_profile.current_location }}</span>
            </div>
          </div>

          <div v-if="employee.teacher_profile.subject" class="mt-4">
            <p class="text-xs font-bold text-slate-500 mb-2">المادة</p>
            <span class="badge-primary">{{ employee.teacher_profile.subject.name }}</span>
          </div>

          <div v-if="employee.teacher_profile.grades?.length" class="mt-4">
            <p class="text-xs font-bold text-slate-500 mb-2">الصفوف</p>
            <div class="flex flex-wrap gap-1.5">
              <span v-for="grade in employee.teacher_profile.grades" :key="grade.id" class="inline-block px-2.5 py-1 text-xs rounded-lg bg-surface-100 text-surface-600 font-medium">
                {{ grade.name }}
              </span>
            </div>
          </div>

          <div v-if="employee.teacher_profile.practical_experience" class="mt-4">
            <p class="text-xs font-bold text-slate-500 mb-2">الخبرة العملية</p>
            <p class="text-sm text-slate-700 leading-relaxed">{{ employee.teacher_profile.practical_experience }}</p>
          </div>

          <div v-if="employee.teacher_profile.bio" class="mt-4">
            <p class="text-xs font-bold text-slate-500 mb-2">النبذة التعريفية</p>
            <p class="text-sm text-slate-700 leading-relaxed">{{ employee.teacher_profile.bio }}</p>
          </div>

          <div class="mt-4">
            <p class="text-xs font-bold text-slate-500 mb-2">حالة التوظيف</p>
            <span :class="{
              'px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700': employee.teacher_profile.employment_status === 'available',
              'px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700': employee.teacher_profile.employment_status === 'employed',
              'px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700': employee.teacher_profile.employment_status === 'unavailable'
            }">
              {{ employee.teacher_profile.employment_status === 'available' ? 'متاح' : employee.teacher_profile.employment_status === 'employed' ? 'متوظف' : 'غير متاح' }}
            </span>
          </div>
        </div>
      </div>

      <!-- Employee Roles & Branches -->
      <div class="card p-6 mb-6 animate-fade-in-up">
        <h2 class="text-lg font-bold text-slate-900 mb-4">الأدوار والفروع</h2>

        <div v-if="employee.roles?.length" class="mb-4">
          <p class="text-xs font-bold text-slate-500 mb-2">الأدوار</p>
          <div class="flex flex-wrap gap-2">
            <span v-for="role in employee.roles" :key="role.id" class="inline-block px-3 py-1.5 text-sm rounded-lg bg-primary-100 text-primary-700 font-bold">
              {{ role.name }}
            </span>
          </div>
        </div>

        <div v-if="employee.branches?.length">
          <p class="text-xs font-bold text-slate-500 mb-2">الفروع</p>
          <div class="flex flex-wrap gap-2">
            <span v-for="branch in employee.branches" :key="branch.id" class="inline-block px-3 py-1.5 text-sm rounded-lg bg-surface-100 text-surface-600 font-bold">
              {{ branch.name }}
            </span>
          </div>
        </div>
      </div>

      <!-- Employment Status -->
      <div v-if="employmentStatus || employeeInvitationStatus" class="card p-6 mb-6 animate-fade-in-up">
        <h2 class="text-lg font-bold text-slate-900 mb-4">حالة التوظيف</h2>

        <div v-if="employmentStatus" class="p-4 rounded-xl bg-surface-50 border border-surface-200 mb-3">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-bold text-slate-900">ملف تعريف مدرس</p>
              <p class="text-xs text-slate-500 mt-0.5">عند {{ employmentStatus.school?.school_profile?.school_name || 'مدرسة' }}</p>
            </div>
            <span :class="{
              'px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700': employmentStatus.status === 'hired',
              'px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700': employmentStatus.status === 'invited',
              'px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700': employmentStatus.status === 'accepted',
              'px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-700': employmentStatus.status === 'interviewed',
              'px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700': employmentStatus.status === 'rejected',
              'px-3 py-1 rounded-full text-xs font-bold bg-surface-100 text-surface-600': employmentStatus.status === 'ended'
            }">
              {{ statusLabels[employmentStatus.status] || employmentStatus.status }}
            </span>
          </div>
        </div>

        <div v-if="employeeInvitationStatus" class="p-4 rounded-xl bg-surface-50 border border-surface-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-bold text-slate-900">دعوة موظف</p>
              <p class="text-xs text-slate-500 mt-0.5">عند {{ employeeInvitationStatus.school?.school_profile?.school_name || 'مدرسة' }}</p>
            </div>
            <span :class="{
              'px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700': employeeInvitationStatus.status === 'hired',
              'px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700': employeeInvitationStatus.status === 'invited',
              'px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700': employeeInvitationStatus.status === 'accepted',
              'px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-700': employeeInvitationStatus.status === 'interviewed',
              'px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700': employeeInvitationStatus.status === 'rejected',
              'px-3 py-1 rounded-full text-xs font-bold bg-surface-100 text-surface-600': employeeInvitationStatus.status === 'ended'
            }">
              {{ statusLabels[employeeInvitationStatus.status] || employeeInvitationStatus.status }}
            </span>
          </div>
        </div>
      </div>

      <!-- Action Button -->
      <div class="card p-6 animate-fade-in-up">
        <button @click="openInviteModal" class="btn-primary w-full justify-center text-base">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          إرسال دعوة توظيف
        </button>
      </div>

      <!-- Invite Modal -->
      <Teleport to="body">
        <div v-if="showInviteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div class="fixed inset-0 bg-black/30 backdrop-blur-sm" @click="showInviteModal = false"></div>
          <div class="relative bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md animate-scale-in">
            <div class="flex items-center gap-3 mb-6">
              <div class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
              </div>
              <div>
                <h3 class="text-lg font-bold text-slate-900">إرسال دعوة</h3>
                <p class="text-sm text-slate-500">إلى {{ employee.name }}</p>
              </div>
            </div>

            <form @submit.prevent="sendInvite" class="space-y-4">
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">رسالة الدعوة</label>
                <textarea
                  v-model="inviteForm.message"
                  rows="3"
                  placeholder="اكتب رسالة ترحيبية للموظف..."
                  class="input-base resize-none"
                ></textarea>
              </div>

              <div class="flex gap-3 pt-2">
                <button type="submit" :disabled="inviteForm.processing" class="btn-primary flex-1 justify-center">
                  {{ inviteForm.processing ? 'جاري الإرسال...' : 'إرسال الدعوة' }}
                </button>
                <button type="button" @click="showInviteModal = false" class="btn-ghost px-6">
                  إلغاء
                </button>
              </div>
            </form>
          </div>
        </div>
      </Teleport>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const props = defineProps({
  employee: Object,
  employmentStatus: Object,
  employeeInvitationStatus: Object,
})

const showInviteModal = ref(false)

const inviteForm = useForm({
  employee_id: props.employee.id,
  message: '',
})

const statusLabels = {
  invited: 'تمت الدعوة',
  accepted: 'تم القبول',
  interviewed: 'تمت المقابلة',
  hired: 'تم التوظيف',
  rejected: 'مرفوض',
  ended: 'منتهي',
}

const hasTeacherProfile = !!props.employee.teacher_profile
const hasEmployeeRole = props.employee.roles?.some(r => r.name === 'employee') || false

const goBack = () => {
  router.get('/school/hiring/applicants', { type: 'employee' })
}

const openInviteModal = () => {
  inviteForm.message = ''
  showInviteModal.value = true
}

const sendInvite = () => {
  inviteForm.post('/school/employee-invite', {
    preserveScroll: true,
    onSuccess: () => {
      showInviteModal.value = false
      router.get('/school/hiring/applicants', { type: 'employee' })
    },
  })
}
</script>
