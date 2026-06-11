@php
    $user = auth()->user();
    $items = $user?->isNurse()
        ? [
            ['label' => 'Dashboard', 'route' => 'dashboard', 'match' => 'dashboard', 'icon' => 'home'],
            ['label' => 'Pasien', 'route' => 'nurse.patients.index', 'match' => 'nurse.patients.*', 'icon' => 'users'],
            ['label' => 'Akun', 'route' => 'account.profile', 'match' => 'account.profile', 'icon' => 'user'],
        ]
        : [
            ['label' => 'Obat Saya', 'route' => 'patient.dashboard', 'match' => 'patient.dashboard', 'icon' => 'pill'],
            ['label' => 'Akun', 'route' => 'account.profile', 'match' => 'account.profile', 'icon' => 'user'],
        ];

    $columnsClass = match (count($items)) {
        2 => 'grid-cols-2',
        3 => 'grid-cols-3',
        4 => 'grid-cols-4',
        default => 'grid-cols-3',
    };
@endphp

<nav class="fixed inset-x-0 bottom-0 z-40 border-t border-slate-200 bg-white pb-[env(safe-area-inset-bottom)] md:hidden">
    <div class="mx-auto grid w-full max-w-6xl {{ $columnsClass }} px-2 pt-1.5 pb-2">
        @foreach($items as $item)
            @php $active = request()->routeIs($item['match']); @endphp
            <a href="{{ route($item['route']) }}"
                class="flex flex-col items-center justify-center gap-1 rounded-md px-2 py-1.5 text-[11px] font-medium transition {{ $active ? 'text-teal-700' : 'text-slate-500' }}">
                <x-bottom-nav-icon :name="$item['icon']" :active="$active" />
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </div>
</nav>
