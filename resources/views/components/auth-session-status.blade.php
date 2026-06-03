@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-sm font-medium text-emerald-700']) }}>
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
            </svg>
            {{ $status }}
        </div>
    </div>
@endif
