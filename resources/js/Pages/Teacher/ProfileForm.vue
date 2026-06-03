<template>
  <MainLayout>
    <div class="max-w-4xl mx-auto">
      <!-- Header -->
      <div class="mb-8 animate-fade-in-up">
        <h1 class="text-3xl font-extrabold text-slate-900 mb-2">{{ $t('messages.add_profile_btn') }}</h1>
        <p class="text-slate-500">{{ $t('messages.bio_placeholder') }}</p>
      </div>

      <!-- Alert -->
      <Alert v-if="form.hasErrors" variant="error" :title="$t('messages.error')" class="mb-6 animate-fade-in-down" />

      <!-- Form -->
      <div class="card animate-fade-in-up animate-delay-100">
        <form @submit.prevent="submit" class="p-8 space-y-8">
          <!-- Photo Upload -->
          <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">{{ $t('messages.photo') }}</label>
            <div class="relative">
              <div class="flex items-center gap-4">
                <div class="w-24 h-24 rounded-2xl bg-surface-100 overflow-hidden flex-shrink-0 border-2 border-dashed border-surface-300">
                  <img v-if="photoPreview" :src="photoPreview" class="w-full h-full object-cover" />
                  <div v-else class="w-full h-full flex items-center justify-center text-3xl text-surface-400">📷</div>
                </div>
                <div class="flex-1">
                  <label class="btn-secondary cursor-pointer inline-flex">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ $t('messages.photo') }}
                    <input @change="handlePhotoChange" type="file" accept="image/*" class="hidden" />
                  </label>
                  <p class="text-xs text-slate-400 mt-2">{{ $t('messages.photo_hint') }}</p>
                </div>
              </div>
            </div>
            <p v-if="form.errors.photo" class="mt-2 text-sm text-red-500 font-medium">{{ form.errors.photo }}</p>
          </div>

          <!-- Personal Information -->
          <div>
            <div class="flex items-center gap-3 mb-6">
              <div class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
              <h3 class="text-lg font-bold text-slate-900">{{ $t('messages.personal_info') }}</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">{{ $t('messages.phone') }}</label>
                <input
                  v-model="form.phone"
                  type="tel"
                  placeholder="05xxxxxxxxx"
                  class="input-base"
                  :class="{ 'input-error': form.errors.phone }"
                />
                <p v-if="form.errors.phone" class="mt-1.5 text-sm text-red-500 font-medium">{{ form.errors.phone }}</p>
              </div>

              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">{{ $t('messages.birth_date') }}</label>
                <input
                  v-model="form.birth_date"
                  type="date"
                  class="input-base"
                  :class="{ 'input-error': form.errors.birth_date }"
                />
                <p v-if="form.errors.birth_date" class="mt-1.5 text-sm text-red-500 font-medium">{{ form.errors.birth_date }}</p>
              </div>

              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">{{ $t('messages.gender') }}</label>
                <select v-model="form.gender" class="input-base" :class="{ 'input-error': form.errors.gender }">
                  <option value="">{{ $t('messages.select') }}</option>
                  <option value="male">{{ $t('messages.male') }}</option>
                  <option value="female">{{ $t('messages.female') }}</option>
                </select>
                <p v-if="form.errors.gender" class="mt-1.5 text-sm text-red-500 font-medium">{{ form.errors.gender }}</p>
              </div>
            </div>
          </div>

          <!-- Academic Information -->
          <div class="divider"></div>

          <div>
            <div class="flex items-center gap-3 mb-6">
              <div class="w-10 h-10 rounded-xl bg-warm-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-warm-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 7l-9-5 9-5 9 5-9 5z" />
                </svg>
              </div>
              <h3 class="text-lg font-bold text-slate-900">{{ $t('messages.academic_info') }}</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">{{ $t('messages.subject') }}</label>
                <select v-model="form.subject_id" class="input-base" :class="{ 'input-error': form.errors.subject_id }">
                  <option value="">{{ $t('messages.select_subject') }}</option>
                  <option v-for="subject in subjects" :key="subject.id" :value="subject.id">
                    {{ subject.name }}
                  </option>
                </select>
                <p v-if="form.errors.subject_id" class="mt-1.5 text-sm text-red-500 font-medium">{{ form.errors.subject_id }}</p>
              </div>

              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">{{ $t('messages.experience_years') }}</label>
                <input
                  v-model="form.experience_years"
                  type="number"
                  min="0"
                  class="input-base"
                  :class="{ 'input-error': form.errors.experience_years }"
                />
                <p v-if="form.errors.experience_years" class="mt-1.5 text-sm text-red-500 font-medium">{{ form.errors.experience_years }}</p>
              </div>

              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">{{ $t('messages.qualification') }}</label>
                <input
                  v-model="form.qualification"
                  type="text"
                  :placeholder="$t('messages.qualification_placeholder')"
                  class="input-base"
                  :class="{ 'input-error': form.errors.qualification }"
                />
                <p v-if="form.errors.qualification" class="mt-1.5 text-sm text-red-500 font-medium">{{ form.errors.qualification }}</p>
              </div>

              <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 mb-3">{{ $t('messages.grade') }}</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                  <label
                    v-for="grade in grades"
                    :key="grade.id"
                    class="relative flex items-center gap-3 p-3 rounded-xl border-2 border-surface-200 cursor-pointer transition-all duration-200"
                    :class="{ 'border-primary-400 bg-primary-50': form.grade_ids.includes(grade.id) }"
                  >
                    <input
                      type="checkbox"
                      :value="grade.id"
                      v-model="form.grade_ids"
                      class="w-4 h-4 rounded border-surface-300 text-primary-600 focus:ring-primary-500"
                    />
                    <span class="text-sm font-medium text-slate-700">{{ grade.name }}</span>
                  </label>
                </div>
                <p v-if="form.errors.grade_ids" class="mt-1.5 text-sm text-red-500 font-medium">{{ form.errors.grade_ids }}</p>
              </div>
            </div>
          </div>

          <!-- Location Information -->
          <div class="divider"></div>

          <div>
            <div class="flex items-center gap-3 mb-6">
              <div class="w-10 h-10 rounded-xl bg-rose-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
              <h3 class="text-lg font-bold text-slate-900">{{ $t('messages.location_info') }}</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">{{ $t('messages.residence_place') }}</label>
                <input
                  v-model="form.residence_place"
                  type="text"
                  placeholder="المدينة / المحافظة"
                  class="input-base"
                  :class="{ 'input-error': form.errors.residence_place }"
                />
                <p v-if="form.errors.residence_place" class="mt-1.5 text-sm text-red-500 font-medium">{{ form.errors.residence_place }}</p>
              </div>

              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">{{ $t('messages.current_location') }}</label>
                <input
                  v-model="form.current_location"
                  type="text"
                  placeholder="المدينة / المحافظة"
                  class="input-base"
                  :class="{ 'input-error': form.errors.current_location }"
                />
                <p v-if="form.errors.current_location" class="mt-1.5 text-sm text-red-500 font-medium">{{ form.errors.current_location }}</p>
              </div>
            </div>
          </div>

          <!-- Bio -->
          <div class="divider"></div>

          <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">{{ $t('messages.bio') }}</label>
            <textarea
              v-model="form.bio"
              rows="4"
              :placeholder="$t('messages.bio_placeholder')"
              class="input-base resize-none"
              :class="{ 'input-error': form.errors.bio }"
            ></textarea>
            <p v-if="form.errors.bio" class="mt-1.5 text-sm text-red-500 font-medium">{{ form.errors.bio }}</p>
          </div>

          <!-- Submit -->
          <div class="flex items-center gap-4 pt-6 border-t border-surface-200">
            <button
              type="submit"
              :disabled="form.processing"
              class="btn-primary flex-1 md:flex-none px-10"
            >
              <svg v-if="form.processing" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              {{ form.processing ? $t('messages.saving') : $t('messages.save') }}
            </button>
            <Link href="/" class="btn-ghost px-8">
              {{ $t('messages.cancel') }}
            </Link>
          </div>
        </form>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import Alert from '@/Components/Alert.vue'

const props = defineProps({
  subjects: Array,
  grades: Array,
  profile: Object,
})

const photoPreview = ref(props.profile?.photo ? `/storage/${props.profile.photo}` : null)

const form = useForm({
  photo: null,
  phone: props.profile?.phone || '',
  birth_date: props.profile?.birth_date || '',
  gender: props.profile?.gender || '',
  subject_id: props.profile?.subject_id || '',
  grade_ids: props.profile?.grades?.map(g => g.id) || [],
  experience_years: props.profile?.experience_years || '',
  qualification: props.profile?.qualification || '',
  residence_place: props.profile?.residence_place || '',
  current_location: props.profile?.current_location || '',
  bio: props.profile?.bio || '',
})

const handlePhotoChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    form.photo = file
    photoPreview.value = URL.createObjectURL(file)
  }
}

const submit = () => {
  if (props.profile) {
    form
      .transform((data) => ({
        ...data,
        _method: 'PUT',
        ...(data.photo ? {} : { photo: undefined }),
      }))
      .post('/teacher-profile/update')
  } else {
    form.post('/teacher-profile/store')
  }
}
</script>
