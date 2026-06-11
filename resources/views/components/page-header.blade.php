@props(['title', 'subtitle' => null])

<div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
    <div>
        <h1 class="text-xl font-semibold text-slate-900 md:text-2xl">{{ $title }}</h1>
        @if($subtitle)
            <p class="mt-1 text-sm text-slate-500">{{ $subtitle }}</p>
        @endif
    </div>
    @if(isset($actions))
        <div class="flex flex-wrap items-center gap-2">
            {{ $actions }}
        </div>
    @endif
</div>
