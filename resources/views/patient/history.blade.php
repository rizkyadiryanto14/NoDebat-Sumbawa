<x-layouts.app :title="'Riwayat Obat'">
    <x-slot:header>
        <x-page-header :title="'Riwayat: '.$medicine->name" :subtitle="$medicine->dose.' - tiap '.$medicine->interval_hours.' jam, '.$medicine->times_per_day.'x sehari'">
            <x-slot:actions>
                <a href="{{ route('patient.dashboard') }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Kembali
                </a>
            </x-slot:actions>
        </x-page-header>
    </x-slot:header>

    @php
        $tzAbbr = auth()->user()->timezoneAbbr();
    @endphp

    <x-card>
        @if($logs->isEmpty())
            <p class="text-sm text-slate-500">Belum ada riwayat untuk obat ini.</p>
        @else
            <ul class="divide-y divide-slate-100">
                @foreach($logs as $log)
                    @php
                        $status = \App\Enums\IntakeStatus::tryFrom($log->status) ?? \App\Enums\IntakeStatus::Pending;
                        $scheduledLocal = $log->scheduled_at->setTimezone($timezone);
                    @endphp
                    <li class="flex flex-col gap-2 py-3 first:pt-0 last:pb-0 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-900">
                                {{ $scheduledLocal->translatedFormat('l, d F Y') }} pukul {{ $scheduledLocal->format('H:i') }} {{ $tzAbbr }}
                            </p>
                            @if($log->taken_at)
                                <p class="text-xs text-green-700">Diminum pukul {{ $log->taken_at->setTimezone($timezone)->format('H:i') }} {{ $tzAbbr }} ({{ $log->taken_at->setTimezone($timezone)->translatedFormat('d M Y') }})</p>
                            @elseif($log->missed_at)
                                <p class="text-xs text-red-700">Tercatat terlewat pada {{ $log->missed_at->setTimezone($timezone)->format('H:i') }} {{ $tzAbbr }} ({{ $log->missed_at->setTimezone($timezone)->translatedFormat('d M Y') }})</p>
                            @endif
                        </div>
                        <x-status-badge :status="$status" />
                    </li>
                @endforeach
            </ul>
        @endif
    </x-card>
</x-layouts.app>
