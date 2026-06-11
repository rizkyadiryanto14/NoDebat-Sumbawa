<x-layouts.app :title="'Data Pasien'">
    <x-slot:header>
        <x-page-header title="Data Pasien" subtitle="Kelola data pasien dan jadwal obatnya.">
            <x-slot:actions>
                <a href="{{ route('nurse.patients.create') }}" class="inline-flex items-center justify-center rounded-md bg-teal-600 px-4 py-2 text-sm font-medium text-white hover:bg-teal-700">
                    Tambah Pasien
                </a>
            </x-slot:actions>
        </x-page-header>
    </x-slot:header>

    <x-card>
        @if($patients->isEmpty())
            <p class="text-sm text-slate-500">Belum ada pasien terdaftar. Tambahkan pasien baru untuk mulai mendata obat.</p>
        @else
            <div class="hidden md:block">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-500">
                            <th class="py-2.5">Kode</th>
                            <th class="py-2.5">Nama</th>
                            <th class="py-2.5">Gender</th>
                            <th class="py-2.5">Umur</th>
                            <th class="py-2.5">Jumlah Obat</th>
                            <th class="py-2.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($patients as $patient)
                            <tr>
                                <td class="py-3 font-mono text-xs text-slate-700">{{ $patient->patient_code }}</td>
                                <td class="py-3 font-medium text-slate-900">{{ $patient->name }}</td>
                                <td class="py-3 text-slate-600 capitalize">{{ $patient->gender }}</td>
                                <td class="py-3 text-slate-600">{{ $patient->age }} tahun</td>
                                <td class="py-3 text-slate-600">{{ $patient->medicines->count() }}</td>
                                <td class="py-3 text-right">
                                    <a href="{{ route('nurse.patients.show', $patient) }}" class="text-sm font-medium text-teal-700 hover:underline">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <ul class="divide-y divide-slate-100 md:hidden">
                @foreach($patients as $patient)
                    <li class="py-3 first:pt-0 last:pb-0">
                        <a href="{{ route('nurse.patients.show', $patient) }}" class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-medium text-slate-900">{{ $patient->name }}</p>
                                <p class="text-xs text-slate-500">{{ $patient->patient_code }} &middot; {{ ucfirst($patient->gender) }} &middot; {{ $patient->age }} th</p>
                            </div>
                            <span class="text-xs font-medium text-teal-700">Detail</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </x-card>
</x-layouts.app>
