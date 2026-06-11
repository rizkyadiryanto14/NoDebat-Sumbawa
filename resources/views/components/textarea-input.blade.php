@props([])

<textarea {{ $attributes->merge([
    'class' => 'w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500',
    'rows' => '3',
]) }}>{{ $slot }}</textarea>
