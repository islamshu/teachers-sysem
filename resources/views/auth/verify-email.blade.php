<x-guest-layout>
    <div class="text-center mb-8">
        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-100 to-primary-50 flex items-center justify-center mx-auto mb-4 shadow-lg shadow-primary-500/10">
            <svg class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </div>
        <h1 class="text-2xl font-extrabold text-slate-900">تحقق من بريدك الإلكتروني</h1>
        <p class="text-slate-500 mt-1">شكراً لتسجيلك! قم بتأكيد بريدك الإلكتروني عبر الرابط الذي أرسلناه لك</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-sm font-medium text-emerald-700 mb-6">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
                {{ __('تم إرسال رابط تحقق جديد إلى بريدك الإلكتروني') }}
            </div>
        </div>
    @endif

    <div class="flex items-center justify-between gap-4">
        <form method="POST" action="{{ route('verification.send') }}" class="flex-1">
            @csrf
            <x-primary-button>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                {{ __('إعادة إرسال رابط التحقق') }}
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-ghost px-4 py-2 text-sm">
                {{ __('تسجيل الخروج') }}
            </button>
        </form>
    </div>
</x-guest-layout>
