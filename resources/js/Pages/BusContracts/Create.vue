<template>
  <MainLayout>
    <div class="max-w-2xl mx-auto py-8 animate-fade-in-up">
      <div class="flex items-center gap-3 mb-8">
        <div class="w-12 h-12 rounded-2xl bg-primary-100 flex items-center justify-center">
          <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
          </svg>
        </div>
        <div>
          <h1 class="text-2xl font-extrabold text-slate-900">عقد باص</h1>
          <p class="text-sm text-slate-500">تعبئة بيانات عقد الباص المدرسي</p>
        </div>
      </div>

      <form @submit.prevent="submit" class="card p-6 space-y-5">
        <div>
          <label class="block text-sm font-bold text-slate-700 mb-1.5">اسم المالك <span class="text-red-500">*</span></label>
          <input v-model="form.owner_name" type="text" class="input-base" placeholder="اسم المالك" required />
          <p v-if="form.errors.owner_name" class="text-red-500 text-xs mt-1">{{ form.errors.owner_name }}</p>
        </div>

        <div>
          <label class="block text-sm font-bold text-slate-700 mb-1.5">هاتف المالك</label>
          <input v-model="form.owner_phone" type="text" class="input-base" placeholder="هاتف المالك" dir="ltr" />
          <p v-if="form.errors.owner_phone" class="text-red-500 text-xs mt-1">{{ form.errors.owner_phone }}</p>
        </div>

        <div class="flex items-center gap-3">
          <input v-model="form.same_driver" type="checkbox" id="same_driver" class="w-4 h-4 rounded border-surface-300 text-primary-600 focus:ring-primary-500" />
          <label for="same_driver" class="text-sm font-medium text-slate-700">السائق هو نفس المالك</label>
        </div>

        <template v-if="!form.same_driver">
          <div>
            <label class="block text-sm font-bold text-slate-700 mb-1.5">اسم السائق</label>
            <input v-model="form.driver_name" type="text" class="input-base" placeholder="اسم السائق" />
            <p v-if="form.errors.driver_name" class="text-red-500 text-xs mt-1">{{ form.errors.driver_name }}</p>
          </div>

          <div>
            <label class="block text-sm font-bold text-slate-700 mb-1.5">هاتف السائق</label>
            <input v-model="form.driver_phone" type="text" class="input-base" placeholder="هاتف السائق" dir="ltr" />
            <p v-if="form.errors.driver_phone" class="text-red-500 text-xs mt-1">{{ form.errors.driver_phone }}</p>
          </div>
        </template>

        <div>
          <label class="block text-sm font-bold text-slate-700 mb-1.5">رقم الرخصة <span class="text-red-500">*</span></label>
          <input v-model="form.license" type="text" class="input-base" placeholder="رقم رخصة الباص" required />
          <p v-if="form.errors.license" class="text-red-500 text-xs mt-1">{{ form.errors.license }}</p>
        </div>

        <div>
          <label class="block text-sm font-bold text-slate-700 mb-1.5">لون الباص <span class="text-red-500">*</span></label>
          <div class="flex items-center gap-3">
            <input v-model="form.van_color" type="text" class="input-base flex-1" dir="ltr" placeholder="أصفر" />
          </div>
          <p v-if="form.errors.van_color" class="text-red-500 text-xs mt-1">{{ form.errors.van_color }}</p>
        </div>

        <div>
          <label class="block text-sm font-bold text-slate-700 mb-1.5">عدد الركاب <span class="text-red-500">*</span></label>
          <input v-model="form.seats_count" type="number" min="1" class="input-base" placeholder="عدد الركاب" required />
          <p v-if="form.errors.seats_count" class="text-red-500 text-xs mt-1">{{ form.errors.seats_count }}</p>
        </div>

        <div>
          <label class="block text-sm font-bold text-slate-700 mb-1.5">الموقع <span class="text-red-500">*</span></label>
          <input v-model="form.location" type="text" class="input-base" placeholder="الموقع" required />
          <p v-if="form.errors.location" class="text-red-500 text-xs mt-1">{{ form.errors.location }}</p>
        </div>

        <template v-if="attachments.length > 0">
          <div class="border-t border-surface-200 pt-5">
            <h3 class="text-sm font-bold text-slate-700 mb-4">المرفقات</h3>
            <div class="space-y-4">
              <div v-for="attachment in attachments" :key="attachment.id">
                <label class="block text-sm font-bold text-slate-700 mb-1.5">
                  {{ attachment.name }}
                  <span v-if="attachment.is_required" class="text-red-500">*</span>
                </label>
                <input
                  type="file"
                  accept=".jpg,.jpeg,.png,.gif,.webp,.pdf"
                  :ref="el => { if (el) fileInputs[attachment.id] = el }"
                  @change="e => handleFileSelect(attachment.id, e)"
                  class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 transition-all duration-200 cursor-pointer"
                />
                <div v-if="previews[attachment.id]" class="mt-2">
                  <img
                    v-if="previews[attachment.id].isImage"
                    :src="previews[attachment.id].url"
                    class="w-32 h-32 rounded-xl object-cover border border-surface-200 shadow-sm"
                  />
                  <div
                    v-else
                    class="flex items-center gap-2 px-3 py-2 bg-surface-50 rounded-xl text-sm text-slate-700"
                  >
                    <svg class="w-5 h-5 text-primary-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span class="font-medium">{{ previews[attachment.id].name }}</span>
                  </div>
                </div>
                <p v-if="form.errors[`attachments.${attachment.id}`]" class="text-red-500 text-xs mt-1">{{ form.errors[`attachments.${attachment.id}`] }}</p>
              </div>
            </div>
          </div>
        </template>

        <div class="flex gap-3 pt-2">
          <button type="submit" :disabled="processing" class="btn-primary flex-1 justify-center">
            {{ processing ? 'جاري الحفظ...' : 'حفظ العقد' }}
          </button>
          <Link href="/" class="btn-ghost px-6">إلغاء</Link>
        </div>
      </form>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useForm, Link, router } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import Swal from 'sweetalert2'

const props = defineProps({
  attachments: Array,
})

const fileInputs = {}
const previews = reactive({})
const processing = ref(false)

const form = useForm({
  owner_name: '',
  same_driver: true,
  owner_phone: '',
  driver_name: '',
  driver_phone: '',
  license: '',
  van_color: '',
  seats_count: '',
  location: '',
})

const attachmentFiles = {}

function handleFileSelect(attachmentId, event) {
  const file = event.target.files[0]
  if (file) {
    attachmentFiles[attachmentId] = file
    const isImage = file.type.startsWith('image/')
    previews[attachmentId] = {
      isImage,
      name: file.name,
      url: isImage ? URL.createObjectURL(file) : null,
    }
  } else {
    delete attachmentFiles[attachmentId]
    delete previews[attachmentId]
  }
}

const submit = () => {
  const formData = new FormData()
  formData.append('owner_name', form.owner_name)
  formData.append('same_driver', form.same_driver ? '1' : '0')
  formData.append('owner_phone', form.owner_phone)
  formData.append('driver_name', form.driver_name)
  formData.append('driver_phone', form.driver_phone)
  formData.append('license', form.license)
  formData.append('van_color', form.van_color)
  formData.append('seats_count', form.seats_count)
  formData.append('location', form.location)

  for (const [attachmentId, file] of Object.entries(attachmentFiles)) {
    formData.append(`attachments[${attachmentId}]`, file)
  }

  processing.value = true

  router.post('/bus-contracts', formData, {
    forceFormData: true,
    onSuccess: () => {
      processing.value = false
      form.reset()
      for (const key in attachmentFiles) delete attachmentFiles[key]
      for (const key in previews) delete previews[key]
      for (const key in fileInputs) {
        if (fileInputs[key]) fileInputs[key].value = ''
      }
      Swal.fire({
        icon: 'success',
        title: 'تم بنجاح',
        text: 'تم إضافة عقد الباص بنجاح',
        confirmButtonColor: '#059669',
        confirmButtonText: 'حسناً',
      })
    },
    onError: (errors) => {
      processing.value = false
      form.errors = errors
    },
  })
}
</script>
