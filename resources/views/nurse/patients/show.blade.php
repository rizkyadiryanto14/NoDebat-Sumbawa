<x-layouts.app :title="$patient->name">
    <x-slot:header>
        <x-page-header :title="$patient->name" :subtitle="'Kode: '.$patient->patient_code">
            <x-slot:actions>
                <a href="{{ route('nurse.patients.medicines.create', $patient) }}" class="inline-flex items-center justify-center rounded-md bg-teal-600 px-4 py-2 text-sm font-medium text-white hover:bg-teal-700">
                    Tambah Obat
                </a>
                <a href="{{ route('nurse.patients.edit', $patient) }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Ubah Pasien
                </a>
            </x-slot:actions>
        </x-page-header>
    </x-slot:header>

    @if($credentials = session('credentials'))
        <x-credentials-callout :credentials="$credentials" />
    @endif

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="lg:col-span-1">
            <x-card title="Identitas Pasien">
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Nama</dt>
                        <dd class="mt-0.5 text-slate-900">{{ $patient->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Kode Pasien</dt>
                        <dd class="mt-0.5 font-mono text-slate-900">{{ $patient->patient_code }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Email Login</dt>
                        <dd class="mt-0.5 text-slate-900">{{ $patient->user->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Tempat, Tanggal Lahir</dt>
                        <dd class="mt-0.5 text-slate-900">{{ $patient->birth_place }}, {{ $patient->birth_date->translatedFormat('d F Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Umur</dt>
                        <dd class="mt-0.5 text-slate-900">{{ $patient->age }} tahun</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Jenis Kelamin</dt>
                        <dd class="mt-0.5 capitalize text-slate-900">{{ $patient->gender }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Alamat</dt>
                        <dd class="mt-0.5 text-slate-900">{{ $patient->address }}</dd>
                    </div>
                </dl>

                <div class="mt-4 flex flex-col gap-2">
                    <form method="POST" action="{{ route('nurse.patients.reset-password', $patient) }}">
                        @csrf
                        <x-secondary-button type="submit" class="w-full" onclick="return confirm('Reset kata sandi pasien ini?')">
                            Reset Kata Sandi
                        </x-secondary-button>
                    </form>
                    <form method="POST" action="{{ route('nurse.patients.destroy', $patient) }}" onsubmit="return confirm('Hapus pasien dan seluruh datanya?')">
                        @csrf
                        @method('DELETE')
                        <x-danger-button class="w-full">Hapus Pasien</x-danger-button>
                    </form>
                </div>
            </x-card>
        </div>

        <div class="lg:col-span-2">
            <x-card title="Daftar Obat" subtitle="Obat yang terjadwal untuk pasien ini.">
                @if($patient->medicines->isEmpty())
                    <div class="rounded-md border border-dashed border-slate-300 px-4 py-6 text-center">
                        <p class="text-sm text-slate-600">Belum ada obat. Tambahkan obat untuk mulai memberi notifikasi pada pasien.</p>
                        <a href="{{ route('nurse.patients.medicines.create', $patient) }}" class="mt-3 inline-flex items-center justify-center rounded-md bg-teal-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-teal-700">
                            Tambah Obat
                        </a>
                    </div>
                @else
                    <ul class="divide-y divide-slate-100">
                        @foreach($patient->medicines as $medicine)
                            <li class="py-3 first:pt-0 last:pb-0">
                                <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">{{ $medicine->name }}</p>
                                        <p class="text-xs text-slate-500">
                                            {{ $medicine->dose }} &middot; {{ $medicine->route }} &middot; qty {{ $medicine->quantity }}
                                        </p>
                                        <p class="mt-1 text-xs text-slate-500">
                                            Tiap {{ $medicine->interval_hours }} jam, {{ $medicine->times_per_day }}x sehari &middot; mulai {{ $medicine->first_dose_at->setTimezone(auth()->user()->timezone)->translatedFormat('d M Y H:i') }} {{ auth()->user()->timezoneAbbr() }}
                                        </p>
                                        @if($medicine->notes)
                                            <p class="mt-1 text-xs text-slate-500">Catatan: {{ $medicine->notes }}</p>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('nurse.patients.medicines.edit', [$patient, $medicine]) }}" class="text-xs font-medium text-teal-700 hover:underline">Ubah</a>
                                        <form method="POST" action="{{ route('nurse.patients.medicines.destroy', [$patient, $medicine]) }}" onsubmit="return confirm('Hapus obat ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-medium text-red-600 hover:underline">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-card>
        </div>
    </div>

    <section id="riwayat-minum-obat" class="mt-4 scroll-mt-24">
        <x-card title="Riwayat Minum Obat" subtitle="Seluruh catatan obat pasien, diurutkan dari jadwal terbaru.">
            @if($intakeLogs->isEmpty())
                <p class="text-sm text-slate-500">Belum ada riwayat minum obat untuk pasien ini.</p>
            @else
                <ul class="divide-y divide-slate-100">
                    @foreach($intakeLogs as $log)
                        @php
                            $status = \App\Enums\IntakeStatus::tryFrom($log->status) ?? \App\Enums\IntakeStatus::Pending;
                            $timezone = $patient->user->timezone;
                            $timezoneAbbr = $patient->user->timezoneAbbr();
                            $scheduledAt = $log->scheduled_at->setTimezone($timezone);
                        @endphp
                        <li class="flex flex-col gap-3 py-4 first:pt-0 last:pb-0 md:flex-row md:items-center md:justify-between">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $log->medicine->name }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">
                                    {{ $log->medicine->dose }} &middot; {{ $log->medicine->route }} &middot;
                                    jadwal {{ $scheduledAt->translatedFormat('l, d F Y') }} pukul {{ $scheduledAt->format('H:i') }} {{ $timezoneAbbr }}
                                </p>
                                @if($log->taken_at)
                                    <p class="mt-1 text-xs text-green-700">
                                        Diminum {{ $log->taken_at->setTimezone($timezone)->translatedFormat('d F Y, H:i') }} {{ $timezoneAbbr }}
                                    </p>
                                @elseif($log->missed_at)
                                    <p class="mt-1 text-xs text-red-700">
                                        Tercatat terlewat {{ $log->missed_at->setTimezone($timezone)->translatedFormat('d F Y, H:i') }} {{ $timezoneAbbr }}
                                    </p>
                                @endif
                            </div>
                            <x-status-badge :status="$status" />
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-card>
    </section>
</x-layouts.app>
