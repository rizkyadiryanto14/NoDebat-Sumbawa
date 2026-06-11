@props(['status'])

@php
    /** @var \App\Enums\IntakeStatus $status */
    $classes = match ($status->color()) {
        'green' => 'bg-green-100 text-green-700 border-green-200',
        'yellow' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'red' => 'bg-red-100 text-red-700 border-red-200',
        'purple' => 'bg-purple-100 text-purple-700 border-purple-200',
        default => 'bg-slate-100 text-slate-700 border-slate-200',
    };
@endphp

<span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs font-medium {{ $classes }}">
    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
    {{ $status->label() }}
</span>
