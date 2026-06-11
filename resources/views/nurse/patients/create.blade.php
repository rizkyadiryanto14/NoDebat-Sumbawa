<x-layouts.app :title="'Tambah Pasien'">
    <x-slot:header>
        <x-page-header title="Tambah Pasien Baru" subtitle="Isi data pasien lalu lanjutkan menambahkan obat." />
    </x-slot:header>

    <x-card>
        <form method="POST" action="{{ route('nurse.patients.store') }}">
            @csrf

            @include('nurse.patients._form')

            <div class="mt-6 flex items-center justify-end gap-2">
                <a href="{{ route('nurse.patients.index') }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Batal</a>
                <x-primary-button>Simpan dan Tambah Obat</x-primary-button>
            </div>
        </form>
    </x-card>
</x-layouts.app>
