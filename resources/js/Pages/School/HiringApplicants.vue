<template>
  <DashboardLayout>
    <div>
      <div class="flex items-center justify-between mb-8 animate-fade-in-up">
        <div>
          <h1 class="text-3xl font-extrabold text-slate-900">طالبي التوظيف</h1>
          <p class="text-slate-500 mt-1">تصفح المتاحين وأرسل دعوات التوظيف</p>
        </div>
      </div>

      <!-- Type Filter -->
      <div class="flex items-center gap-2 mb-6 p-1 bg-surface-100 rounded-xl w-fit">
        <button
          @click="switchType('teacher')"
          class="px-5 py-2.5 rounded-lg text-sm font-bold transition-all duration-200"
          :class="currentType === 'teacher' ? 'bg-white text-primary-700 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
        >
          معلم
        </button>
        <button
          @click="switchType('employee')"
          class="px-5 py-2.5 rounded-lg text-sm font-bold transition-all duration-200"
          :class="currentType === 'employee' ? 'bg-white text-primary-700 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
        >
          موظف
        </button>
      </div>

      <div v-if="!isApproved" class="card p-8 text-center animate-fade-in-up">
        <div class="w-20 h-20 rounded-3xl bg-amber-100 flex items-center justify-center mx-auto mb-5">
          <svg class="w-10 h-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
          </svg>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-2">بيانات المدرسة غير معتمدة</h3>
        <p class="text-slate-500 max-w-md mx-auto">يجب أن تتم الموافقة على بيانات المدرسة أولاً قبل التمكن من إرسال دعوات.</p>
      </div>

      <!-- Teacher Cards -->
      <template v-if="currentType === 'teacher' && isApproved">
        <div v-if="itemsList.length === 0 && !loading" class="card p-10 text-center animate-fade-in-up">
          <div class="w-16 h-16 rounded-2xl bg-surface-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </div>
          <h3 class="text-lg font-bold text-slate-900 mb-1">لا يوجد مدرسون متاحون</h3>
          <p class="text-sm text-slate-500">لم يتم العثور على مدرسين متاحين حالياً</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
          <div
            v-for="teacher in itemsList"
            :key="teacher.id"
            class="card p-5 animate-fade-in-up transition-all duration-300 cursor-pointer hover:shadow-lg hover:-translate-y-0.5"
            @click="openTeacherDetail(teacher)"
          >
            <div class="flex items-start gap-4 mb-4">
              <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-100 to-surface-100 overflow-hidden flex-shrink-0">
                <img v-if="teacher.photo" :src="`/storage/${teacher.photo}`" :alt="teacher.user?.name" class="w-full h-full object-cover" />
                <div v-else class="w-full h-full flex items-center justify-center text-xl font-bold text-primary-600">
                  {{ teacher.user?.name?.charAt(0) }}
                </div>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                  <h3 class="text-lg font-bold text-slate-900 truncate">{{ teacher.user?.name }}</h3>
                  <span v-if="teacher.gender" class="text-xs">{{ teacher.gender === 'male' ? '♂' : '♀' }}</span>
                </div>
                <div class="flex flex-wrap items-center gap-x-2 gap-y-1 mt-0.5">
                  <span class="badge-primary text-xs">{{ teacher.subject?.name }}</span>
                  <span v-if="teacher.employment_status === 'available'" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    متاح
                  </span>
                </div>
                <div class="flex flex-wrap gap-1.5 mt-1.5">
                  <span v-for="grade in teacher.grades" :key="grade.id" class="inline-block px-2 py-0.5 text-xs rounded-lg bg-surface-100 text-surface-600 font-medium">
                    {{ grade.name }}
                  </span>
                </div>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-2 mb-3 text-sm">
              <div class="p-2 rounded-xl bg-warm-50 text-center">
                <p class="text-xs text-warm-600 font-semibold">الخبرة</p>
                <p class="font-bold text-slate-900">{{ teacher.experience_years }} سنة</p>
              </div>
              <div class="p-2 rounded-xl bg-primary-50 text-center">
                <p class="text-xs text-primary-600 font-semibold">المؤهل</p>
                <p class="font-bold text-primary-700 text-xs truncate">{{ teacher.qualification || '-' }}</p>
              </div>
            </div>

            <div class="space-y-1.5 mb-3 text-sm">
              <div v-if="teacher.residence_place" class="flex items-center gap-2 text-slate-600">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span class="truncate">{{ teacher.residence_place }}</span>
              </div>
              <div v-if="teacher.current_location" class="flex items-center gap-2 text-slate-600">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span class="truncate">{{ teacher.current_location }}</span>
              </div>
            </div>

            <p v-if="teacher.practical_experience" class="text-sm text-slate-600 mb-3 line-clamp-2">{{ teacher.practical_experience }}</p>
            <p v-else-if="teacher.bio" class="text-sm text-slate-600 mb-3 line-clamp-2">{{ teacher.bio }}</p>

            <button
              @click.stop="openTeacherInvite(teacher)"
              class="btn-primary w-full justify-center text-sm"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              إرسال دعوة
            </button>
          </div>
        </div>
      </template>

      <!-- Employee Cards -->
      <template v-if="currentType === 'employee'">
        <div v-if="itemsList.length === 0 && !loading" class="card p-10 text-center animate-fade-in-up">
          <div class="w-16 h-16 rounded-2xl bg-surface-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </div>
          <h3 class="text-lg font-bold text-slate-900 mb-1">لا يوجد موظفون متاحون</h3>
          <p class="text-sm text-slate-500">لم يتم العثور على موظفين متاحين حالياً</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
          <div
            v-for="employee in itemsList"
            :key="employee.id"
            class="card p-5 animate-fade-in-up transition-all duration-300 cursor-pointer hover:shadow-lg hover:-translate-y-0.5"
            @click="openEmployeeDetail(employee)"
          >
            <div class="flex items-start gap-4 mb-4">
              <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-100 to-surface-100 overflow-hidden flex-shrink-0">
                <div class="w-full h-full flex items-center justify-center text-xl font-bold text-primary-600">
                  {{ employee.name?.charAt(0) }}
                </div>
              </div>
              <div class="flex-1 min-w-0">
                <h3 class="text-lg font-bold text-slate-900 truncate">{{ employee.name }}</h3>
                <p class="text-sm text-slate-500 truncate">{{ employee.email }}</p>
                <div v-if="employee.job_title" class="mt-1">
                  <span class="inline-block px-2 py-0.5 text-xs rounded-lg bg-warm-100 text-warm-700 font-bold">
                    {{ employee.job_title }}
                  </span>
                </div>
                <div class="flex flex-wrap gap-1.5 mt-1.5">
                  <span v-for="role in employee.roles" :key="role.id" class="inline-block px-2 py-0.5 text-xs rounded-lg bg-primary-100 text-primary-700 font-medium">
                    {{ role.name }}
                  </span>
                </div>
                  <div class="flex flex-wrap gap-1.5 mt-1.5">
                      <span class="text-xs text-slate-400">{{ formatDate(employee.created_at) }}</span>
                </div>
              </div>
            </div>

            <div class="flex flex-wrap gap-1.5 mb-3">
              <span v-for="branch in employee.branches" :key="branch.id" class="inline-block px-2 py-0.5 text-xs rounded-lg bg-surface-100 text-surface-600 font-medium">
                {{ branch.name }}
              </span>
            </div>

            <button
              @click.stop="openEmployeeInvite(employee)"
              class="btn-primary w-full justify-center text-sm"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              إرسال دعوة
            </button>
          </div>
        </div>
      </template>

      <!-- Loading -->
      <div v-if="isApproved" ref="sentinel" class="flex justify-center py-8">
        <div v-if="loading" class="flex items-center gap-3 text-slate-500">
          <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          <span class="text-sm font-medium">جاري التحميل...</span>
        </div>
      </div>

      <!-- Teacher Detail Modal -->
      <Teleport to="body">
        <div v-if="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div class="fixed inset-0 bg-black/30 backdrop-blur-sm" @click="showDetailModal = false"></div>
          <div class="relative bg-white rounded-2xl shadow-2xl p-6 w-full max-w-lg animate-scale-in max-h-[90vh] overflow-y-auto">
            <div class="flex items-center gap-4 mb-6">
              <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary-100 to-surface-100 overflow-hidden flex-shrink-0">
                <img v-if="detailItem?.photo" :src="`/storage/${detailItem.photo}`" :alt="detailItem.user?.name" class="w-full h-full object-cover" />
                <div v-else class="w-full h-full flex items-center justify-center text-2xl font-bold text-primary-600">
                  {{ detailItem?.user?.name?.charAt(0) || detailItem?.name?.charAt(0) }}
                </div>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                  <h3 class="text-xl font-bold text-slate-900">{{ detailItem?.user?.name }}</h3>
                  <span v-if="detailItem?.gender" class="text-sm">{{ detailItem.gender === 'male' ? '♂' : '♀' }}</span>
                </div>
                <div class="flex flex-wrap gap-2 mt-1">
                  <span class="badge-primary text-xs">{{ detailItem?.subject?.name }}</span>
                  <span v-if="detailItem?.employment_status === 'available'" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    متاح
                  </span>
                </div>
              </div>
              <button @click="showDetailModal = false" class="flex-shrink-0 w-8 h-8 rounded-full bg-surface-100 flex items-center justify-center hover:bg-surface-200 transition-colors">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
              </button>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-5 text-sm">
              <div class="p-3 rounded-xl bg-warm-50 text-center">
                <p class="text-xs text-warm-600 font-semibold">الخبرة</p>
                <p class="font-bold text-slate-900">{{ detailItem?.experience_years }} سنة</p>
              </div>
              <div class="p-3 rounded-xl bg-primary-50 text-center">
                <p class="text-xs text-primary-600 font-semibold">المؤهل</p>
                <p class="font-bold text-primary-700">{{ detailItem?.qualification || '-' }}</p>
              </div>
            </div>

            <div class="space-y-3 mb-5 text-sm">
              <div v-if="detailItem?.residence_place" class="flex items-center gap-3 text-slate-700">
                <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>{{ detailItem.residence_place }}</span>
              </div>
              <div v-if="detailItem?.current_location" class="flex items-center gap-3 text-slate-700">
                <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>{{ detailItem.current_location }}</span>
              </div>
              <div v-if="detailItem?.practical_experience" class="flex items-start gap-3 text-slate-700">
                <svg class="w-4 h-4 text-slate-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>{{ detailItem.practical_experience }}</span>
              </div>
              <div v-else-if="detailItem?.bio" class="flex items-start gap-3 text-slate-700">
                <svg class="w-4 h-4 text-slate-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                <span>{{ detailItem.bio }}</span>
              </div>
            </div>

            <div class="flex flex-wrap gap-1.5 mb-5">
              <span v-for="grade in detailItem?.grades" :key="grade.id" class="inline-block px-2.5 py-1 text-xs rounded-lg bg-surface-100 text-surface-600 font-medium">
                {{ grade.name }}
              </span>
            </div>

            <button
              @click="openTeacherInviteFromDetail"
              class="btn-primary w-full justify-center text-sm"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              إرسال دعوة
            </button>
          </div>
        </div>
      </Teleport>

      <!-- Teacher Invite Modal -->
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
                <p class="text-sm text-slate-500">إلى {{ selectedTeacher?.user?.name }}</p>
              </div>
            </div>

            <form @submit.prevent="sendTeacherInvite" class="space-y-4">
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">المادة <span class="text-red-500">*</span></label>
                <select v-model="inviteForm.subject_id" disabled class="input-base" required>
                  <option value="">اختر المادة</option>
                  <option v-for="subject in subjects" :key="subject.id" :value="subject.id">{{ subject.name }}</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">رسالة الدعوة</label>
                <textarea
                  v-model="inviteForm.message"
                  rows="3"
                  placeholder="اكتب رسالة ترحيبية للمدرس..."
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

      <!-- Employee Invite Modal -->
      <Teleport to="body">
        <div v-if="showEmployeeInviteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div class="fixed inset-0 bg-black/30 backdrop-blur-sm" @click="showEmployeeInviteModal = false"></div>
          <div class="relative bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md animate-scale-in">
            <div class="flex items-center gap-3 mb-6">
              <div class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
              </div>
              <div>
                <h3 class="text-lg font-bold text-slate-900">إرسال دعوة</h3>
                <p class="text-sm text-slate-500">إلى {{ selectedEmployee?.name }}</p>
              </div>
            </div>

            <form @submit.prevent="sendEmployeeInvite" class="space-y-4">
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">رسالة الدعوة</label>
                <textarea
                  v-model="employeeInviteForm.message"
                  rows="3"
                  placeholder="اكتب رسالة ترحيبية للموظف..."
                  class="input-base resize-none"
                ></textarea>
              </div>

              <div class="flex gap-3 pt-2">
                <button type="submit" :disabled="employeeInviteForm.processing" class="btn-primary flex-1 justify-center">
                  {{ employeeInviteForm.processing ? 'جاري الإرسال...' : 'إرسال الدعوة' }}
                </button>
                <button type="button" @click="showEmployeeInviteModal = false" class="btn-ghost px-6">
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
import { ref, onMounted, onUnmounted, watch } from 'vue'
import { router, useForm, usePage } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const props = defineProps({
  items: Array,
  nextPage: Number,
  type: String,
  subjects: Array,
  isApproved: Boolean,
})

const formatDate = (date) => {
  if (!date) return ''
  const d = new Date(date)
  return d.toLocaleDateString('ar-SA', { year: 'numeric', month: 'short', day: 'numeric' })
}

const currentType = ref(props.type)
const itemsList = ref([...props.items])
const nextPage = ref(props.nextPage)
const loading = ref(false)
const sentinel = ref(null)
let observer = null

const showInviteModal = ref(false)
const selectedTeacher = ref(null)
const subjects = ref(props.subjects || [])

const showDetailModal = ref(false)
const detailItem = ref(null)

const showEmployeeInviteModal = ref(false)
const selectedEmployee = ref(null)

const inviteForm = useForm({
  teacher_id: '',
  subject_id: '',
  message: '',
})

const employeeInviteForm = useForm({
  employee_id: '',
  message: '',
})

const switchType = (type) => {
  if (type === currentType.value) return
  router.get(`/school/hiring/applicants`, { type }, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      currentType.value = type
    },
  })
}

const openTeacherDetail = (teacher) => {
  detailItem.value = teacher
  showDetailModal.value = true
}

const openTeacherInviteFromDetail = () => {
  showDetailModal.value = false
  openTeacherInvite(detailItem.value)
}

const openTeacherInvite = (teacher) => {
  selectedTeacher.value = teacher
  inviteForm.teacher_id = teacher.id
  inviteForm.subject_id = teacher.subject_id || ''
  inviteForm.message = ''
  showInviteModal.value = true
}

const sendTeacherInvite = () => {
  inviteForm.post('/school/invite', {
    preserveScroll: true,
    onSuccess: () => {
      showInviteModal.value = false
      itemsList.value = itemsList.value.filter(t => t.id !== selectedTeacher.value?.id)
      selectedTeacher.value = null
    },
  })
}

const openEmployeeDetail = (employee) => {
  router.get(`/school/hiring/employee/${employee.id}`)
}

const openEmployeeInvite = (employee) => {
  selectedEmployee.value = employee
  employeeInviteForm.employee_id = employee.id
  employeeInviteForm.message = ''
  showEmployeeInviteModal.value = true
}

const sendEmployeeInvite = () => {
  employeeInviteForm.post('/school/employee-invite', {
    preserveScroll: true,
    onSuccess: () => {
      showEmployeeInviteModal.value = false
      itemsList.value = itemsList.value.filter(e => e.id !== selectedEmployee.value?.id)
      selectedEmployee.value = null
    },
  })
}

const loadMore = async () => {
  if (loading.value || !nextPage.value) return
  loading.value = true
  try {
    const url = currentType.value === 'teacher'
      ? `/school/hiring/applicants?page=${nextPage.value}&type=teacher`
      : `/school/hiring/applicants?page=${nextPage.value}&type=employee`
    const res = await fetch(url, {
      headers: { Accept: 'application/json' },
    })
    const json = await res.json()
    itemsList.value.push(...json.data)
    nextPage.value = json.current_page < json.last_page ? json.current_page + 1 : null
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
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

watch(() => props.items, (newItems) => {
  itemsList.value = [...newItems]
})

watch(() => props.nextPage, (newPage) => {
  nextPage.value = newPage
})

watch(() => props.type, (newType) => {
  currentType.value = newType
})
</script>
