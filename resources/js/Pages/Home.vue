<template>
  <MainLayout>
    <div>
      <!-- Hero Section -->
      <div class="relative mb-12 overflow-hidden rounded-3xl bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 shadow-2xl shadow-primary-500/20">
        <div class="absolute inset-0 bg-grid opacity-20"></div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-primary-400/20 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-warm-400/10 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2"></div>
        <div class="relative px-8 py-12 md:px-12 md:py-16">
          <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-3 text-balance animate-fade-in-up">
            {{ $t('messages.welcome') }}
          </h1>
          <p class="text-lg text-primary-100/80 max-w-2xl animate-fade-in-up animate-delay-100">
            {{ $t('messages.hero_subtitle') }}
          </p>

          <!-- Stats -->
          <div class="flex flex-wrap gap-8 mt-8 animate-fade-in-up animate-delay-200">
            <div>
              <div class="text-3xl font-extrabold text-white">{{ totalCount }}+</div>
              <div class="text-sm text-primary-200">{{ $t('messages.teachers_count') }}</div>
            </div>
            <div>
              <div class="text-3xl font-extrabold text-white">{{ subjects.length }}+</div>
              <div class="text-sm text-primary-200">{{ $t('messages.subjects_count') }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="card p-6 mb-8 animate-fade-in-up animate-delay-100">
        <form @submit.prevent="applyFilters">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-1">
              <label class="block text-sm font-semibold text-slate-700 mb-1.5">{{ $t('messages.subject') }}</label>
              <select v-model="filters.subject_id" class="input-base">
                <option value="">{{ $t('messages.all_subjects') }}</option>
                <option v-for="subject in subjects" :key="subject.id" :value="subject.id">
                  {{ subject.name }}
                </option>
              </select>
            </div>

            <div class="md:col-span-1">
              <label class="block text-sm font-semibold text-slate-700 mb-1.5">{{ $t('messages.grade') }}</label>
              <select v-model="filters.grade_id" class="input-base">
                <option value="">{{ $t('messages.all_grades') }}</option>
                <option v-for="grade in grades" :key="grade.id" :value="grade.id">
                  {{ grade.name }}
                </option>
              </select>
            </div>

            <div class="md:col-span-1">
              <label class="block text-sm font-semibold text-slate-700 mb-1.5">{{ $t('messages.search') }}</label>
              <input
                v-model="filters.search"
                type="text"
                :placeholder="$t('messages.search_placeholder')"
                class="input-base"
              />
            </div>

            <div class="md:col-span-1 flex items-end gap-2">
              <button type="submit" class="btn-primary flex-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                {{ $t('messages.search_btn') }}
              </button>
              <button
                v-if="hasActiveFilters"
                type="button"
                @click="resetFilters"
                class="btn-ghost px-4"
                :title="$t('messages.reset')"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>
        </form>
      </div>

      <!-- Section Header -->
      <div class="flex items-center justify-between mb-6 animate-fade-in-up animate-delay-200">
        <div>
          <h2 class="text-2xl font-extrabold text-slate-900">{{ $t('messages.available_teachers') }}</h2>
          <p class="text-slate-500">{{ $t('messages.teachers_count_desc', { count: teachersList.length }) }}</p>
        </div>
      </div>

      <!-- Teachers Grid -->
      <div v-if="teachersList.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <Link
          v-for="(teacher, index) in teachersList"
          :key="teacher.id"
          :href="`/teachers/${teacher.id}`"
          class="group card-hover overflow-hidden animate-fade-in-up block"
          :style="{ animationDelay: `${(index % 6) * 0.1}s` }"
        >
          <!-- Photo -->
          <div class="relative h-48 bg-gradient-to-br from-primary-100 via-primary-50 to-surface-100 overflow-hidden">
            <img
              v-if="teacher.photo"
              :src="`/storage/${teacher.photo}`"
              :alt="teacher.user?.name"
              class="w-full h-full object-cover transition-all duration-500 group-hover:scale-110"
            />
            <div v-else class="w-full h-full flex items-center justify-center">
              <div class="w-20 h-20 rounded-2xl bg-white/80 backdrop-blur flex items-center justify-center text-4xl shadow-sm">
                👨‍🏫
              </div>
            </div>

            <!-- Gradient overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

            <!-- Watermark for employed teachers -->
            <div v-if="teacher.employment_status === 'employed'" class="absolute inset-0 flex items-center justify-center pointer-events-none select-none">
              <span class="text-4xl font-extrabold text-white/30 -rotate-[30deg] tracking-wider uppercase">موظف حالياً</span>
            </div>

            <!-- Badges -->
            <div class="absolute top-3 left-3 flex flex-col gap-2">
              <span class="badge-success shadow-lg shadow-emerald-500/20">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                {{ $t('messages.approved') }}
              </span>
              <span
                v-if="teacher.employment_status === 'employed'"
                class="badge bg-amber-500 text-white shadow-lg shadow-amber-500/20 text-xs"
              >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                موظف حالياً
              </span>
            </div>
          </div>

          <!-- Content -->
          <div class="p-6">
            <!-- Name & Qualification -->
            <div class="mb-4">
              <h3 class="text-lg font-bold text-slate-900 mb-0.5">{{ teacher.user?.name }}</h3>
              <p class="text-sm text-slate-500">{{ teacher.qualification || $t('messages.no_qualification') }}</p>
            </div>

            <!-- Subject -->
            <div class="mb-4">
              <span class="badge-primary">
                {{ teacher.subject?.name }}
              </span>
            </div>

            <!-- Info -->
            <div class="space-y-2.5 text-sm">
              <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-primary-50 flex items-center justify-center flex-shrink-0">
                  <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                  </svg>
                </div>
                <span class="text-slate-600">{{ teacher.experience_years }} {{ $t('messages.years_exp') }}</span>
              </div>

              <div class="flex items-start gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-warm-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                  <svg class="w-4 h-4 text-warm-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                  </svg>
                </div>
                <div class="flex flex-wrap gap-1.5">
                  <span
                    v-for="grade in teacher.grades"
                    :key="grade.id"
                    class="inline-block px-2.5 py-1 bg-surface-100 text-surface-700 rounded-lg text-xs font-medium"
                  >
                    {{ grade.name }}
                  </span>
                </div>
              </div>

              <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-rose-50 flex items-center justify-center flex-shrink-0">
                  <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                </div>
                <span class="text-slate-600">{{ teacher.residence_place || $t('messages.no_location') }}</span>
              </div>

              <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
                  <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                  </svg>
                </div>
                <span class="text-slate-600 ltr" dir="ltr">{{ teacher.phone || '-' }}</span>
              </div>
            </div>

            <!-- Bio -->
            <p v-if="teacher.bio" class="mt-4 text-sm text-slate-500 line-clamp-2 leading-relaxed">
              {{ teacher.bio }}
            </p>
          </div>
        </Link>
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

      <!-- Empty State -->
      <div v-if="teachersList.length === 0 && !loading" class="card text-center py-16 animate-fade-in-up">
        <div class="w-20 h-20 rounded-2xl bg-surface-100 flex items-center justify-center mx-auto mb-6">
          <svg class="w-10 h-10 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-2">{{ $t('messages.no_teachers') }}</h3>
        <p class="text-slate-500">{{ $t('messages.no_teachers_desc') }}</p>
        <button
          v-if="hasActiveFilters"
          @click="resetFilters"
          class="btn-secondary mt-6"
        >
          {{ $t('messages.reset_filters') }}
        </button>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'

const props = defineProps({
  teachers: Array,
  subjects: Array,
  grades: Array,
  filters: Object,
  nextPage: Number,
})

const teachersList = ref([...props.teachers])
const nextPage = ref(props.nextPage)
const loading = ref(false)

const totalCount = computed(() => teachersList.value.length)

const filters = reactive({
  subject_id: props.filters?.subject_id || '',
  grade_id: props.filters?.grade_id || '',
  search: props.filters?.search || '',
})

const hasActiveFilters = computed(() => {
  return filters.subject_id || filters.grade_id || filters.search
})

const sentinel = ref(null)
let observer = null

const loadMore = async () => {
  if (loading.value || !nextPage.value) return
  loading.value = true

  try {
    const params = new URLSearchParams()
    if (props.filters?.subject_id) params.append('subject_id', props.filters.subject_id)
    if (props.filters?.grade_id) params.append('grade_id', props.filters.grade_id)
    if (props.filters?.search) params.append('search', props.filters.search)
    params.append('page', nextPage.value)

    const res = await fetch(`/?${params.toString()}`, {
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
      if (entries[0].isIntersecting) loadMore()
    },
    { threshold: 0.1 }
  )
  if (sentinel.value) observer.observe(sentinel.value)
})

onUnmounted(() => {
  if (observer) observer.disconnect()
})

const applyFilters = () => {
  const params = new URLSearchParams()
  if (filters.subject_id) params.append('subject_id', filters.subject_id)
  if (filters.grade_id) params.append('grade_id', filters.grade_id)
  if (filters.search) params.append('search', filters.search)

  const qs = params.toString()
  window.location.href = qs ? `/?${qs}` : '/'
}

const resetFilters = () => {
  filters.subject_id = ''
  filters.grade_id = ''
  filters.search = ''
  window.location.href = '/'
}
</script>
