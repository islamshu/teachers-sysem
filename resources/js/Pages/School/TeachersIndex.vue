<template>
  <DashboardLayout>
    <div>
      <div class="flex items-center justify-between mb-8 animate-fade-in-up">
        <div>
          <h1 class="text-3xl font-extrabold text-slate-900">تصفح المدرسين</h1>
          <p class="text-slate-500 mt-1">تصفح المدرسين المتاحين وأرسل دعوات التوظيف</p>
        </div>
      </div>

      <div v-if="!isApproved" class="card p-8 text-center animate-fade-in-up">
        <div class="w-20 h-20 rounded-3xl bg-amber-100 flex items-center justify-center mx-auto mb-5">
          <svg class="w-10 h-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
          </svg>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-2">بيانات المدرسة غير معتمدة</h3>
        <p class="text-slate-500 max-w-md mx-auto">يجب أن تتم الموافقة على بيانات المدرسة أولاً قبل التمكن من إرسال دعوات للمدرسين. يرجى الانتظار حتى تقوم الإدارة بمراجعة واعتماد بيانات المدرسة.</p>
      </div>

      <div v-if="isApproved" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <div
          v-for="teacher in teachersList"
          :key="teacher.id"
          class="card p-5 animate-fade-in-up transition-all duration-300 cursor-pointer hover:shadow-lg hover:-translate-y-0.5"
          @click="openDetail(teacher)"
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
            <div v-if="teacher.birth_date" class="flex items-center gap-2 text-slate-600">
              <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.5a4.5 4.5 0 01-4.5 4.5H7.5A4.5 4.5 0 013 15.5V8l9-4 9 4v7.5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12v4"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12v4"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12v4"/></svg>
              <span>{{ teacher.birth_date }}</span>
            </div>
          </div>

          <p v-if="teacher.practical_experience" class="text-sm text-slate-600 mb-3 line-clamp-2">{{ teacher.practical_experience }}</p>
          <p v-else-if="teacher.bio" class="text-sm text-slate-600 mb-3 line-clamp-2">{{ teacher.bio }}</p>

          <button
            @click.stop="openInvite(teacher)"
            class="btn-primary w-full justify-center text-sm"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            إرسال دعوة
          </button>
        </div>
      </div>

      <!-- Teacher Detail Modal -->
      <Teleport to="body">
        <div v-if="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div class="fixed inset-0 bg-black/30 backdrop-blur-sm" @click="showDetailModal = false"></div>
          <div class="relative bg-white rounded-2xl shadow-2xl p-6 w-full max-w-lg animate-scale-in max-h-[90vh] overflow-y-auto">
            <div class="flex items-center gap-4 mb-6">
              <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary-100 to-surface-100 overflow-hidden flex-shrink-0">
                <img v-if="detailTeacher?.photo" :src="`/storage/${detailTeacher.photo}`" :alt="detailTeacher.user?.name" class="w-full h-full object-cover" />
                <div v-else class="w-full h-full flex items-center justify-center text-2xl font-bold text-primary-600">
                  {{ detailTeacher?.user?.name?.charAt(0) }}
                </div>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                  <h3 class="text-xl font-bold text-slate-900">{{ detailTeacher?.user?.name }}</h3>
                  <span v-if="detailTeacher?.gender" class="text-sm">{{ detailTeacher.gender === 'male' ? '♂' : '♀' }}</span>
                </div>
                <div class="flex flex-wrap gap-2 mt-1">
                  <span class="badge-primary text-xs">{{ detailTeacher?.subject?.name }}</span>
                  <span v-if="detailTeacher?.employment_status === 'available'" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
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
                <p class="font-bold text-slate-900">{{ detailTeacher?.experience_years }} سنة</p>
              </div>
              <div class="p-3 rounded-xl bg-primary-50 text-center">
                <p class="text-xs text-primary-600 font-semibold">المؤهل</p>
                <p class="font-bold text-primary-700">{{ detailTeacher?.qualification || '-' }}</p>
              </div>
            </div>

            <div class="space-y-3 mb-5 text-sm">
              <div v-if="detailTeacher?.residence_place" class="flex items-center gap-3 text-slate-700">
                <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>{{ detailTeacher.residence_place }}</span>
              </div>
              <div v-if="detailTeacher?.current_location" class="flex items-center gap-3 text-slate-700">
                <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>{{ detailTeacher.current_location }}</span>
              </div>
              <div v-if="detailTeacher?.birth_date" class="flex items-center gap-3 text-slate-700">
                <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.5a4.5 4.5 0 01-4.5 4.5H7.5A4.5 4.5 0 013 15.5V8l9-4 9 4v7.5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12v4"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12v4"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12v4"/></svg>
                <span>{{ detailTeacher.birth_date }}</span>
              </div>
              <div v-if="detailTeacher?.practical_experience" class="flex items-start gap-3 text-slate-700">
                <svg class="w-4 h-4 text-slate-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>{{ detailTeacher.practical_experience }}</span>
              </div>
              <div v-else-if="detailTeacher?.bio" class="flex items-start gap-3 text-slate-700">
                <svg class="w-4 h-4 text-slate-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                <span>{{ detailTeacher.bio }}</span>
              </div>
            </div>

            <div class="flex flex-wrap gap-1.5 mb-5">
              <span v-for="grade in detailTeacher?.grades" :key="grade.id" class="inline-block px-2.5 py-1 text-xs rounded-lg bg-surface-100 text-surface-600 font-medium">
                {{ grade.name }}
              </span>
            </div>

            <button
              @click="openInviteFromDetail(detailTeacher)"
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

      <div v-if="isApproved" ref="sentinel" class="flex justify-center py-8">
        <div v-if="loading" class="flex items-center gap-3 text-slate-500">
          <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          <span class="text-sm font-medium">جاري التحميل...</span>
        </div>
        <p v-else-if="teachersList.length === 0" class="text-sm text-slate-400">لا يوجد مدرسون متاحون حالياً</p>
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
                <p class="text-sm text-slate-500">إلى {{ selectedTeacher?.user?.name }}</p>
              </div>
            </div>

            <form @submit.prevent="sendInvite" class="space-y-4">
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">المادة <span class="text-red-500">*</span></label>
                <select v-model="inviteForm.subject_id" disabled class="input-base" required>
                  <option value="">اختر المادة</option>
                  <option  v-for="subject in subjects" :key="subject.id" :value="subject.id">{{ subject.name }}</option>
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
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const props = defineProps({
  teachers: Array,
  nextPage: Number,
  subjects: Array,
  isApproved: Boolean,
})

const teachersList = ref([...props.teachers])
const nextPage = ref(props.nextPage)
const loading = ref(false)
const sentinel = ref(null)
let observer = null

const showInviteModal = ref(false)
const selectedTeacher = ref(null)
const subjects = ref(props.subjects || [])

const showDetailModal = ref(false)
const detailTeacher = ref(null)

const openDetail = (teacher) => {
  detailTeacher.value = teacher
  showDetailModal.value = true
}

const openInviteFromDetail = (teacher) => {
  showDetailModal.value = false
  openInvite(teacher)
}

const inviteForm = useForm({
  teacher_id: '',
  subject_id: '',
  message: '',
})

const openInvite = (teacher) => {
  selectedTeacher.value = teacher
  inviteForm.teacher_id = teacher.id
  inviteForm.subject_id = teacher.subject_id || ''
  inviteForm.message = ''
  showInviteModal.value = true
}

const sendInvite = () => {
  inviteForm.post('/school/invite', {
    preserveScroll: true,
    onSuccess: () => {
      showInviteModal.value = false
      teachersList.value = teachersList.value.filter(t => t.id !== selectedTeacher.value?.id)
      selectedTeacher.value = null
    },
  })
}

const loadMore = async () => {
  if (loading.value || !nextPage.value) return
  loading.value = true
  try {
    const res = await fetch(`/school/teachers?page=${nextPage.value}`, {
      headers: { Accept: 'application/json' },
    })
    const json = await res.json()
    teachersList.value.push(...json.data)
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
</script>
