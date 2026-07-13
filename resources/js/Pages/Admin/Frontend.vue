<template>
  <DashboardLayout>
    <div>
      <div class="flex items-center justify-between mb-8 animate-fade-in-up">
        <div>
          <h1 class="text-3xl font-extrabold text-slate-900">إعدادات الصفحة الرئيسية</h1>
          <p class="text-slate-500 mt-1">إدارة كل ما يظهر في الصفحة الرئيسية من هنا</p>
        </div>
      </div>

      <Alert v-if="flash.success" variant="success" :title="flash.success" closeable class="mb-6 animate-fade-in-down" />

      <div class="space-y-8">
        <!-- ========== HERO / ABOUT / CONTACT SETTINGS ========== -->
        <div class="card p-6 animate-fade-in-up">
          <button @click="openSection = openSection === 'settings' ? null : 'settings'" class="flex items-center justify-between w-full text-right">
            <h2 class="text-xl font-bold text-slate-900 flex items-center gap-3">
              <div class="w-9 h-9 rounded-xl bg-primary-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
              الإعدادات العامة
            </h2>
            <svg class="w-5 h-5 text-slate-400 transition-transform" :class="openSection === 'settings' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>
          <div v-show="openSection === 'settings'" class="mt-6">
            <form @submit.prevent="submitSettings">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-2">اسم الموقع</label>
                  <input v-model="settingsForm.site_name" type="text" class="input-base" />
                </div>
                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-2">اللون الأساسي</label>
                  <div class="flex gap-3 items-center">
                    <input v-model="settingsForm.primary_color" type="color" class="w-10 h-10 rounded-xl border border-surface-200 cursor-pointer" />
                    <input v-model="settingsForm.primary_color" type="text" class="input flex-1" dir="ltr" />
                  </div>
                </div>
                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-2">شعار الموقع</label>
                  <input @change="onLogoChange" type="file" accept="image/png,image/jpg,image/jpeg,image/svg+xml" class="input-base" />
                  <div v-if="logoPreview || settings?.site_logo" class="mt-3">
                    <img :src="logoPreview || `/storage/${settings.site_logo}`" alt="Logo" class="h-12 rounded-lg" />
                  </div>
                </div>
                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-2">وصف الموقع</label>
                  <textarea v-model="settingsForm.description" class="input-base" rows="3"></textarea>
                </div>
                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-2">البريد الإلكتروني</label>
                  <input v-model="settingsForm.contact_email" type="email" class="input-base" dir="ltr" />
                </div>
                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-2">رقم الهاتف</label>
                  <input v-model="settingsForm.contact_phone" type="text" class="input-base" dir="ltr" />
                </div>
                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-2">العنوان</label>
                  <input v-model="settingsForm.address" type="text" class="input-base" />
                </div>
                <div class="flex items-center gap-3">
                  <input v-model="settingsForm.show_stats" type="checkbox" id="show_stats" class="w-5 h-5 rounded border-surface-300 text-primary-600 focus:ring-primary-500" true-value="1" false-value="0" />
                  <label for="show_stats" class="text-sm font-bold text-slate-700">إظهار إحصائيات (عدد المدرسين والمواد)</label>
                </div>
                <div class="md:col-span-2">
                  <label class="block text-sm font-bold text-slate-700 mb-2">نبذة عن الموقع</label>
                  <textarea v-model="settingsForm.about" class="input-base" rows="5"></textarea>
                </div>
              </div>
              <div class="flex justify-end mt-6">
                <button type="submit" :disabled="settingsForm.processing" class="btn-primary">
                  {{ settingsForm.processing ? 'جاري الحفظ...' : 'حفظ الإعدادات' }}
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- ========== SLIDES ========== -->
        <div class="card p-6 animate-fade-in-up animate-delay-100">
          <button @click="openSection = openSection === 'slides' ? null : 'slides'" class="flex items-center justify-between w-full text-right">
            <h2 class="text-xl font-bold text-slate-900 flex items-center gap-3">
              <div class="w-9 h-9 rounded-xl bg-primary-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
              الشرائح ({{ slides.length }})
            </h2>
            <div class="flex items-center gap-2">
              <button @click.stop="openSlideModal(null)" class="btn-primary text-sm py-1.5 px-3">+ إضافة</button>
              <svg class="w-5 h-5 text-slate-400 transition-transform" :class="openSection === 'slides' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </div>
          </button>
          <div v-show="openSection === 'slides'" class="mt-6">
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-surface-50 border-b border-surface-200">
                    <th class="px-4 py-3 text-right text-sm font-bold text-slate-700 w-10"></th>
                    <th class="px-4 py-3 text-right text-sm font-bold text-slate-700">الصورة</th>
                    <th class="px-4 py-3 text-right text-sm font-bold text-slate-700">العنوان</th>
                    <th class="px-4 py-3 text-center text-sm font-bold text-slate-700">فعال</th>
                    <th class="px-4 py-3 text-center text-sm font-bold text-slate-700"></th>
                  </tr>
                </thead>
                <tbody ref="slidesTbody" class="divide-y divide-surface-100">
                  <tr v-for="slide in slides" :key="slide.id" :data-id="slide.id" class="hover:bg-primary-50/50 transition-colors">
                    <td class="px-4 py-3"><span class="drag-handle cursor-grab active:cursor-grabbing text-slate-400 select-none text-lg">⠿</span></td>
                    <td class="px-4 py-3">
                      <img :src="`/storage/${slide.image}`" class="w-20 h-12 rounded-lg object-cover border border-surface-200" />
                    </td>
                    <td class="px-4 py-3 font-medium text-slate-900 max-w-[200px] truncate">{{ slide.title || '-' }}</td>
                    <td class="px-4 py-3 text-center">
                      <span :class="slide.is_active ? 'badge-success' : 'badge bg-surface-200 text-surface-600'">{{ slide.is_active ? 'نعم' : 'لا' }}</span>
                    </td>
                    <td class="px-4 py-3">
                      <div class="flex gap-2 justify-center">
                        <button @click="openSlideModal(slide)" class="p-1.5 rounded-lg bg-primary-100 text-primary-700 hover:bg-primary-200" title="تعديل">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                          </svg>
                        </button>
                        <button @click="confirmDeleteSlide(slide)" class="p-1.5 rounded-lg bg-red-100 text-red-700 hover:bg-red-200" title="حذف">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                          </svg>
                        </button>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="slides.length === 0">
                    <td colspan="5" class="px-4 py-8 text-center text-slate-500">لا توجد شرائح</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- ========== FEATURES ========== -->
        <div class="card p-6 animate-fade-in-up animate-delay-200">
          <button @click="openSection = openSection === 'features' ? null : 'features'" class="flex items-center justify-between w-full text-right">
            <h2 class="text-xl font-bold text-slate-900 flex items-center gap-3">
              <div class="w-9 h-9 rounded-xl bg-primary-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
              </div>
              الميزات ({{ features.length }})
            </h2>
            <div class="flex items-center gap-2">
              <button @click.stop="openFeatureModal(null)" class="btn-primary text-sm py-1.5 px-3">+ إضافة</button>
              <svg class="w-5 h-5 text-slate-400 transition-transform" :class="openSection === 'features' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </div>
          </button>
          <div v-show="openSection === 'features'" class="mt-6">
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-surface-50 border-b border-surface-200">
                    <th class="px-4 py-3 text-right text-sm font-bold text-slate-700">#</th>
                    <th class="px-4 py-3 text-right text-sm font-bold text-slate-700">العنوان</th>
                    <th class="px-4 py-3 text-right text-sm font-bold text-slate-700">الوصف</th>
                    <th class="px-4 py-3 text-right text-sm font-bold text-slate-700">الأيقونة</th>
                    <th class="px-4 py-3 text-center text-sm font-bold text-slate-700">فعال</th>
                    <th class="px-4 py-3 text-center text-sm font-bold text-slate-700"></th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-surface-100">
                  <tr v-for="(f, i) in features" :key="f.id" class="hover:bg-primary-50/50 transition-colors">
                    <td class="px-4 py-3 text-slate-400 font-bold">{{ f.sort_order || i + 1 }}</td>
                    <td class="px-4 py-3 font-medium text-slate-900">{{ f.title }}</td>
                    <td class="px-4 py-3 text-slate-600 max-w-[300px] truncate">{{ f.description }}</td>
                    <td class="px-4 py-3">
                      <span class="text-xs bg-surface-100 px-2 py-1 rounded-lg">{{ f.icon || '—' }}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                      <span :class="f.is_active ? 'badge-success' : 'badge bg-surface-200 text-surface-600'">{{ f.is_active ? 'نعم' : 'لا' }}</span>
                    </td>
                    <td class="px-4 py-3">
                      <div class="flex gap-2 justify-center">
                        <button @click="openFeatureModal(f)" class="p-1.5 rounded-lg bg-primary-100 text-primary-700 hover:bg-primary-200">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                          </svg>
                        </button>
                        <button @click="confirmDeleteFeature(f)" class="p-1.5 rounded-lg bg-red-100 text-red-700 hover:bg-red-200">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                          </svg>
                        </button>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="features.length === 0">
                    <td colspan="6" class="px-4 py-8 text-center text-slate-500">لا توجد ميزات</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- ========== TESTIMONIALS ========== -->
        <div class="card p-6 animate-fade-in-up animate-delay-300">
          <button @click="openSection = openSection === 'testimonials' ? null : 'testimonials'" class="flex items-center justify-between w-full text-right">
            <h2 class="text-xl font-bold text-slate-900 flex items-center gap-3">
              <div class="w-9 h-9 rounded-xl bg-primary-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
              </div>
              الآراء ({{ testimonials.length }})
            </h2>
            <div class="flex items-center gap-2">
              <button @click.stop="openTestimonialModal(null)" class="btn-primary text-sm py-1.5 px-3">+ إضافة</button>
              <svg class="w-5 h-5 text-slate-400 transition-transform" :class="openSection === 'testimonials' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </div>
          </button>
          <div v-show="openSection === 'testimonials'" class="mt-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              <div v-for="t in testimonials" :key="t.id" class="card p-4 relative group border border-surface-200">
                <div class="flex items-start gap-3 mb-3">
                  <div class="w-10 h-10 rounded-full bg-primary-100 flex-shrink-0 overflow-hidden">
                    <img v-if="t.avatar" :src="'/storage/' + t.avatar" class="w-full h-full object-cover" />
                    <div v-else class="w-full h-full flex items-center justify-center text-primary-600 text-sm font-bold">{{ t.name.charAt(0) }}</div>
                  </div>
                  <div class="min-w-0 flex-1">
                    <div class="font-bold text-slate-900 text-sm truncate">{{ t.name }}</div>
                    <div v-if="t.role" class="text-xs text-slate-500 truncate">{{ t.role }}</div>
                  </div>
                  <span :class="t.is_active ? 'badge-success text-xs' : 'badge bg-surface-200 text-surface-600 text-xs'">{{ t.is_active ? 'نشط' : 'غير نشط' }}</span>
                </div>
                <p class="text-slate-600 text-sm line-clamp-3">{{ t.content }}</p>
                <div class="absolute top-1 left-1 opacity-0 group-hover:opacity-100 transition-opacity flex gap-1">
                  <button @click="openTestimonialModal(t)" class="p-1 rounded-lg bg-primary-100 text-primary-700 hover:bg-primary-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>
                  <button @click="confirmDeleteTestimonial(t)" class="p-1 rounded-lg bg-red-100 text-red-700 hover:bg-red-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </div>
              <div v-if="testimonials.length === 0" class="col-span-full text-center text-slate-500 py-8">لا توجد آراء</div>
            </div>
          </div>
        </div>

        <!-- ========== STEPS ========== -->
        <div class="card p-6 animate-fade-in-up animate-delay-[400ms]">
          <button @click="openSection = openSection === 'steps' ? null : 'steps'" class="flex items-center justify-between w-full text-right">
            <h2 class="text-xl font-bold text-slate-900 flex items-center gap-3">
              <div class="w-9 h-9 rounded-xl bg-primary-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
              </div>
              خطوات التسجيل ({{ steps.length }})
            </h2>
            <div class="flex items-center gap-2">
              <button @click.stop="openStepModal(null)" class="btn-primary text-sm py-1.5 px-3">+ إضافة</button>
              <svg class="w-5 h-5 text-slate-400 transition-transform" :class="openSection === 'steps' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </div>
          </button>
          <div v-show="openSection === 'steps'" class="mt-6">
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-surface-50 border-b border-surface-200">
                    <th class="px-4 py-3 text-right text-sm font-bold text-slate-700">#</th>
                    <th class="px-4 py-3 text-right text-sm font-bold text-slate-700">العنوان</th>
                    <th class="px-4 py-3 text-right text-sm font-bold text-slate-700">الوصف</th>
                    <th class="px-4 py-3 text-center text-sm font-bold text-slate-700">فعال</th>
                    <th class="px-4 py-3 text-center text-sm font-bold text-slate-700"></th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-surface-100">
                  <tr v-for="(step, i) in steps" :key="step.id" class="hover:bg-primary-50/50 transition-colors">
                    <td class="px-4 py-3 text-slate-400 font-bold">{{ step.sort_order || i + 1 }}</td>
                    <td class="px-4 py-3 font-medium text-slate-900">{{ step.title }}</td>
                    <td class="px-4 py-3 text-slate-600 max-w-[300px] truncate">{{ step.description }}</td>
                    <td class="px-4 py-3 text-center">
                      <span :class="step.is_active ? 'badge-success' : 'badge bg-surface-200 text-surface-600'">{{ step.is_active ? 'نعم' : 'لا' }}</span>
                    </td>
                    <td class="px-4 py-3">
                      <div class="flex gap-2 justify-center">
                        <button @click="openStepModal(step)" class="p-1.5 rounded-lg bg-primary-100 text-primary-700 hover:bg-primary-200">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                          </svg>
                        </button>
                        <button @click="confirmDeleteStep(step)" class="p-1.5 rounded-lg bg-red-100 text-red-700 hover:bg-red-200">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                          </svg>
                        </button>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="steps.length === 0">
                    <td colspan="5" class="px-4 py-8 text-center text-slate-500">لا توجد خطوات</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- ========== MODALS ========== -->
      <Teleport to="body">
        <!-- Slide Modal -->
        <div v-if="slideModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="slideModal = false">
          <div class="fixed inset-0 bg-black/30 backdrop-blur-sm" @click="slideModal = false"></div>
          <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg p-8 animate-scale-in">
            <h3 class="text-xl font-bold text-slate-900 mb-6">{{ editingSlide ? 'تعديل الشريحة' : 'إضافة شريحة' }}</h3>
            <form @submit.prevent="submitSlide">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1.5">الصورة <span class="text-red-500">*</span></label>
                  <input ref="slideFileInput" @change="onSlideImageChange" type="file" accept="image/png,image/jpg,image/jpeg,image/webp" class="input-base" :required="!editingSlide" />
                  <div v-if="slideImagePreview || (editingSlide && editingSlide.image)" class="mt-3">
                    <img :src="slideImagePreview || `/storage/${editingSlide.image}`" class="h-20 rounded-lg border" />
                  </div>
                </div>
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1.5">العنوان</label>
                  <input v-model="slideForm.title" type="text" class="input-base" />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1.5">النص الفرعي</label>
                  <textarea v-model="slideForm.subtitle" class="input-base min-h-[60px]"></textarea>
                </div>
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1.5">الرابط</label>
                  <input v-model="slideForm.link" type="text" class="input-base" dir="ltr" />
                </div>
                <div class="flex items-center gap-3">
                  <input v-model="slideForm.is_active" type="checkbox" id="slide_active" class="w-5 h-5 rounded border-surface-300 text-primary-600 focus:ring-primary-500" />
                  <label for="slide_active" class="text-sm font-semibold text-slate-700">شريحة نشطة</label>
                </div>
              </div>
              <div class="flex gap-3 mt-8">
                <button type="submit" class="btn-primary flex-1" :disabled="slideForm.processing">{{ slideForm.processing ? 'جاري...' : (editingSlide ? 'تحديث' : 'إضافة') }}</button>
                <button type="button" @click="slideModal = false" class="btn-ghost px-6">إلغاء</button>
              </div>
            </form>
          </div>
        </div>

        <!-- Testimonial Modal -->
        <div v-if="testimonialModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="testimonialModal = false">
          <div class="fixed inset-0 bg-black/30 backdrop-blur-sm" @click="testimonialModal = false"></div>
          <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg p-8 animate-scale-in">
            <h3 class="text-xl font-bold text-slate-900 mb-6">{{ editingTestimonial ? 'تعديل الرأي' : 'إضافة رأي' }}</h3>
            <form @submit.prevent="submitTestimonial">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1.5">الاسم <span class="text-red-500">*</span></label>
                  <input v-model="testimonialForm.name" type="text" class="input-base" required />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1.5">المسمى</label>
                  <input v-model="testimonialForm.role" type="text" class="input-base" />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1.5">النص <span class="text-red-500">*</span></label>
                  <textarea v-model="testimonialForm.content" class="input-base min-h-[100px]" required></textarea>
                </div>
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1.5">الصورة الرمزية</label>
                  <input @change="onAvatarChange" type="file" accept="image/png,image/jpg,image/jpeg,image/webp" class="input-base" />
                  <div v-if="avatarPreview || (editingTestimonial && editingTestimonial.avatar)" class="mt-3">
                    <img :src="avatarPreview || `/storage/${editingTestimonial.avatar}`" class="h-16 w-16 rounded-full object-cover border" />
                  </div>
                </div>
                <div class="flex items-center gap-3">
                  <input v-model="testimonialForm.is_active" type="checkbox" id="testimonial_active" class="w-5 h-5 rounded border-surface-300 text-primary-600 focus:ring-primary-500" />
                  <label for="testimonial_active" class="text-sm font-semibold text-slate-700">رأي نشط</label>
                </div>
              </div>
              <div class="flex gap-3 mt-8">
                <button type="submit" class="btn-primary flex-1" :disabled="testimonialForm.processing">{{ testimonialForm.processing ? 'جاري...' : (editingTestimonial ? 'تحديث' : 'إضافة') }}</button>
                <button type="button" @click="testimonialModal = false" class="btn-ghost px-6">إلغاء</button>
              </div>
            </form>
          </div>
        </div>

        <!-- Feature Modal -->
        <div v-if="featureModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="featureModal = false">
          <div class="fixed inset-0 bg-black/30 backdrop-blur-sm" @click="featureModal = false"></div>
          <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg p-8 animate-scale-in">
            <h3 class="text-xl font-bold text-slate-900 mb-6">{{ editingFeature ? 'تعديل الميزة' : 'إضافة ميزة' }}</h3>
            <form @submit.prevent="submitFeature">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1.5">العنوان <span class="text-red-500">*</span></label>
                  <input v-model="featureForm.title" type="text" class="input-base" required />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1.5">الوصف <span class="text-red-500">*</span></label>
                  <textarea v-model="featureForm.description" class="input-base min-h-[80px]" required></textarea>
                </div>
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1.5">الأيقونة</label>
                  <div class="grid grid-cols-4 gap-3">
                    <button type="button" @click="featureForm.icon = 'star'" :class="['flex flex-col items-center gap-1 p-3 rounded-xl border-2 transition-all', featureForm.icon === 'star' ? 'border-primary-500 bg-primary-50' : 'border-surface-200 hover:border-primary-300']">
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>
                      <span class="text-xs font-medium">نجمة</span>
                    </button>
                    <button type="button" @click="featureForm.icon = 'shield'" :class="['flex flex-col items-center gap-1 p-3 rounded-xl border-2 transition-all', featureForm.icon === 'shield' ? 'border-primary-500 bg-primary-50' : 'border-surface-200 hover:border-primary-300']">
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                      <span class="text-xs font-medium">أمان</span>
                    </button>
                    <button type="button" @click="featureForm.icon = 'lightning'" :class="['flex flex-col items-center gap-1 p-3 rounded-xl border-2 transition-all', featureForm.icon === 'lightning' ? 'border-primary-500 bg-primary-50' : 'border-surface-200 hover:border-primary-300']">
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                      <span class="text-xs font-medium">سرعة</span>
                    </button>
                    <button type="button" @click="featureForm.icon = 'heart'" :class="['flex flex-col items-center gap-1 p-3 rounded-xl border-2 transition-all', featureForm.icon === 'heart' ? 'border-primary-500 bg-primary-50' : 'border-surface-200 hover:border-primary-300']">
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                      <span class="text-xs font-medium">حب</span>
                    </button>
                    <button type="button" @click="featureForm.icon = 'people'" :class="['flex flex-col items-center gap-1 p-3 rounded-xl border-2 transition-all', featureForm.icon === 'people' ? 'border-primary-500 bg-primary-50' : 'border-surface-200 hover:border-primary-300']">
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                      <span class="text-xs font-medium">مجتمع</span>
                    </button>
                    <button type="button" @click="featureForm.icon = 'chart'" :class="['flex flex-col items-center gap-1 p-3 rounded-xl border-2 transition-all', featureForm.icon === 'chart' ? 'border-primary-500 bg-primary-50' : 'border-surface-200 hover:border-primary-300']">
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                      <span class="text-xs font-medium">إحصائيات</span>
                    </button>
                    <button type="button" @click="featureForm.icon = 'globe'" :class="['flex flex-col items-center gap-1 p-3 rounded-xl border-2 transition-all', featureForm.icon === 'globe' ? 'border-primary-500 bg-primary-50' : 'border-surface-200 hover:border-primary-300']">
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" /></svg>
                      <span class="text-xs font-medium">عالمي</span>
                    </button>
                    <button type="button" @click="featureForm.icon = ''" :class="['flex flex-col items-center gap-1 p-3 rounded-xl border-2 transition-all', !featureForm.icon ? 'border-primary-500 bg-primary-50' : 'border-surface-200 hover:border-primary-300']">
                      <span class="text-xs font-medium">بدون</span>
                    </button>
                  </div>
                </div>
                <div class="flex items-center gap-3">
                  <input v-model="featureForm.is_active" type="checkbox" id="feature_active" class="w-5 h-5 rounded border-surface-300 text-primary-600 focus:ring-primary-500" />
                  <label for="feature_active" class="text-sm font-semibold text-slate-700">ميزة نشطة</label>
                </div>
              </div>
              <div class="flex gap-3 mt-8">
                <button type="submit" class="btn-primary flex-1" :disabled="featureForm.processing">{{ featureForm.processing ? 'جاري...' : (editingFeature ? 'تحديث' : 'إضافة') }}</button>
                <button type="button" @click="featureModal = false" class="btn-ghost px-6">إلغاء</button>
              </div>
            </form>
          </div>
        </div>

        <!-- Step Modal -->
        <div v-if="stepModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="stepModal = false">
          <div class="fixed inset-0 bg-black/30 backdrop-blur-sm" @click="stepModal = false"></div>
          <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg p-8 animate-scale-in">
            <h3 class="text-xl font-bold text-slate-900 mb-6">{{ editingStep ? 'تعديل الخطوة' : 'إضافة خطوة' }}</h3>
            <form @submit.prevent="submitStep">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1.5">العنوان <span class="text-red-500">*</span></label>
                  <input v-model="stepForm.title" type="text" class="input-base" required />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1.5">الوصف <span class="text-red-500">*</span></label>
                  <textarea v-model="stepForm.description" class="input-base min-h-[80px]" required></textarea>
                </div>
                <div class="flex items-center gap-3">
                  <input v-model="stepForm.is_active" type="checkbox" id="step_active" class="w-5 h-5 rounded border-surface-300 text-primary-600 focus:ring-primary-500" />
                  <label for="step_active" class="text-sm font-semibold text-slate-700">خطوة نشطة</label>
                </div>
              </div>
              <div class="flex gap-3 mt-8">
                <button type="submit" class="btn-primary flex-1" :disabled="stepForm.processing">{{ stepForm.processing ? 'جاري...' : (editingStep ? 'تحديث' : 'إضافة') }}</button>
                <button type="button" @click="stepModal = false" class="btn-ghost px-6">إلغاء</button>
              </div>
            </form>
          </div>
        </div>
      </Teleport>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue'
import { useForm, usePage, router } from '@inertiajs/vue3'
import Sortable from 'sortablejs'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import Alert from '@/Components/Alert.vue'

const props = defineProps({
  settings: Object,
  features: Array,
  slides: Array,
  testimonials: Array,
  steps: Array,
})

const page = usePage()
const flash = page.props.flash || {}

const openSection = ref(null)

// ---- Settings Form ----
const logoPreview = ref(null)
const settingsForm = useForm({
  site_name: props.settings?.site_name || '',
  site_logo: null,
  primary_color: props.settings?.primary_color || '#14b8a6',
  description: props.settings?.description || '',
  contact_email: props.settings?.contact_email || '',
  contact_phone: props.settings?.contact_phone || '',
  address: props.settings?.address || '',
  about: props.settings?.about || '',
  show_stats: props.settings?.show_stats ?? '1',
})

const onLogoChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    settingsForm.site_logo = file
    const reader = new FileReader()
    reader.onload = (ev) => { logoPreview.value = ev.target.result }
    reader.readAsDataURL(file)
  }
}

const submitSettings = () => {
  settingsForm.post('/admin/frontend/settings', {
    onSuccess: () => logoPreview.value = null,
  })
}

// ---- Slides ----
const slidesTbody = ref(null)
let sortableInstance = null
const slideModal = ref(false)
const editingSlide = ref(null)
const slideFileInput = ref(null)
const slideImagePreview = ref(null)

const slideForm = useForm({
  image: null,
  title: '',
  subtitle: '',
  link: '',
  is_active: true,
})

const onSlideImageChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    slideForm.image = file
    const reader = new FileReader()
    reader.onload = (ev) => { slideImagePreview.value = ev.target.result }
    reader.readAsDataURL(file)
  }
}

const openSlideModal = (slide) => {
  editingSlide.value = slide
  slideForm.image = null
  slideForm.title = slide?.title || ''
  slideForm.subtitle = slide?.subtitle || ''
  slideForm.link = slide?.link || ''
  slideForm.is_active = slide?.is_active ?? true
  slideImagePreview.value = null
  slideModal.value = true
}

const submitSlide = () => {
  if (editingSlide.value) {
    slideForm.post(`/admin/frontend/slides/${editingSlide.value.id}`, {
      onSuccess: () => { slideModal.value = false; slideForm.reset() },
      preserveScroll: true,
    })
  } else {
    slideForm.post('/admin/frontend/slides', {
      onSuccess: () => { slideModal.value = false; slideForm.reset() },
      preserveScroll: true,
    })
  }
}

const confirmDeleteSlide = (slide) => {
  if (confirm('هل أنت متأكد؟')) {
    useForm({}).delete(`/admin/frontend/slides/${slide.id}`, { preserveScroll: true })
  }
}

// ---- Testimonials ----
const testimonialModal = ref(false)
const editingTestimonial = ref(null)
const avatarPreview = ref(null)

const testimonialForm = useForm({
  name: '',
  role: '',
  content: '',
  avatar: null,
  is_active: true,
})

const onAvatarChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    testimonialForm.avatar = file
    const reader = new FileReader()
    reader.onload = (ev) => { avatarPreview.value = ev.target.result }
    reader.readAsDataURL(file)
  }
}

const openTestimonialModal = (t) => {
  editingTestimonial.value = t
  testimonialForm.avatar = null
  testimonialForm.name = t?.name || ''
  testimonialForm.role = t?.role || ''
  testimonialForm.content = t?.content || ''
  testimonialForm.is_active = t?.is_active ?? true
  avatarPreview.value = null
  testimonialModal.value = true
}

const submitTestimonial = () => {
  if (editingTestimonial.value) {
    testimonialForm.post(`/admin/frontend/testimonials/${editingTestimonial.value.id}`, {
      onSuccess: () => { testimonialModal.value = false; testimonialForm.reset() },
      preserveScroll: true,
    })
  } else {
    testimonialForm.post('/admin/frontend/testimonials', {
      onSuccess: () => { testimonialModal.value = false; testimonialForm.reset() },
      preserveScroll: true,
    })
  }
}

const confirmDeleteTestimonial = (t) => {
  if (confirm('هل أنت متأكد؟')) {
    useForm({}).delete(`/admin/frontend/testimonials/${t.id}`, { preserveScroll: true })
  }
}

// ---- Features ----
const featureModal = ref(false)
const editingFeature = ref(null)

const featureForm = useForm({
  title: '',
  description: '',
  icon: '',
  is_active: true,
})

const openFeatureModal = (f) => {
  editingFeature.value = f
  featureForm.title = f?.title || ''
  featureForm.description = f?.description || ''
  featureForm.icon = f?.icon || ''
  featureForm.is_active = f?.is_active ?? true
  featureModal.value = true
}

const submitFeature = () => {
  if (editingFeature.value) {
    featureForm.post(`/admin/frontend/features/${editingFeature.value.id}`, {
      onSuccess: () => { featureModal.value = false; featureForm.reset() },
      preserveScroll: true,
    })
  } else {
    featureForm.post('/admin/frontend/features', {
      onSuccess: () => { featureModal.value = false; featureForm.reset() },
      preserveScroll: true,
    })
  }
}

const confirmDeleteFeature = (f) => {
  if (confirm('هل أنت متأكد؟')) {
    useForm({}).delete(`/admin/frontend/features/${f.id}`, { preserveScroll: true })
  }
}

// ---- Steps ----
const stepModal = ref(false)
const editingStep = ref(null)

const stepForm = useForm({
  title: '',
  description: '',
  is_active: true,
})

const openStepModal = (step) => {
  editingStep.value = step
  stepForm.title = step?.title || ''
  stepForm.description = step?.description || ''
  stepForm.is_active = step?.is_active ?? true
  stepModal.value = true
}

const submitStep = () => {
  if (editingStep.value) {
    stepForm.post(`/admin/frontend/steps/${editingStep.value.id}`, {
      onSuccess: () => { stepModal.value = false; stepForm.reset() },
      preserveScroll: true,
    })
  } else {
    stepForm.post('/admin/frontend/steps', {
      onSuccess: () => { stepModal.value = false; stepForm.reset() },
      preserveScroll: true,
    })
  }
}

const confirmDeleteStep = (step) => {
  if (confirm('هل أنت متأكد؟')) {
    useForm({}).delete(`/admin/frontend/steps/${step.id}`, { preserveScroll: true })
  }
}

// ---- Sortable ----
onMounted(() => {
  nextTick(() => {
    if (slidesTbody.value) {
      sortableInstance = new Sortable(slidesTbody.value, {
        handle: '.drag-handle',
        animation: 150,
        easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
        ghostClass: 'bg-primary-100',
        dragClass: 'opacity-80',
        onEnd() {
          const items = [...slidesTbody.value.querySelectorAll('tr[data-id]')].map((tr, i) => ({
            id: parseInt(tr.dataset.id),
            sort_order: i + 1,
          }))
          router.post('/admin/frontend/slides/reorder', { items }, { preserveScroll: true })
        },
      })
    }
  })
})

onUnmounted(() => {
  if (sortableInstance) {
    sortableInstance.destroy()
    sortableInstance = null
  }
})
</script>
