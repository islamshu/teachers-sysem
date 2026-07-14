<template>
  <DashboardLayout>
    <div>
      <div class="flex items-center justify-between mb-8 animate-fade-in-up">
        <div>
          <h1 class="text-3xl font-extrabold text-slate-900">تصفح الموظفين</h1>
          <p class="text-slate-500 mt-1">تصفح الموظفين المتاحين وأرسل دعوات التوظيف</p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <div
          v-for="employee in employeesList"
          :key="employee.id"
          class="card p-5 animate-fade-in-up transition-all duration-300 cursor-pointer hover:shadow-lg hover:-translate-y-0.5"
          @click="openDetail(employee)"
        >
          <div class="flex items-start gap-4 mb-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-100 to-surface-100 overflow-hidden flex-shrink-0">
              <div class="w-full h-full flex items-center justify-center text-xl font-bold text-primary-600">
                {{ employee.name?.charAt(0) }}
              </div>
            </div>
            <div class="flex-1 min-w-0">
              <h3 class="text-lg font-bold text-slate-900 truncate">{{ employee.name }}</h3>
              <p class="text-sm text-slate-500 truncate">{{ employee.email }}</p>
              <div class="flex flex-wrap gap-1.5 mt-1.5">
                <span v-for="role in employee.roles" :key="role.id" class="inline-block px-2 py-0.5 text-xs rounded-lg bg-primary-100 text-primary-700 font-medium">
                  {{ role.name }}
                </span>
              </div>
            </div>
          </div>

          <div class="flex flex-wrap gap-1.5 mb-3">
            <span v-for="branch in employee.branches" :key="branch.id" class="inline-block px-2 py-0.5 text-xs rounded-lg bg-surface-100 text-surface-600 font-medium">
              {{ branch.name }}
            </span>
          </div>

          <button
            @click.stop="openInvite(employee)"
            class="btn-primary w-full justify-center text-sm"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            إرسال دعوة
          </button>
        </div>
      </div>

      <!-- Detail Modal -->
      <Teleport to="body">
        <div v-if="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div class="fixed inset-0 bg-black/30 backdrop-blur-sm" @click="showDetailModal = false"></div>
          <div class="relative bg-white rounded-2xl shadow-2xl p-6 w-full max-w-lg animate-scale-in max-h-[90vh] overflow-y-auto">
            <div class="flex items-center gap-4 mb-6">
              <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary-100 to-surface-100 overflow-hidden flex-shrink-0">
                <div class="w-full h-full flex items-center justify-center text-2xl font-bold text-primary-600">
                  {{ detailEmployee?.name?.charAt(0) }}
                </div>
              </div>
              <div class="flex-1 min-w-0">
                <h3 class="text-xl font-bold text-slate-900">{{ detailEmployee?.name }}</h3>
                <p class="text-sm text-slate-500">{{ detailEmployee?.email }}</p>
              </div>
              <button @click="showDetailModal = false" class="flex-shrink-0 w-8 h-8 rounded-full bg-surface-100 flex items-center justify-center hover:bg-surface-200 transition-colors">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
              </button>
            </div>

            <div class="space-y-3 mb-5 text-sm">
              <div class="flex items-center gap-3 text-slate-700">
                <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                <span>{{ detailEmployee?.email }}</span>
              </div>
            </div>

            <div class="mb-4">
              <p class="text-xs font-bold text-slate-500 mb-2">الأدوار</p>
              <div class="flex flex-wrap gap-1.5">
                <span v-for="role in detailEmployee?.roles" :key="role.id" class="inline-block px-2.5 py-1 text-xs rounded-lg bg-primary-100 text-primary-700 font-medium">
                  {{ role.name }}
                </span>
              </div>
            </div>

            <div v-if="detailEmployee?.branches?.length" class="mb-5">
              <p class="text-xs font-bold text-slate-500 mb-2">الفروع</p>
              <div class="flex flex-wrap gap-1.5">
                <span v-for="branch in detailEmployee?.branches" :key="branch.id" class="inline-block px-2.5 py-1 text-xs rounded-lg bg-surface-100 text-surface-600 font-medium">
                  {{ branch.name }}
                </span>
              </div>
            </div>

            <button
              @click="openInviteFromDetail(detailEmployee)"
              class="btn-primary w-full justify-center text-sm"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              إرسال دعوة
            </button>
          </div>
        </div>
      </Teleport>

      <!-- Loading -->
      <div ref="sentinel" class="flex justify-center py-8">
        <div v-if="loading" class="flex items-center gap-3 text-slate-500">
          <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          <span class="text-sm font-medium">جاري التحميل...</span>
        </div>
        <p v-else-if="employeesList.length === 0" class="text-sm text-slate-400">لا يوجد موظفون متاحون حالياً</p>
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
                <p class="text-sm text-slate-500">إلى {{ selectedEmployee?.name }}</p>
              </div>
            </div>

            <form @submit.prevent="sendInvite" class="space-y-4">
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">رسالة الدعوة</label>
                <textarea
                  v-model="inviteForm.message"
                  rows="3"
                  placeholder="اكتب رسالة ترحيبية للموظف..."
                  class="input-base resize-none"
                ></textarea>
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
import { useForm } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const props = defineProps({
  employees: Array,
  nextPage: Number,
})

const employeesList = ref([...props.employees])
const nextPage = ref(props.nextPage)
const loading = ref(false)
const sentinel = ref(null)
let observer = null

const showInviteModal = ref(false)
const selectedEmployee = ref(null)

const showDetailModal = ref(false)
const detailEmployee = ref(null)

const openDetail = (employee) => {
  detailEmployee.value = employee
  showDetailModal.value = true
}

const openInviteFromDetail = (employee) => {
  showDetailModal.value = false
  openInvite(employee)
}

const inviteForm = useForm({
  employee_id: '',
  message: '',
})

const openInvite = (employee) => {
  selectedEmployee.value = employee
  inviteForm.employee_id = employee.id
  inviteForm.message = ''
  showInviteModal.value = true
}

const sendInvite = () => {
  inviteForm.post('/school/employee-invite', {
    preserveScroll: true,
    onSuccess: () => {
      showInviteModal.value = false
      employeesList.value = employeesList.value.filter(e => e.id !== selectedEmployee.value?.id)
      selectedEmployee.value = null
    },
  })
}

const loadMore = async () => {
  if (loading.value || !nextPage.value) return
  loading.value = true
  try {
    const res = await fetch(`/school/employees/browse?page=${nextPage.value}`, {
      headers: { Accept: 'application/json' },
    })
    const json = await res.json()
    employeesList.value.push(...json.data)
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
