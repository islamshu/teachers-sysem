<template>
  <MainLayout>
    <div>
      <!-- Header -->
      <div class="flex items-center justify-between mb-8 animate-fade-in-up">
        <div>
          <h1 class="text-3xl font-extrabold text-slate-900">{{ $t('messages.admin_title') }}</h1>
          <p class="text-slate-500 mt-1">{{ $t('messages.admin_desc') }}</p>
        </div>
        <div class="text-center px-6 py-3 rounded-2xl bg-primary-50 border border-primary-100">
          <div class="text-3xl font-extrabold text-primary-700">{{ totalCount }}</div>
          <p class="text-xs font-semibold text-primary-600">{{ $t('messages.pending_review') }}</p>
        </div>
      </div>

      <!-- Flash Message -->
      <Alert v-if="flash.message" variant="success" :title="flash.message" closeable class="mb-6 animate-fade-in-down" />

      <!-- Batch Actions Bar -->
      <div v-if="selectedIds.size > 0" class="flex items-center gap-3 mb-4 p-4 rounded-2xl bg-white border border-surface-200 shadow-sm animate-fade-in-down">
        <span class="text-sm font-bold text-slate-700">{{ selectedIds.size }} مختار</span>
        <div class="flex gap-2 mr-auto">
          <button @click="batchApprove" class="btn-primary text-sm px-4 py-2 bg-emerald-600 hover:bg-emerald-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
            </svg>
            قبول الكل
          </button>
          <button @click="batchReject" class="btn-primary text-sm px-4 py-2 bg-red-600 hover:bg-red-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
            </svg>
            رفض الكل
          </button>
        </div>
      </div>

      <!-- Teachers Table -->
      <div class="card overflow-hidden animate-fade-in-up animate-delay-100">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-surface-50 border-b border-surface-200">
                <th class="px-6 py-4 text-center text-sm font-bold text-slate-700">
                  <input
                    type="checkbox"
                    :checked="allSelected"
                    @change="toggleAll"
                    class="w-4 h-4 rounded border-surface-300 text-primary-600 focus:ring-primary-500 cursor-pointer"
                  />
                </th>
                <th class="px-6 py-4 text-center text-sm font-bold text-slate-700">#</th>
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">{{ $t('messages.teacher') }}</th>
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">{{ $t('messages.subject') }}</th>
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">{{ $t('messages.grade') }}</th>
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">{{ $t('messages.experience') }}</th>
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">{{ $t('messages.phone') }}</th>
                <th class="px-6 py-4 text-center text-sm font-bold text-slate-700">{{ $t('messages.actions') }}</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-surface-100">
              <tr
                v-for="teacher in teachersList"
                :key="teacher.id"
                class="hover:bg-primary-50/50 transition-colors duration-150"
                :class="{ 'bg-primary-50/50': selectedIds.has(teacher.id) }"
              >
                <td class="px-6 py-4 text-center">
                  <input
                    type="checkbox"
                    :checked="selectedIds.has(teacher.id)"
                    @change="toggleId(teacher.id)"
                    class="w-4 h-4 rounded border-surface-300 text-primary-600 focus:ring-primary-500 cursor-pointer"
                  />
                </td>
                <td class="px-6 py-4 text-center text-slate-500 font-medium">{{ teacher.id }}</td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-100 to-surface-100 overflow-hidden flex-shrink-0">
                      <img
                        v-if="teacher.photo"
                        :src="`/storage/${teacher.photo}`"
                        :alt="teacher.user?.name"
                        class="w-full h-full object-cover"
                      />
                      <div v-else class="w-full h-full flex items-center justify-center text-sm font-bold text-primary-600">
                        {{ teacher.user?.name?.charAt(0) }}
                      </div>
                    </div>
                    <div>
                      <p class="font-bold text-slate-900">{{ teacher.user?.name }}</p>
                      <p class="text-xs text-slate-500">{{ teacher.user?.email }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <span class="badge-primary">{{ teacher.subject?.name }}</span>
                </td>
                <td class="px-6 py-4">
                  <div class="flex flex-wrap gap-1">
                    <span
                      v-for="grade in teacher.grades"
                      :key="grade.id"
                      class="inline-block px-2 py-0.5 text-xs rounded-lg bg-surface-100 text-surface-700 font-medium"
                    >
                      {{ grade.name }}
                    </span>
                  </div>
                </td>
                <td class="px-6 py-4 font-medium text-slate-900">
                  {{ teacher.experience_years }} {{ $t('messages.year') }}
                </td>
                <td class="px-6 py-4 text-slate-600 ltr" dir="ltr">
                  {{ teacher.phone }}
                </td>
                <td class="px-6 py-4">
                  <div class="flex gap-2 justify-center">
                    <button
                      @click="approveTeacher(teacher.id)"
                      class="p-2.5 rounded-xl bg-emerald-100 text-emerald-700 hover:bg-emerald-200 hover:shadow-lg hover:shadow-emerald-500/20 transition-all duration-200"
                      :title="$t('messages.approve')"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                      </svg>
                    </button>
                    <button
                      @click="rejectTeacher(teacher.id)"
                      class="p-2.5 rounded-xl bg-red-100 text-red-700 hover:bg-red-200 hover:shadow-lg hover:shadow-red-500/20 transition-all duration-200"
                      :title="$t('messages.reject')"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                      </svg>
                    </button>
                    <button
                      @click="viewTeacher(teacher.id)"
                      class="p-2.5 rounded-xl bg-primary-100 text-primary-700 hover:bg-primary-200 hover:shadow-lg hover:shadow-primary-500/20 transition-all duration-200"
                      :title="$t('messages.view_details')"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Loading / Sentinel -->
        <div ref="sentinel" class="flex justify-center py-8">
          <div v-if="loading" class="flex items-center gap-3 text-slate-500">
            <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <span class="text-sm font-medium">جاري التحميل...</span>
          </div>
          <p v-else-if="!nextPage && teachersList.length > 0" class="text-sm text-slate-400">لا يوجد المزيد</p>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="teachersList.length === 0 && !loading" class="card text-center py-16 animate-fade-in-up">
        <div class="w-20 h-20 rounded-3xl bg-emerald-50 flex items-center justify-center mx-auto mb-6">
          <svg class="w-10 h-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-2">{{ $t('messages.no_pending') }}</h3>
        <p class="text-slate-500">{{ $t('messages.all_reviewed') }}</p>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import Alert from '@/Components/Alert.vue'

const props = defineProps({
  teachers: Array,
  nextPage: Number,
  flash: Object,
  currentStatus: { type: String, default: 'all' },
})

const teachersList = ref([...props.teachers])
const nextPage = ref(props.nextPage)
const loading = ref(false)

const totalCount = computed(() => teachersList.value.length)

const selectedIds = ref(new Set())
const allSelected = computed(() =>
  teachersList.value.length > 0 && selectedIds.value.size === teachersList.value.length
)

const toggleId = (id) => {
  const s = new Set(selectedIds.value)
  if (s.has(id)) s.delete(id); else s.add(id)
  selectedIds.value = s
}

const toggleAll = () => {
  if (allSelected.value) {
    selectedIds.value = new Set()
  } else {
    selectedIds.value = new Set(teachersList.value.map(t => t.id))
  }
}

const batchApprove = () => {
  const ids = [...selectedIds.value]
  if (!confirm(`هل تريد قبول ${ids.length} مدرس؟`)) return
  useForm({ ids }).patch('/admin/teachers/batch/approve', {
    onSuccess: () => location.reload()
  })
}

const batchReject = () => {
  const ids = [...selectedIds.value]
  if (!confirm(`هل تريد رفض ${ids.length} مدرس؟`)) return
  useForm({ ids }).patch('/admin/teachers/batch/reject', {
    onSuccess: () => location.reload()
  })
}

const sentinel = ref(null)
let observer = null

const loadMore = async () => {
  if (loading.value || !nextPage.value) return
  loading.value = true

  try {
    const res = await fetch(`/admin/teachers?page=${nextPage.value}&status=${props.currentStatus}`, {
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
    (entries) => {
      if (entries[0].isIntersecting) {
        loadMore()
      }
    },
    { threshold: 0.1 }
  )
  if (sentinel.value) {
    observer.observe(sentinel.value)
  }
})

onUnmounted(() => {
  if (observer) observer.disconnect()
})

const approveTeacher = (teacherId) => {
  if (confirm(props.flash?.approve_confirm || 'هل تريد قبول هذا المدرس؟')) {
    useForm({}).patch(`/admin/teachers/${teacherId}/approve`, {
      onSuccess: () => location.reload()
    })
  }
}

const rejectTeacher = (teacherId) => {
  if (confirm(props.flash?.reject_confirm || 'هل تريد رفض هذا المدرس؟')) {
    useForm({}).patch(`/admin/teachers/${teacherId}/reject`, {
      onSuccess: () => location.reload()
    })
  }
}

const viewTeacher = (teacherId) => {
  window.location.href = `/admin/teachers/${teacherId}`
}
</script>
