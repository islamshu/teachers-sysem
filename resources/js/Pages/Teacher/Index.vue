<template>
  <MainLayout>
    <div dir="rtl">
      <!-- Header -->
      <div class="mb-12">
        <h1 class="text-4xl font-bold text-slate-900 mb-2">المدرسون المتاحون</h1>
        <p class="text-slate-600 text-lg">
          اختر المادة والصف الدراسي لعرض المدرسين المتخصصين
        </p>
      </div>

      <!-- Filters -->
      <div class="card mb-8 p-6">
        <form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <!-- Subject Filter -->
          <div>
            <label class="block text-sm font-semibold text-slate-900 mb-2">المادة</label>
            <select v-model="filters.subject_id" class="input-base">
              <option value="">جميع المواد</option>
              <option v-for="subject in subjects" :key="subject.id" :value="subject.id">
                {{ subject.name }}
              </option>
            </select>
          </div>

          <!-- Grade Filter -->
          <div>
            <label class="block text-sm font-semibold text-slate-900 mb-2">الصف الدراسي</label>
            <select v-model="filters.grade_id" class="input-base">
              <option value="">جميع الصفوف</option>
              <option v-for="grade in grades" :key="grade.id" :value="grade.id">
                {{ grade.name }}
              </option>
            </select>
          </div>

          <!-- Search -->
          <div>
            <label class="block text-sm font-semibold text-slate-900 mb-2">البحث</label>
            <input
              v-model="filters.search"
              type="text"
              placeholder="ابحث عن اسم المدرس..."
              class="input-base"
            />
          </div>

          <!-- Submit -->
          <button type="submit" class="btn-primary col-span-1 md:col-span-3 mt-6">
            بحث
          </button>
        </form>
      </div>

      <!-- Teachers Grid -->
      <div v-if="teachers.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="teacher in teachers" :key="teacher.id" class="card-hover group overflow-hidden">
          <!-- Photo -->
          <div class="relative h-48 bg-gradient-to-br from-indigo-100 to-purple-100 overflow-hidden">
            <img
              v-if="teacher.photo"
              :src="`/storage/${teacher.photo}`"
              :alt="teacher.user?.name"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            />
            <div v-else class="w-full h-full flex items-center justify-center text-6xl opacity-50">
              👨‍🏫
            </div>

            <!-- Status Badge -->
            <div class="absolute top-3 right-3">
              <span class="badge badge-success">✓ موافق عليه</span>
            </div>
          </div>

          <!-- Content -->
          <div class="p-6">
            <!-- Name -->
            <h3 class="text-xl font-bold text-slate-900 mb-1">{{ teacher.user?.name }}</h3>
            <p class="text-sm text-slate-500 mb-4">{{ teacher.qualification }}</p>

            <!-- Subject Badge -->
            <div class="mb-4">
              <span class="badge badge-primary">{{ teacher.subject?.name }}</span>
            </div>

            <!-- Info Grid -->
            <div class="space-y-2 mb-4 text-sm">
              <!-- Experience -->
              <div class="flex items-center gap-2">
                <span class="text-indigo-600 font-semibold">📚</span>
                <span class="text-slate-600">{{ teacher.experience_years }} سنوات خبرة</span>
              </div>

              <!-- Grades -->
              <div class="flex items-start gap-2">
                <span class="text-purple-600 font-semibold mt-1">🎓</span>
                <div class="flex flex-wrap gap-1">
                  <span
                    v-for="grade in teacher.grades"
                    :key="grade.id"
                    class="inline-block px-2 py-1 bg-purple-100 text-purple-700 rounded text-xs"
                  >
                    {{ grade.name }}
                  </span>
                </div>
              </div>

              <!-- Location -->
              <div class="flex items-center gap-2">
                <span class="text-pink-600 font-semibold">📍</span>
                <span class="text-slate-600">{{ teacher.residence_place }}</span>
              </div>

              <!-- Phone -->
              <div class="flex items-center gap-2">
                <span class="text-green-600 font-semibold">📞</span>
                <span class="text-slate-600" dir="ltr">{{ teacher.phone }}</span>
              </div>
            </div>

            <!-- Bio -->
            <p v-if="teacher.bio" class="text-sm text-slate-600 mb-4 line-clamp-2">
              {{ teacher.bio }}
            </p>

            <!-- Contact Button -->
            <button
              @click="contactTeacher(teacher)"
              class="w-full btn-primary text-sm"
            >
              التواصل مع المدرس
            </button>
          </div>
        </div>
      </div>

      <!-- No Results -->
      <div v-else class="card text-center py-12">
        <div class="text-6xl mb-4">🔍</div>
        <h3 class="text-xl font-semibold text-slate-900 mb-2">لم يتم العثور على مدرسين</h3>
        <p class="text-slate-600">جرب تغيير معايير البحث</p>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { reactive } from 'vue'
import MainLayout from '@/Layouts/MainLayout.vue'

const props = defineProps({
  teachers: Array,
  subjects: Array,
  grades: Array,
  filters: Object,
})

const filters = reactive({
  subject_id: props.filters?.subject_id || '',
  grade_id: props.filters?.grade_id || '',
  search: props.filters?.search || '',
})

const applyFilters = () => {
  const params = new URLSearchParams()
  if (filters.subject_id) params.append('subject_id', filters.subject_id)
  if (filters.grade_id) params.append('grade_id', filters.grade_id)
  if (filters.search) params.append('search', filters.search)

  window.location.href = `/?${params.toString()}`
}

const contactTeacher = (teacher) => {
  window.location.href = `tel:${teacher.phone}`
}
</script>
