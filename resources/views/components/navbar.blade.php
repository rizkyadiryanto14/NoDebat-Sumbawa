@php
    $user = auth()->user();
    $isNurse = $user?->isNurse();
    $links = $isNurse
        ? [
            ['label' => 'Dashboard', 'route' => 'dashboard'],
            ['label' => 'Pasien', 'route' => 'nurse.patients.index'],
            ['label' => 'Akun', 'route' => 'account.profile'],
        ]
        : [
            ['label' => 'Obat Saya', 'route' => 'patient.dashboard'],
            ['label' => 'Akun', 'route' => 'account.profile'],
        ];
@endphp

<header class="hidden border-b border-slate-200 bg-white md:block">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-3">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <span class="flex h-8 w-8 items-center justify-center rounded-md bg-teal-600 text-sm font-bold text-white">N</span>
            <div>
                <p class="text-sm font-semibold leading-tight text-slate-900">NoDebat</p>
                <p class="text-xs leading-tight text-slate-500">Manajemen Obat Pasien</p>
            </div>
        </a>

        <nav class="flex items-center gap-1">
            @foreach($links as $link)
                @php
                    $active = request()->routeIs($link['route']) || ($link['route'] === 'nurse.patients.index' && request()->routeIs('nurse.patients.*'));
                @endphp
                <a href="{{ route($link['route']) }}"
                    class="rounded-md px-3 py-2 text-sm font-medium transition {{ $active ? 'bg-teal-50 text-teal-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach

            <form method="POST" action="{{ route('logout') }}" class="ml-2">
                @csrf
                <button type="submit" class="rounded-md border border-slate-200 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50">
                    Keluar
                </button>
            </form>
        </nav>
    </div>
</header>

<header class="border-b border-slate-200 bg-white md:hidden">
    <div class="flex items-center justify-between px-4 py-3">
        <div class="flex items-center gap-2">
            <span class="flex h-8 w-8 items-center justify-center rounded-md bg-teal-600 text-sm font-bold text-white">N</span>
            <div>
                <p class="text-sm font-semibold leading-tight text-slate-900">NoDebat</p>
                <p class="text-xs leading-tight text-slate-500">{{ $user?->name }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="rounded-md border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600">
                Keluar
            </button>
        </form>
    </div>
</header>
