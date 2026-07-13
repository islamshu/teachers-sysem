<template>
  <DashboardLayout>
    <div>
      <button @click="goBack" class="btn-ghost mb-6 animate-fade-in-up">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        العودة
      </button>

      <div class="relative mb-8 overflow-hidden rounded-3xl bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 shadow-2xl shadow-primary-500/20 animate-fade-in-up">
        <div class="absolute inset-0 bg-grid opacity-20"></div>
        <div class="relative px-8 py-10">
          <div class="flex items-center gap-4 mb-3">
            <div class="w-14 h-14 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center text-3xl shadow-lg">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
            </div>
            <div>
              <h1 class="text-2xl font-bold text-white">إضافة رصيد</h1>
              <p class="text-white/70 text-sm">إضافة رصيد إلى حساب مستخدم</p>
            </div>
          </div>
        </div>
      </div>

      <div class="max-w-2xl mx-auto animate-fade-in-up animate-delay-100">
        <form @submit.prevent="submit" class="card p-8">
          <div class="space-y-6">
            <div ref="select2Ref">
              <label class="block text-sm font-bold text-slate-700 mb-1.5">اختر المستخدم <span class="text-red-500">*</span></label>

              <!-- Trigger -->
              <button
                type="button"
                @click="open = !open"
                class="w-full flex items-center justify-between gap-3 px-4 py-3 bg-white border-2 rounded-xl text-right transition-all duration-200"
                :class="open ? 'border-primary-500 shadow-lg shadow-primary-500/10' : 'border-surface-200 hover:border-surface-300'"
              >
                <template v-if="selectedUser">
                  <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                      {{ selectedUser.name?.charAt(0) }}
                    </div>
                    <div class="min-w-0 text-right">
                      <p class="text-sm font-bold text-slate-900 truncate">{{ selectedUser.name }}</p>
                      <p class="text-xs text-slate-500 truncate">{{ selectedUser.email }}</p>
                    </div>
                  </div>
                </template>
                <template v-else>
                  <span class="text-sm text-slate-400">اختر مستخدم...</span>
                </template>
                <svg class="w-5 h-5 text-slate-400 flex-shrink-0 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>

              <!-- Dropdown -->
              <Transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0 translate-y-1"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 translate-y-1"
              >
                <div v-if="open" class="absolute z-30 mt-2 w-full bg-white rounded-2xl shadow-2xl border border-surface-200 overflow-hidden">
                  <!-- Search -->
                  <div class="p-3 border-b border-surface-100">
                    <div class="relative">
                      <input
                        ref="searchInput"
                        v-model="searchQuery"
                        type="text"
                        class="w-full px-4 py-2.5 pr-10 bg-surface-50 border border-surface-200 rounded-xl text-sm focus:outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-500/10 transition-all"
                        placeholder="ابحث بالاسم أو البريد..."
                      />
                      <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                      </svg>
                    </div>
                  </div>

                  <!-- Options -->
                  <div class="max-h-60 overflow-y-auto overscroll-contain">
                    <button
                      v-for="user in filteredUsers"
                      :key="user.id"
                      @click="selectUser(user)"
                      type="button"
                      class="w-full flex items-center gap-3 px-4 py-3 text-right transition-colors duration-100"
                      :class="form.user_id == user.id ? 'bg-primary-50' : 'hover:bg-surface-50'"
                    >
                      <div class="w-9 h-9 rounded-lg flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                           :class="form.user_id == user.id ? 'bg-primary-500' : 'bg-gradient-to-br from-slate-400 to-slate-500'">
                        {{ user.name?.charAt(0) }}
                      </div>
                      <div class="min-w-0 flex-1 text-right">
                        <p class="text-sm font-bold truncate" :class="form.user_id == user.id ? 'text-primary-700' : 'text-slate-900'">{{ user.name }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ user.email }}</p>
                      </div>
                      <span class="flex-shrink-0 inline-flex px-2 py-0.5 rounded-md text-[10px] font-bold"
                            :class="form.user_id == user.id ? 'bg-primary-100 text-primary-700' : 'bg-surface-100 text-slate-500'">
                        {{ roleLabel(user.role) }}
                      </span>
                      <svg v-if="form.user_id == user.id" class="w-4 h-4 text-primary-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                      </svg>
                    </button>

                    <div v-if="filteredUsers.length === 0" class="px-4 py-6 text-center">
                      <svg class="w-10 h-10 text-surface-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                      </svg>
                      <p class="text-sm text-slate-500">لا توجد نتائج</p>
                    </div>
                  </div>
                </div>
              </Transition>

              <p v-if="form.errors.user_id" class="mt-1 text-xs text-red-500 font-semibold">{{ form.errors.user_id }}</p>
            </div>

            <div v-if="selectedUser" class="bg-surface-50 rounded-2xl p-6 border border-surface-200">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-slate-500 mb-1">الرصيد الحالي</p>
                  <p class="text-3xl font-bold" :class="currentBalance > 0 ? 'text-emerald-600' : 'text-slate-400'">
                    {{ Number(currentBalance).toLocaleString('ar') }} <span class="text-sm font-normal">ريال</span>
                  </p>
                </div>
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center" :class="currentBalance > 0 ? 'bg-emerald-100' : 'bg-surface-200'">
                  <svg class="w-8 h-8" :class="currentBalance > 0 ? 'text-emerald-500' : 'text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
              </div>
            </div>

            <div v-if="selectedUser">
              <label class="block text-sm font-bold text-slate-700 mb-1.5">المبلغ المطلوب إضافته <span class="text-red-500">*</span></label>
              <div class="relative">
                <input
                  v-model="form.amount"
                  type="number"
                  step="0.01"
                  min="0.01"
                  class="input-base pl-16"
                  placeholder="0.00"
                />
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400">ريال</span>
              </div>
              <p v-if="form.errors.amount" class="mt-1 text-xs text-red-500 font-semibold">{{ form.errors.amount }}</p>
            </div>

            <div v-if="selectedUser">
              <label class="block text-sm font-bold text-slate-700 mb-1.5">الوصف (اختياري)</label>
              <textarea
                v-model="form.description"
                class="input-base min-h-[80px]"
                placeholder="سبب إضافة الرصيد..."
              ></textarea>
              <p v-if="form.errors.description" class="mt-1 text-xs text-red-500 font-semibold">{{ form.errors.description }}</p>
            </div>

            <div v-if="selectedUser && form.amount > 0" class="bg-primary-50 rounded-2xl p-6 border border-primary-200">
              <p class="text-sm text-primary-700 mb-2 font-bold">ملخص العملية</p>
              <div class="space-y-2">
                <div class="flex justify-between text-sm">
                  <span class="text-slate-600">الرصيد الحالي:</span>
                  <span class="font-bold text-slate-900">{{ Number(currentBalance).toLocaleString('ar') }} ريال</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-slate-600">المبلغ المضاف:</span>
                  <span class="font-bold text-emerald-600">+{{ Number(form.amount).toLocaleString('ar') }} ريال</span>
                </div>
                <div class="border-t border-primary-200 pt-2 flex justify-between">
                  <span class="text-sm font-bold text-slate-900">الرصيد الجديد:</span>
                  <span class="text-lg font-bold text-primary-700">{{ Number(newBalance).toLocaleString('ar') }} ريال</span>
                </div>
              </div>
            </div>
          </div>

          <div class="flex items-center gap-3 mt-8 pt-6 border-t border-surface-200">
            <button
              type="button"
              @click="confirmAndSubmit"
              :disabled="form.processing || !form.user_id || !form.amount"
              class="btn-primary flex-1"
            >
              <svg v-if="form.processing" class="w-4 h-4 animate-spin inline ml-1" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
              </svg>
              {{ form.processing ? 'جاري الإرسال...' : 'إضافة الرصيد' }}
            </button>
            <Link href="/admin/balances" class="btn-ghost flex-1">إلغاء</Link>
          </div>
        </form>
      </div>

      <!-- Confirmation Modal -->
      <Teleport to="body">
        <div v-if="showConfirm" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showConfirm = false"></div>
          <div class="relative bg-white rounded-3xl shadow-2xl max-w-md w-full p-8 animate-scale-in">
            <div class="text-center">
              <div class="w-16 h-16 rounded-2xl bg-primary-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <h3 class="text-lg font-bold text-slate-900 mb-2">تأكيد إضافة الرصيد</h3>
              <p class="text-sm text-slate-600 mb-1">
                هل أنت متأكد من إضافة
                <span class="font-bold text-primary-600">{{ Number(form.amount).toLocaleString('ar') }} ريال</span>
                إلى حساب
                <span class="font-bold text-slate-900">{{ selectedUser?.name }}</span>
                ؟
              </p>
              <p v-if="form.description" class="text-xs text-slate-500 mt-2">الوصف: {{ form.description }}</p>
            </div>
            <div class="flex gap-3 mt-6">
              <button @click="showConfirm = false" class="btn-ghost flex-1">إلغاء</button>
              <button @click="doSubmit" :disabled="form.processing" class="btn-primary flex-1">
                {{ form.processing ? 'جاري...' : 'تأكيد' }}
              </button>
            </div>
          </div>
        </div>
      </Teleport>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const props = defineProps({
  users: Array,
})

const page = usePage()
const showConfirm = ref(false)
const searchQuery = ref('')
const open = ref(false)
const select2Ref = ref(null)
const searchInput = ref(null)

const form = useForm({
  user_id: '',
  amount: '',
  description: '',
})

const selectedUser = ref(null)
const currentBalance = ref(0)

const filteredUsers = computed(() => {
  if (!searchQuery.value) return props.users
  const q = searchQuery.value.toLowerCase()
  return props.users.filter(u =>
    u.name?.toLowerCase().includes(q) ||
    u.email?.toLowerCase().includes(q)
  )
})

function roleLabel(role) {
  const labels = { admin: 'مدير', teacher: 'مدرس', school: 'مدرسة', employee: 'موظف' }
  return labels[role] || role
}

function selectUser(user) {
  form.user_id = user.id
  selectedUser.value = user
  open.value = false
  searchQuery.value = ''
  fetch(`/admin/balances/user/${user.id}`)
    .then(res => res.json())
    .then(data => { currentBalance.value = data.balance })
}

watch(open, (val) => {
  if (val) {
    nextTick(() => searchInput.value?.focus())
  }
})

function handleClickOutside(e) {
  if (select2Ref.value && !select2Ref.value.contains(e.target)) {
    open.value = false
    searchQuery.value = ''
  }
}

const newBalance = computed(() => {
  return (currentBalance.value || 0) + (Number(form.amount) || 0)
})

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  const urlParams = new URLSearchParams(window.location.search)
  const userId = urlParams.get('user_id')
  if (userId) {
    const user = props.users.find(u => u.id === Number(userId))
    if (user) selectUser(user)
  }
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

function confirmAndSubmit() {
  if (!form.user_id || !form.amount) return
  showConfirm.value = true
}

function doSubmit() {
  form.post('/admin/balances', {
    onSuccess: () => {
      showConfirm.value = false
    },
  })
}

function goBack() {
  window.history.back()
}
</script>
