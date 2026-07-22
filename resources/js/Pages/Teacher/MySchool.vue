<template>
  <DashboardLayout>
    <div>
      <template v-if="employment">
        <template v-if="employment.status === 'ended'">
          <div class="relative mb-8 overflow-hidden rounded-3xl bg-gradient-to-br from-slate-700 via-slate-600 to-slate-800 shadow-2xl shadow-slate-500/20 animate-fade-in-up">
            <div class="absolute inset-0 bg-grid opacity-20"></div>
            <div class="relative px-8 py-10">
              <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center text-3xl shadow-lg">🏫</div>
                <div>
                  <h1 class="text-3xl font-extrabold text-white">{{ employment.school?.school_profile?.school_name }}</h1>
                  <p class="text-slate-300">سابقاً</p>
                </div>
              </div>
            </div>
          </div>
        </template>
        <template v-else>
          <div class="relative mb-8 overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-700 via-emerald-600 to-emerald-800 shadow-2xl shadow-emerald-500/20 animate-fade-in-up">
            <div class="absolute inset-0 bg-grid opacity-20"></div>
            <div class="relative px-8 py-10">
              <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center text-3xl shadow-lg">🏫</div>
                <div>
                  <h1 class="text-3xl font-extrabold text-white">{{ employment.school?.school_profile?.school_name }}</h1>
                  <p class="text-emerald-200">جهة عملك الحالية</p>
                </div>
              </div>
            </div>
          </div>
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <div class="lg:col-span-2 space-y-6">
            <template v-if="employment.status === 'ended'">
              <div class="card p-6 animate-fade-in-up animate-delay-100 border-2 border-slate-200">
                <div class="flex items-center gap-3 mb-4">
                  <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </div>
                  <h2 class="text-lg font-bold text-slate-900">تم إنهاء التعاقد</h2>
                </div>
                <p class="text-slate-600">تم إنهاء التعاقد معك من قبل المدرسة. يمكنك الآن التقديم على فرص توظيف جديدة.</p>
              </div>
            </template>
            <template v-else>
              <div class="card p-6 animate-fade-in-up animate-delay-100">
                <div class="flex items-center gap-3 mb-4">
                  <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                  </div>
                  <h2 class="text-lg font-bold text-slate-900">تم توظيفك بنجاح!</h2>
                </div>
                <p class="text-slate-600">تم تعيينك في المدرسة اعتباراً من <strong>{{ formatDate(employment.hired_at) }}</strong> كمدرس لمادة <strong>{{ employment.subject?.name }}</strong>.</p>
              </div>
            </template>

            <div v-if="employment.school?.school_profile" class="card p-6 animate-fade-in-up animate-delay-200">
              <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center">
                  <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
                </div>
                <h2 class="text-lg font-bold text-slate-900">بيانات المدرسة</h2>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-3 rounded-xl bg-surface-50">
                  <p class="text-xs text-slate-500 font-semibold">اسم المدرسة</p>
                  <p class="font-bold text-slate-900">{{ employment.school.school_profile.school_name }}</p>
                </div>
                <div class="p-3 rounded-xl bg-surface-50">
                  <p class="text-xs text-slate-500 font-semibold">مدير المدرسة</p>
                  <p class="font-bold text-slate-900">{{ employment.school.school_profile.principal_name || '-' }}</p>
                </div>
                <div class="p-3 rounded-xl bg-surface-50">
                  <p class="text-xs text-slate-500 font-semibold">الهاتف</p>
                  <p class="font-bold text-slate-900" dir="ltr">{{ employment.school.school_profile.phone || '-' }}</p>
                </div>
                <div class="p-3 rounded-xl bg-surface-50">
                  <p class="text-xs text-slate-500 font-semibold">العنوان</p>
                  <p class="font-bold text-slate-900">{{ employment.school.school_profile.address || '-' }}</p>
                </div>
              </div>
            </div>
          </div>

          <div class="space-y-6">
            <div class="card p-6 animate-fade-in-up animate-delay-150">
              <h2 class="text-lg font-bold text-slate-900 mb-4">روابط سريعة</h2>
              <div class="space-y-2">
                <a :href="`tel:${employment.school?.school_profile?.phone}`" class="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-slate-700 hover:bg-primary-50 hover:text-primary-700 transition-all duration-200 group">
                  <div class="w-9 h-9 rounded-xl bg-surface-100 group-hover:bg-primary-100 flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                  </div>
                  <span class="font-medium">اتصال هاتفي</span>
                </a>
              </div>
            </div>

            <template v-if="employment.status === 'hired'">
              <div class="card p-6 bg-gradient-to-br from-emerald-600 to-emerald-800 text-white animate-fade-in-up animate-delay-200">
                <div class="flex items-center gap-3 mb-4">
                  <div class="w-10 h-10 rounded-xl bg-white/15 backdrop-blur flex items-center justify-center">🎉</div>
                  <h3 class="font-bold">مبروك!</h3>
                </div>
                <p class="text-emerald-100 text-sm leading-relaxed">نتمنى لك التوفيق في مسيرتك المهنية مع المدرسة. تواصل مع إدارة المدرسة لأي استفسارات.</p>
              </div>
            </template>
          </div>
        </div>
      </template>

      <template v-else>
        <div class="card p-10 text-center animate-fade-in-up">
          <div class="w-16 h-16 rounded-2xl bg-surface-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
          </div>
          <h3 class="text-lg font-bold text-slate-900 mb-1">غير موظف بعد</h3>
          <p class="text-sm text-slate-500">لم يتم توظيفك من قبل أي مدرسة حالياً</p>
        </div>
      </template>

      <!-- Employment History -->
      <div v-if="history.length > 0" class="mt-8 animate-fade-in-up animate-delay-300">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 rounded-xl bg-surface-100 flex items-center justify-center">
            <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h2 class="text-lg font-bold text-slate-900">السجل الوظيفي</h2>
        </div>
        <div class="space-y-3">
          <div
            v-for="item in history"
            :key="item.id"
            class="card p-4 animate-fade-in-up"
          >
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-surface-100 flex items-center justify-center text-lg flex-shrink-0">🏫</div>
                <div>
                  <p class="font-bold text-slate-900">{{ item.school?.school_profile?.school_name || 'مدرسة' }}</p>
                  <p class="text-xs text-slate-500">
                    {{ item.subject?.name }}
                    <span class="mx-1">·</span>
                    {{ formatDate(item.hired_at) }}
                  </p>
                </div>
              </div>
              <span
                class="px-3 py-1 rounded-full text-xs font-bold whitespace-nowrap"
                :class="statusClass(item.status)"
              >
                {{ statusLabel(item.status) }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const props = defineProps({
  employment: Object,
  history: { type: Array, default: () => [] },
})

const formatDate = (date) => {
  if (!date) return '-'
  const d = new Date(date)
  const pad = (n) => String(n).padStart(2, '0')
  return `${d.getFullYear()}/${pad(d.getMonth() + 1)}/${pad(d.getDate())}`
}

const statusLabel = (status) => {
  const map = { ended: 'منتهي', rejected: 'مرفوض' }
  return map[status] || status
}

const statusClass = (status) => {
  if (status === 'ended') return 'bg-slate-100 text-slate-700'
  if (status === 'rejected') return 'bg-red-100 text-red-700'
  return 'bg-surface-100 text-slate-600'
}
</script>
