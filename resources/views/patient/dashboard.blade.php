<x-layouts.app :title="'Obat Saya'">
    <x-slot:header>
        <x-page-header title="Jadwal Obat Saya" :subtitle="'Halo, '.$patient->name.' - '.auth()->user()->timezoneAbbr()" />
    </x-slot:header>

    @if(! auth()->user()->password_changed)
        <div class="mb-4 rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            Kata sandi Anda masih menggunakan kata sandi otomatis dari perawat.
            <a href="{{ route('account.profile') }}" class="font-semibold underline">Ganti kata sandi sekarang</a>.
        </div>
    @endif

    @php
        $tzAbbr = auth()->user()->timezoneAbbr();
        $now = \Carbon\CarbonImmutable::now($timezone);
    @endphp

    @if($medicines->isEmpty())
        <x-card>
            <p class="text-sm text-slate-500">Belum ada obat yang dijadwalkan untuk Anda. Silakan hubungi perawat.</p>
        </x-card>
    @else
        <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-slate-500">Daftar Obat</h2>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            @foreach($medicines as $item)
                @php
                    $medicine = $item['medicine'];
                    $doses = $item['doses'];
                    $nextDose = $item['next'];
                @endphp

                <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
                    <div class="border-b border-slate-100 px-5 py-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $medicine->name }}</p>
                                <p class="text-xs text-slate-500">{{ $medicine->dose }} &middot; {{ $medicine->route }} &middot; qty {{ $medicine->quantity }}</p>
                            </div>
                            <a href="{{ route('patient.medicines.history', $medicine) }}" class="text-xs font-medium text-teal-700 hover:underline">Riwayat</a>
                        </div>
                        @if($medicine->notes)
                            <p class="mt-2 rounded-md bg-slate-50 px-3 py-2 text-xs text-slate-600">{{ $medicine->notes }}</p>
                        @endif

                        @if($nextDose)
                            @php
                                $nextAt = $nextDose['scheduled_at']->setTimezone($timezone);
                            @endphp
                            <div class="mt-3 rounded-md border border-teal-200 bg-teal-50 px-3 py-3">
                                <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">Dosis Berikutnya</p>
                                <p class="mt-1 text-sm font-semibold text-slate-900">{{ $medicine->name }}</p>
                                <p class="text-xs text-slate-600">{{ $medicine->dose }} &middot; {{ $medicine->route }}</p>
                                <p class="mt-2 text-xs text-slate-700">
                                    {{ $nextAt->translatedFormat('l, d F Y') }} pukul
                                    <span class="font-semibold">{{ $nextAt->format('H:i') }} {{ $tzAbbr }}</span>
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="px-5 py-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Timeline {{ $medicine->name }} - Tiap {{ $medicine->interval_hours }} jam, {{ $medicine->times_per_day }}x sehari
                        </p>

                        @if($doses->isEmpty())
                            <p class="mt-3 text-xs text-slate-500">Tidak ada dosis terjadwal hari ini.</p>
                        @else
                            <ul class="mt-3 space-y-2">
                                @foreach($doses as $dose)
                                    @php
                                        $status = $dose['status'];
                                        $accent = match($status->color()) {
                                            'green' => 'bg-green-500',
                                            'yellow' => 'bg-yellow-500',
                                            'red' => 'bg-red-500',
                                            'purple' => 'bg-purple-500',
                                            default => 'bg-slate-300',
                                        };
                                        $scheduledLocal = $dose['scheduled_at']->setTimezone($timezone);
                                    @endphp
                                    <li class="flex flex-col gap-2 rounded-md border border-slate-100 px-3 py-2 md:flex-row md:items-center md:justify-between">
                                        <div class="flex items-center gap-3">
                                            <span class="h-2.5 w-2.5 rounded-full {{ $accent }}"></span>
                                            <div>
                                                <p class="text-sm font-medium text-slate-900">
                                                    Dosis ke-{{ $dose['index'] + 1 }} &middot; {{ $scheduledLocal->format('H:i') }} {{ $tzAbbr }}
                                                </p>
                                                @if($status === \App\Enums\IntakeStatus::Upcoming)
                                                    <p class="text-xs text-yellow-700">{{ (int) $now->diffInMinutes($scheduledLocal) }} menit lagi.</p>
                                                @elseif($status === \App\Enums\IntakeStatus::Missed)
                                                    @php
                                                        $totalMinutes = (int) $scheduledLocal->diffInMinutes($now);
                                                        $h = intdiv($totalMinutes, 60);
                                                        $m = $totalMinutes % 60;
                                                    @endphp
                                                    <p class="text-xs text-red-700">Terlewat {{ $h }} jam{{ $m > 0 ? ' '.$m.' menit' : '' }}.</p>
                                                @elseif($status === \App\Enums\IntakeStatus::Taken && $dose['log']?->taken_at)
                                                    <p class="text-xs text-green-700">Diminum pukul {{ $dose['log']->taken_at->setTimezone($timezone)->format('H:i') }} {{ $tzAbbr }}.</p>
                                                @else
                                                    <p class="text-xs text-slate-500">Belum waktunya.</p>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <x-status-badge :status="$status" />
                                            @if($status !== \App\Enums\IntakeStatus::Taken)
                                                <form method="POST" action="{{ route('patient.medicines.mark-taken', $medicine) }}">
                                                    @csrf
                                                    <input type="hidden" name="scheduled_at" value="{{ $dose['scheduled_at']->utc()->format('Y-m-d H:i:s') }}">
                                                    <button type="submit" class="rounded-md bg-teal-600 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-teal-700">
                                                        Sudah Diminum
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-layouts.app>
