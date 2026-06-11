@props(['title' => null, 'subtitle' => null])

<div {{ $attributes->merge(['class' => 'rounded-lg border border-slate-200 bg-white']) }}>
    @if($title || $subtitle)
        <div class="border-b border-slate-200 px-5 py-4">
            @if($title)<h2 class="text-base font-semibold text-slate-900">{{ $title }}</h2>@endif
            @if($subtitle)<p class="mt-0.5 text-xs text-slate-500">{{ $subtitle }}</p>@endif
        </div>
    @endif
    <div class="px-5 py-4">
        {{ $slot }}
    </div>
</div>
