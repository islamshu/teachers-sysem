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
          class="card p-6 animate-fade-in-up transition-all duration-300"
        >
          <div class="flex items-start gap-4 mb-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-100 to-surface-100 overflow-hidden flex-shrink-0">
              <img v-if="teacher.photo" :src="`/storage/${teacher.photo}`" :alt="teacher.user?.name" class="w-full h-full object-cover" />
              <div v-else class="w-full h-full flex items-center justify-center text-xl font-bold text-primary-600">
                {{ teacher.user?.name?.charAt(0) }}
              </div>
            </div>
            <div class="flex-1 min-w-0">
              <h3 class="text-lg font-bold text-slate-900 truncate">{{ teacher.user?.name }}</h3>
              <span class="badge-primary text-xs">{{ teacher.subject?.name }}</span>
              <div class="flex gap-2 mt-1">
                <span v-for="grade in teacher.grades" :key="grade.id" class="inline-block px-2 py-0.5 text-xs rounded-lg bg-surface-100 text-surface-600 font-medium">
                  {{ grade.name }}
                </span>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3 mb-4 text-sm">
            <div class="p-2 rounded-xl bg-warm-50 text-center">
              <p class="text-xs text-warm-600 font-semibold">الخبرة</p>
              <p class="font-bold text-slate-900">{{ teacher.experience_years }} سنة</p>
            </div>
            <div class="p-2 rounded-xl bg-primary-50 text-center">
              <p class="text-xs text-primary-600 font-semibold">التقييم</p>
              <p class="font-bold text-primary-700">{{ teacher.qualification || '-' }}</p>
            </div>
          </div>

          <p v-if="teacher.bio" class="text-sm text-slate-600 mb-4 line-clamp-2">{{ teacher.bio }}</p>

          <button
            @click="openInvite(teacher)"
            class="btn-primary w-full justify-center text-sm"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            إرسال دعوة
          </button>
        </div>
      </div>

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
                <select v-model="inviteForm.subject_id" class="input-base" required>
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

              <div class="p-4 rounded-2xl bg-surface-50 border border-surface-200">
                <p class="text-xs font-semibold text-slate-500 mb-2">بعد القبول ستتم مشاركة بيانات التواصل:</p>
                <p class="text-sm text-slate-700">{{ $page.props.auth.user?.school_profile?.phone || 'جوال المدرسة' }}</p>
                <p class="text-sm text-slate-700">{{ $page.props.auth.user?.email }}</p>
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
