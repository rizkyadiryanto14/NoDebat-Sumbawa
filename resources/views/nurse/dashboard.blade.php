<x-layouts.app :title="'Dashboard'">
    <x-slot:header>
        <x-page-header
            title="Dashboard Perawat"
            subtitle="Ringkasan data pasien dan status minum obat hari ini."
        >
            <x-slot:actions>
                <a href="{{ route('nurse.patients.create') }}" class="inline-flex items-center justify-center rounded-md bg-teal-600 px-4 py-2 text-sm font-medium text-white hover:bg-teal-700">
                    Tambah Pasien
                </a>
            </x-slot:actions>
        </x-page-header>
    </x-slot:header>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <x-stat-card label="Jumlah Pasien" :value="$totalPatients" hint="Total seluruh pasien terdaftar" />
        <x-stat-card label="Pasien Laki - Laki" :value="$malePatients" />
        <x-stat-card label="Pasien Perempuan" :value="$femalePatients" />
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-2">
        <x-card title="Mendekati Waktu Minum Obat" subtitle="Notifikasi kuning - kurang dari 1 jam menjelang dosis.">
            @if($upcomingAlerts->isEmpty())
                <p class="text-sm text-slate-500">Tidak ada pasien yang mendekati waktu minum obat.</p>
            @else
                <ul class="divide-y divide-slate-100">
                    @foreach($upcomingAlerts as $alert)
                        <li class="flex items-center justify-between gap-3 py-3 first:pt-0 last:pb-0">
                            <div>
                                <p class="text-sm font-medium text-slate-900">{{ $alert['patient']->name }}</p>
                                <p class="text-xs text-slate-500">
                                    {{ $alert['medicine']->name }} &middot; jadwal pukul {{ $alert['scheduled_at']->setTimezone(auth()->user()->timezone)->format('H:i') }} {{ auth()->user()->timezoneAbbr() }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-semibold text-yellow-700">{{ $alert['minutes_until_dose'] }} menit lagi</p>
                                <a href="{{ route('nurse.patients.show', $alert['patient']) }}" class="text-xs font-medium text-teal-700 hover:underline">Lihat detail</a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-card>

        <x-card title="Sudah Melewati Waktu" subtitle="Notifikasi merah - pasien belum minum dan sudah lewat jadwal dosis.">
            @if($missedAlerts->isEmpty())
                <p class="text-sm text-slate-500">Tidak ada pasien yang melewatkan jadwal minum obat hari ini.</p>
            @else
                <ul class="divide-y divide-slate-100">
                    @foreach($missedAlerts as $alert)
                        @php
                            $hours = intdiv($alert['minutes_overdue'], 60);
                            $minutes = $alert['minutes_overdue'] % 60;
                        @endphp
                        <li class="flex items-center justify-between gap-3 py-3 first:pt-0 last:pb-0">
                            <div>
                                <p class="text-sm font-medium text-slate-900">{{ $alert['patient']->name }}</p>
                                <p class="text-xs text-slate-500">
                                    {{ $alert['medicine']->name }} &middot; jadwal pukul {{ $alert['scheduled_at']->setTimezone(auth()->user()->timezone)->format('H:i') }} {{ auth()->user()->timezoneAbbr() }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-semibold text-red-700">
                                    Lewat {{ $hours }} jam{{ $minutes > 0 ? ' '.$minutes.' menit' : '' }}
                                </p>
                                <a href="{{ route('nurse.patients.show', $alert['patient']) }}" class="text-xs font-medium text-teal-700 hover:underline">Lihat detail</a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-card>
    </div>

    <x-card
        class="mt-4"
        title="Riwayat Minum Obat Terbaru"
        subtitle="Dua catatan terbaru. Pilih catatan untuk membuka riwayat lengkap pasien."
    >
        @if($recentIntakeLogs->isEmpty())
            <p class="text-sm text-slate-500">Belum ada riwayat minum obat yang tercatat.</p>
        @else
            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                @foreach($recentIntakeLogs as $log)
                    @php
                        $status = \App\Enums\IntakeStatus::tryFrom($log->status) ?? \App\Enums\IntakeStatus::Pending;
                        $patient = $log->medicine->patient;
                        $timezone = $patient->user->timezone;
                        $timezoneAbbr = $patient->user->timezoneAbbr();
                        $scheduledAt = $log->scheduled_at->setTimezone($timezone);
                    @endphp
                    <a
                        href="{{ route('nurse.patients.show', $patient) }}#riwayat-minum-obat"
                        class="group rounded-md border border-slate-200 px-4 py-3 transition hover:border-teal-300 hover:bg-teal-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-600"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-slate-900 group-hover:text-teal-800">{{ $patient->name }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">
                                    {{ $log->medicine->name }} &middot; {{ $log->medicine->dose }} &middot; {{ $log->medicine->route }}
                                </p>
                            </div>
                            <x-status-badge :status="$status" />
                        </div>
                        <div class="mt-3 flex items-end justify-between gap-3">
                            <p class="text-xs text-slate-600">
                                {{ $scheduledAt->translatedFormat('d F Y, H:i') }} {{ $timezoneAbbr }}
                            </p>
                            <span class="text-xs font-semibold text-teal-700 group-hover:underline">Buka riwayat</span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </x-card>
</x-layouts.app>
