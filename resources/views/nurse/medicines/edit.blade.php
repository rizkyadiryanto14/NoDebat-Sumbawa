<x-layouts.app :title="'Ubah Obat'">
    <x-slot:header>
        <x-page-header :title="'Ubah Obat: '.$medicine->name" :subtitle="'Pasien '.$patient->name" />
    </x-slot:header>

    <x-card>
        <form method="POST" action="{{ route('nurse.patients.medicines.update', [$patient, $medicine]) }}">
            @csrf
            @method('PUT')

            @include('nurse.medicines._form', ['medicine' => $medicine])

            <div class="mt-6 flex items-center justify-end gap-2">
                <a href="{{ route('nurse.patients.show', $patient) }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Batal</a>
                <x-primary-button>Simpan Perubahan</x-primary-button>
            </div>
        </form>
    </x-card>
</x-layouts.app>
