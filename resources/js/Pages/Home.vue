<template>
  <MainLayout>
    <div>
      <!-- Slider Section -->
    <div v-if="activeSlides.length > 0" class="mb-12">
  <div class="relative w-full overflow-hidden rounded-2xl"
       style="height: 500px;">

    <div
      v-for="(slide, i) in activeSlides"
      :key="slide.id"
      v-show="i === currentSlide"
      class="absolute inset-0 transition-opacity duration-700"
      :class="i === currentSlide ? 'opacity-100' : 'opacity-0'"
    >
      <img
        :src="'/storage/' + slide.image"
        :alt="slide.title || ''"
        class="w-full h-full object-cover"
        @error="onImageError"
      />

      <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>

      <div class="absolute bottom-0 right-0 left-0 p-8 md:p-12 text-white">
        <h2
          v-if="slide.title"
          class="text-2xl md:text-4xl font-extrabold mb-2"
        >
          {{ slide.title }}
        </h2>

        <p
          v-if="slide.subtitle"
          class="text-base md:text-lg text-white/80 max-w-2xl"
        >
          {{ slide.subtitle }}
        </p>

        <a
          v-if="slide.link"
          :href="slide.link"
          target="_blank"
          rel="noopener"
          class="inline-flex items-center gap-2 mt-4 px-6 py-2.5 bg-white/20 backdrop-blur-sm rounded-xl text-white font-semibold hover:bg-white/30 transition-all"
        >
          اكتشف المزيد
        </a>
      </div>
    </div>

    <!-- Previous -->
    <button
      @click="prevSlide"
      class="absolute right-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 rounded-full bg-white/20 backdrop-blur-sm text-white"
    >
      
      <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
      </svg>
    </button>

    <!-- Next -->
    <button
      @click="nextSlide"
      class="absolute left-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 rounded-full bg-white/20 backdrop-blur-sm text-white"
    >
      <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
      </svg>
    </button>

  </div>
</div>

      <!-- About Section -->
      <div v-if="aboutContent" class="card p-8 md:p-12 mb-12 animate-fade-in-up">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-8 items-center">
          <div class="md:col-span-3">
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 mb-4">
              {{ aboutTitle }}
            </h2>
            <p class="text-slate-600 leading-relaxed text-lg">{{ aboutDescription }}</p>
            <div v-if="aboutContent" class="mt-6 text-slate-600 leading-relaxed whitespace-pre-line">
              {{ aboutContent }}
            </div>
          </div>
          <div class="md:col-span-2 flex justify-center">
            <div class="w-48 h-48 md:w-64 md:h-64 rounded-3xl bg-gradient-to-br from-primary-100 via-primary-50 to-surface-200 flex items-center justify-center shadow-inner">
              <svg class="w-24 h-24 md:w-32 md:h-32 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Statistics Section -->
      <div class="mb-12 animate-fade-in-up animate-delay-100">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="card p-8 text-center hover:shadow-lg transition-shadow duration-300">
            <div class="w-16 h-16 rounded-2xl bg-primary-100 flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
            <div class="text-4xl md:text-5xl font-extrabold text-primary-600 mb-2">{{ totalTeachers }}+</div>
            <div class="text-slate-500 font-medium">مدرس معتمد</div>
          </div>

          <div class="card p-8 text-center hover:shadow-lg transition-shadow duration-300">
            <div class="w-16 h-16 rounded-2xl bg-warm-100 flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-warm-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
            </div>
            <div class="text-4xl md:text-5xl font-extrabold text-warm-600 mb-2">{{ subjectsCount }}+</div>
            <div class="text-slate-500 font-medium">مادة دراسية</div>
          </div>

          <div class="card p-8 text-center hover:shadow-lg transition-shadow duration-300">
            <div class="w-16 h-16 rounded-2xl bg-emerald-100 flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
            <div class="text-4xl md:text-5xl font-extrabold text-emerald-600 mb-2">{{ totalSchools }}+</div>
            <div class="text-slate-500 font-medium">مدرسة مسجلة</div>
          </div>
        </div>
      </div>

      <!-- Features Section -->
      <div v-if="activeFeatures.length > 0" class="mb-12 animate-fade-in-up animate-delay-150">
        <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 text-center mb-10">لماذا نحن؟</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <div v-for="f in activeFeatures" :key="f.id" class="card p-6 text-center hover:shadow-lg transition-shadow duration-300 group">
            <div class="w-14 h-14 rounded-2xl bg-primary-100 flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
              <svg v-if="f.icon === 'star'" class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
              </svg>
              <svg v-else-if="f.icon === 'shield'" class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
              <svg v-else-if="f.icon === 'lightning'" class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
              <svg v-else-if="f.icon === 'heart'" class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
              </svg>
              <svg v-else-if="f.icon === 'people'" class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <svg v-else-if="f.icon === 'chart'" class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
              <svg v-else-if="f.icon === 'globe'" class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
              </svg>
              <svg v-else class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
              </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">{{ f.title }}</h3>
            <p class="text-slate-600 text-sm leading-relaxed">{{ f.description }}</p>
          </div>
        </div>
      </div>

      <!-- Testimonials Section -->
      <div v-if="activeTestimonials.length > 0" class="mb-12 animate-fade-in-up animate-delay-200">
        <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 text-center mb-10">ماذا يقول عنا المستخدمون</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="t in activeTestimonials" :key="t.id" class="card p-6 relative">
            <svg class="text-primary-200 w-8 h-8 mb-3" fill="currentColor" viewBox="0 0 24 24">
              <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
            </svg>
            <p class="text-slate-600 leading-relaxed mb-4">{{ t.content }}</p>
            <div class="flex items-center gap-3 mt-auto">
              <div class="w-10 h-10 rounded-full bg-primary-100 flex-shrink-0 overflow-hidden">
                <img v-if="t.avatar" :src="'/storage/' + t.avatar" class="w-full h-full object-cover" />
                <div v-else class="w-full h-full flex items-center justify-center text-primary-600 text-sm font-bold">{{ t.name.charAt(0) }}</div>
              </div>
              <div>
                <div class="font-bold text-slate-900 text-sm">{{ t.name }}</div>
                <div v-if="t.role" class="text-xs text-slate-500">{{ t.role }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Registration Steps Section -->
      <div v-if="activeSteps.length > 0" class="mb-12 animate-fade-in-up animate-delay-300">
        <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 text-center mb-10">خطوات التسجيل</h2>
        <div class="relative">
          <div class="hidden md:block absolute top-12 left-1/2 -translate-x-1/2 w-[80%] h-0.5 bg-primary-200"></div>
          <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-8">
            <div v-for="(step, i) in activeSteps" :key="step.id" class="relative flex flex-col items-center text-center">
              <div class="w-12 h-12 rounded-full bg-primary-600 text-white flex items-center justify-center text-xl font-bold mb-4 relative z-10 shadow-lg shadow-primary-200">
                {{ i + 1 }}
              </div>
              <h3 class="text-lg font-bold text-slate-900 mb-2">{{ step.title }}</h3>
              <p class="text-slate-600 text-sm leading-relaxed">{{ step.description }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Contact Us Section -->
      <div class="mb-12 animate-fade-in-up animate-delay-300">
        <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 text-center mb-10">تواصل معنا</h2>
        <div class="max-w-2xl mx-auto">
          <div class="card p-8">
            <form @submit.prevent="submitContact">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-1">الاسم</label>
                  <input
                    v-model="contactForm.name"
                    type="text"
                    required
                    class="w-full px-4 py-2.5 rounded-xl border border-surface-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none transition-all"
                    placeholder="الاسم الكامل"
                  />
                  <p v-if="contactForm.errors.name" class="text-red-500 text-xs mt-1">{{ contactForm.errors.name }}</p>
                </div>
                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-1">البريد الإلكتروني</label>
                  <input
                    v-model="contactForm.email"
                    type="email"
                    required
                    class="w-full px-4 py-2.5 rounded-xl border border-surface-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none transition-all"
                    placeholder="example@email.com"
                  />
                  <p v-if="contactForm.errors.email" class="text-red-500 text-xs mt-1">{{ contactForm.errors.email }}</p>
                </div>
              </div>
              <div class="mb-4">
                <label class="block text-sm font-bold text-slate-700 mb-1">رقم الجوال</label>
                <input
                  v-model="contactForm.phone"
                  type="text"
                  class="w-full px-4 py-2.5 rounded-xl border border-surface-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none transition-all"
                  placeholder="05xxxxxxxx"
                />
                <p v-if="contactForm.errors.phone" class="text-red-500 text-xs mt-1">{{ contactForm.errors.phone }}</p>
              </div>
              <div class="mb-6">
                <label class="block text-sm font-bold text-slate-700 mb-1">الرسالة</label>
                <textarea
                  v-model="contactForm.message"
                  required
                  rows="4"
                  class="w-full px-4 py-2.5 rounded-xl border border-surface-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none transition-all resize-none"
                  placeholder="اكتب رسالتك هنا..."
                ></textarea>
                <p v-if="contactForm.errors.message" class="text-red-500 text-xs mt-1">{{ contactForm.errors.message }}</p>
              </div>
              <button
                type="submit"
                :disabled="contactForm.processing"
                class="btn-primary w-full py-3 text-base"
              >
                <svg v-if="contactForm.processing" class="w-5 h-5 animate-spin mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span v-else>إرسال الرسالة</span>
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { usePage, useForm } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'

const props = defineProps({
  slides: Array,
  features: Array,
  testimonials: Array,
  steps: Array,
  totalTeachers: Number,
  totalSchools: Number,
  subjects: Array,
})

const page = usePage()
const settings = page.props.settings || {}

const subjectsCount = computed(() => props.subjects?.length || 0)
const aboutTitle = computed(() => settings.site_name || 'منصتنا')
const aboutDescription = computed(() => settings.description || '')
const aboutContent = computed(() => settings.about || '')

const activeSlides = computed(() => (props.slides || []).filter(s => s.is_active))
const activeFeatures = computed(() => (props.features || []).filter(f => f.is_active))
const activeTestimonials = computed(() => (props.testimonials || []).filter(t => t.is_active))
const activeSteps = computed(() => (props.steps || []).filter(s => s.is_active))

const currentSlide = ref(0)
let slideTimer = null

const startSlideTimer = () => {
  stopSlideTimer()
  if (activeSlides.value.length > 1) {
    slideTimer = setInterval(() => {
      currentSlide.value = (currentSlide.value + 1) % activeSlides.value.length
    }, 5000)
  }
}

const stopSlideTimer = () => {
  if (slideTimer) {
    clearInterval(slideTimer)
    slideTimer = null
  }
}

const nextSlide = () => {
  stopSlideTimer()
  currentSlide.value = (currentSlide.value + 1) % activeSlides.value.length
  startSlideTimer()
}

const prevSlide = () => {
  stopSlideTimer()
  currentSlide.value = (currentSlide.value - 1 + activeSlides.value.length) % activeSlides.value.length
  startSlideTimer()
}

const onImageError = (e) => {
  e.target.style.display = 'none'
}

const goToSlide = (i) => {
  stopSlideTimer()
  currentSlide.value = i
  startSlideTimer()
}

const contactForm = useForm({
  name: '',
  email: '',
  phone: '',
  message: '',
})

const submitContact = () => {
  contactForm.post('/contact', {
    preserveScroll: true,
    onSuccess: () => {
      contactForm.reset()
    },
  })
}

onMounted(() => {
  startSlideTimer()
})

onUnmounted(() => {
  stopSlideTimer()
})
</script>
