<template>
  <DashboardLayout>
    <div>
      <div class="flex items-center justify-between mb-8 animate-fade-in-up">
        <div>
          <h1 class="text-3xl font-extrabold text-slate-900">إدارة مرفقات العقود</h1>
          <p class="text-slate-500 mt-1">إضافة وتعديل وحذف المرفقات المطلوبة في عقود الباصات</p>
        </div>
        <button @click="openAddModal" class="btn-primary">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          إضافة مرفق
        </button>
      </div>

      <Alert v-if="flash.message" variant="success" :title="flash.message" closeable class="mb-6 animate-fade-in-down" />

      <div class="card overflow-hidden animate-fade-in-up animate-delay-100">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-surface-50 border-b border-surface-200">
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">#</th>
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">اسم المرفق</th>
                <th class="px-6 py-4 text-center text-sm font-bold text-slate-700">إجباري</th>
                <th class="px-6 py-4 text-center text-sm font-bold text-slate-700">مفعل</th>
                <th class="px-6 py-4 text-center text-sm font-bold text-slate-700">الإجراءات</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-surface-100">
              <tr v-for="(attachment, index) in attachments" :key="attachment.id" class="hover:bg-primary-50/50 transition-colors">
                <td class="px-6 py-4 text-slate-500 font-medium">{{ index + 1 }}</td>
                <td class="px-6 py-4 font-medium text-slate-900">{{ attachment.name }}</td>
                <td class="px-6 py-4 text-center">
                  <button
                    @click="toggleRequired(attachment)"
                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                    :class="attachment.is_required ? 'bg-primary-600' : 'bg-slate-300'"
                    role="switch"
                    :aria-checked="attachment.is_required"
                  >
                    <span
                      class="inline-block h-4 w-4 transform rounded-full bg-white shadow-sm transition-transform duration-200"
                      :class="attachment.is_required ? 'ltr:translate-x-6 rtl:-translate-x-6' : 'ltr:translate-x-1 rtl:-translate-x-1'"
                    />
                  </button>
                </td>
                <td class="px-6 py-4 text-center">
                  <button
                    @click="toggleAttachment(attachment)"
                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                    :class="attachment.is_enabled ? 'bg-primary-600' : 'bg-slate-300'"
                    role="switch"
                    :aria-checked="attachment.is_enabled"
                  >
                    <span
                      class="inline-block h-4 w-4 transform rounded-full bg-white shadow-sm transition-transform duration-200"
                      :class="attachment.is_enabled ? 'ltr:translate-x-6 rtl:-translate-x-6' : 'ltr:translate-x-1 rtl:-translate-x-1'"
                    />
                  </button>
                </td>
                <td class="px-6 py-4">
                  <div class="flex gap-2 justify-center">
                    <button @click="openEditModal(attachment)" class="p-2 rounded-xl bg-primary-100 text-primary-700 hover:bg-primary-200 transition-all duration-200" title="تعديل">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                    </button>
                    <button @click="confirmDelete(attachment)" class="p-2 rounded-xl bg-red-100 text-red-700 hover:bg-red-200 transition-all duration-200" title="حذف">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="attachments.length === 0">
                <td colspan="5" class="px-6 py-12 text-center text-slate-500">لا توجد مرفقات مضافة بعد</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <Teleport to="body">
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="closeModal">
          <div class="fixed inset-0 bg-black/30 backdrop-blur-sm" @click="closeModal"></div>
          <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 animate-scale-in">
            <div class="flex items-center gap-3 mb-6">
              <div class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                </svg>
              </div>
              <h3 class="text-xl font-bold text-slate-900">{{ editingAttachment ? 'تعديل المرفق' : 'إضافة مرفق جديد' }}</h3>
            </div>

            <form @submit.prevent="submitForm">
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">اسم المرفق <span class="text-red-500">*</span></label>
                <input
                  v-model="form.name"
                  type="text"
                  class="input-base"
                  placeholder="أدخل اسم المرفق"
                  required
                />
              </div>

              <div class="flex items-center gap-3 mt-5">
                <input
                  v-model="form.is_required"
                  type="checkbox"
                  :true-value="1"
                  :false-value="0"
                  id="is_required"
                  class="w-4 h-4 rounded border-surface-300 text-primary-600 focus:ring-primary-500"
                />
                <label for="is_required" class="text-sm font-medium text-slate-700">مرفق إجباري</label>
              </div>

              <div class="flex gap-3 mt-8">
                <button type="submit" class="btn-primary flex-1" :disabled="form.processing">
                  {{ form.processing ? 'جاري الحفظ...' : (editingAttachment ? 'تحديث' : 'إضافة') }}
                </button>
                <button type="button" @click="closeModal" class="btn-ghost px-6">إلغاء</button>
              </div>
            </form>
          </div>
        </div>
      </Teleport>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, usePage, router } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import Alert from '@/Components/Alert.vue'
import Swal from 'sweetalert2'

const props = defineProps({
  attachments: Array,
})

const page = usePage()
const flash = page.props.flash || {}

const showModal = ref(false)
const editingAttachment = ref(null)

const form = useForm({
  name: '',
  is_required: 0,
})

const openAddModal = () => {
  editingAttachment.value = null
  form.name = ''
  form.is_required = 0
  showModal.value = true
}

const openEditModal = (attachment) => {
  editingAttachment.value = attachment
  form.name = attachment.name
  form.is_required = attachment.is_required ? 1 : 0
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  form.reset()
}

const submitForm = () => {
  if (editingAttachment.value) {
    form.put(`/admin/contract-attachments/${editingAttachment.value.id}`, {
      onSuccess: () => closeModal(),
    })
  } else {
    form.post('/admin/contract-attachments', {
      onSuccess: () => closeModal(),
    })
  }
}

const toggleAttachment = (attachment) => {
  router.post(`/admin/contract-attachments/${attachment.id}/toggle`, {}, {
    preserveScroll: true,
  })
}

const toggleRequired = (attachment) => {
  router.put(`/admin/contract-attachments/${attachment.id}`, {
    name: attachment.name,
    is_enabled: attachment.is_enabled,
    is_required: !attachment.is_required,
  }, {
    preserveScroll: true,
  })
}

const confirmDelete = (attachment) => {
  Swal.fire({
    title: 'تأكيد الحذف',
    text: `هل أنت متأكد من حذف "${attachment.name}"؟`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#64748b',
    confirmButtonText: 'نعم، حذف',
    cancelButtonText: 'إلغاء',
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(`/admin/contract-attachments/${attachment.id}`, {
        preserveScroll: true,
      })
    }
  })
}
</script>
