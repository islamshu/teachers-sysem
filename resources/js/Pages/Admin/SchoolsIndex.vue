<template>
  <DashboardLayout>
    <div>
      <!-- Header -->
      <div class="relative mb-8 overflow-hidden rounded-3xl bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 shadow-2xl shadow-primary-500/20 animate-fade-in-up">
        <div class="absolute inset-0 bg-grid opacity-20"></div>
        <div class="relative px-8 py-10">
          <div class="flex items-center gap-4 mb-3">
            <div class="w-14 h-14 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center text-3xl shadow-lg">
              🏫
            </div>
            <div>
              <h1 class="text-3xl font-extrabold text-white">إدارة المدارس</h1>
              <p class="text-primary-200">مراجعة وقبول أو رفض طلبات المدارس</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Status Tabs -->
      <div class="flex flex-wrap gap-2 mb-6 animate-fade-in-up animate-delay-100">
        <Link
          href="/admin/schools"
          class="px-5 py-2.5 rounded-xl font-bold text-sm transition-all duration-200"
          :class="!currentStatus ? 'bg-primary-500 text-white shadow-md shadow-primary-500/20' : 'bg-white text-slate-600 hover:bg-surface-100 border border-surface-200'"
        >
          الكل ({{ allCount }})
        </Link>
        <Link
          href="/admin/schools?status=pending"
          class="px-5 py-2.5 rounded-xl font-bold text-sm transition-all duration-200"
          :class="currentStatus === 'pending' ? 'bg-amber-500 text-white shadow-md shadow-amber-500/20' : 'bg-white text-slate-600 hover:bg-surface-100 border border-surface-200'"
        >
          قيد المراجعة
        </Link>
        <Link
          href="/admin/schools?status=approved"
          class="px-5 py-2.5 rounded-xl font-bold text-sm transition-all duration-200"
          :class="currentStatus === 'approved' ? 'bg-emerald-500 text-white shadow-md shadow-emerald-500/20' : 'bg-white text-slate-600 hover:bg-surface-100 border border-surface-200'"
        >
          المقبولة
        </Link>
        <Link
          href="/admin/schools?status=rejected"
          class="px-5 py-2.5 rounded-xl font-bold text-sm transition-all duration-200"
          :class="currentStatus === 'rejected' ? 'bg-red-500 text-white shadow-md shadow-red-500/20' : 'bg-white text-slate-600 hover:bg-surface-100 border border-surface-200'"
        >
          المرفوضة
        </Link>
      </div>

      <!-- Schools List -->
      <div class="card animate-fade-in-up animate-delay-200">
        <div v-if="schools.length > 0" class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-surface-50 border-b border-surface-200">
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">المدرسة</th>
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">السجل التجاري</th>
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">المدير</th>
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">الهاتف</th>
                <th class="px-6 py-4 text-right text-sm font-bold text-slate-700">الحالة</th>
                <th class="px-6 py-4 text-center text-sm font-bold text-slate-700">الإجراءات</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-surface-100">
              <tr
                v-for="school in schools"
                :key="school.id"
                class="hover:bg-primary-50/50 transition-colors duration-150"
              >
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary-100 to-surface-100 overflow-hidden flex-shrink-0">
                      <img v-if="school.logo" :src="`/storage/${school.logo}`" :alt="school.school_name" class="w-full h-full object-cover" />
                      <div v-else class="w-full h-full flex items-center justify-center text-sm">🏫</div>
                    </div>
                    <div>
                      <p class="font-bold text-slate-900 text-sm">{{ school.school_name }}</p>
                      <p class="text-xs text-slate-500">{{ school.user?.email }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 text-sm text-slate-900 font-medium">{{ school.commercial_register }}</td>
                <td class="px-6 py-4 text-sm text-slate-600">{{ school.principal_name || '-' }}</td>
                <td class="px-6 py-4 text-sm text-slate-600">{{ school.phone || '-' }}</td>
                <td class="px-6 py-4">
                  <span
                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold"
                    :class="{
                      'bg-emerald-100 text-emerald-700': school.status === 'approved',
                      'bg-amber-100 text-amber-700': school.status === 'pending',
                      'bg-red-100 text-red-700': school.status === 'rejected',
                    }"
                  >
                    <span
                      class="w-1.5 h-1.5 rounded-full"
                      :class="{
                        'bg-emerald-500': school.status === 'approved',
                        'bg-amber-500': school.status === 'pending',
                        'bg-red-500': school.status === 'rejected',
                      }"
                    ></span>
                    {{ school.status === 'approved' ? 'مقبولة' : school.status === 'pending' ? 'قيد المراجعة' : 'مرفوضة' }}
                  </span>
                </td>
                <td class="px-6 py-4 text-center">
                  <div class="flex items-center justify-center gap-2">
                    <button
                      v-if="school.status === 'pending'"
                      @click="approveSchool(school.id)"
                      class="btn-primary px-3 py-1.5 text-xs"
                    >
                      قبول
                    </button>
                    <button
                      v-if="school.status === 'pending'"
                      @click="rejectSchool(school.id)"
                      class="btn-ghost px-3 py-1.5 text-xs text-red-600 hover:bg-red-50"
                    >
                      رفض
                    </button>
                    <button
                      @click="deleteSchool(school.id)"
                      class="p-2 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 transition-all duration-200"
                      title="حذف"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-else class="p-10 text-center">
          <div class="w-16 h-16 rounded-2xl bg-surface-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
          </div>
          <h3 class="text-lg font-bold text-slate-900 mb-1">لا توجد مدارس</h3>
          <p class="text-sm text-slate-500">لم يتم العثور على مدارس في هذا القسم</p>
        </div>

        <!-- Pagination -->
        <div ref="sentinel" class="flex justify-center py-6">
          <div v-if="loading" class="flex items-center gap-3 text-slate-500">
            <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <span class="text-sm font-medium">جاري التحميل...</span>
          </div>
          <p v-else-if="!nextPage && schools.length > 0" class="text-sm text-slate-400">لا يوجد المزيد</p>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import Swal from 'sweetalert2'

const props = defineProps({
  schools: Array,
  nextPage: Number,
  currentStatus: String,
})

const schoolsList = ref([...props.schools])
const nextPage = ref(props.nextPage)
const loading = ref(false)
const sentinel = ref(null)
let observer = null

const allCount = computed(() => schoolsList.value.length)

const loadMore = async () => {
  if (loading.value || !nextPage.value) return
  loading.value = true

  try {
    const params = new URLSearchParams()
    if (props.currentStatus) params.append('status', props.currentStatus)
    params.append('page', nextPage.value)

    const res = await fetch(`/admin/schools?${params.toString()}`, {
      headers: { Accept: 'application/json' },
    })
    const json = await res.json()
    schoolsList.value.push(...json.data)
    nextPage.value = json.current_page < json.last_page ? json.current_page + 1 : null
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const approveSchool = (id) => {
  Swal.fire({
    icon: 'question',
    title: 'تأكيد القبول',
    text: 'هل تريد قبول هذه المدرسة؟',
    showCancelButton: true,
    confirmButtonText: 'قبول',
    cancelButtonText: 'إلغاء',
    confirmButtonColor: '#10b981',
  }).then((result) => {
    if (result.isConfirmed) {
      router.patch(`/admin/schools/${id}/approve`, {}, {
        preserveScroll: true,
        onSuccess: () => {
          const school = schoolsList.value.find(s => s.id === id)
          if (school) school.status = 'approved'
        },
      })
    }
  })
}

const rejectSchool = (id) => {
  Swal.fire({
    icon: 'question',
    title: 'تأكيد الرفض',
    text: 'هل تريد رفض هذه المدرسة؟',
    showCancelButton: true,
    confirmButtonText: 'رفض',
    cancelButtonText: 'إلغاء',
    confirmButtonColor: '#ef4444',
  }).then((result) => {
    if (result.isConfirmed) {
      router.patch(`/admin/schools/${id}/reject`, {}, {
        preserveScroll: true,
        onSuccess: () => {
          const school = schoolsList.value.find(s => s.id === id)
          if (school) school.status = 'rejected'
        },
      })
    }
  })
}

const deleteSchool = (id) => {
  Swal.fire({
    icon: 'warning',
    title: 'حذف المدرسة',
    text: 'سيتم حذف جميع بيانات المدرسة والمستخدم المرتبط بها نهائياً. لا يمكن التراجع عن هذا الإجراء.',
    showCancelButton: true,
    confirmButtonText: 'نعم، احذف',
    cancelButtonText: 'إلغاء',
    confirmButtonColor: '#ef4444',
  }).then((result) => {
    if (result.isConfirmed) {
      useForm({}).delete(`/admin/schools/${id}`, {
        onSuccess: () => location.reload()
      })
    }
  })
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
