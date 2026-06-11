@props(['medicine' => null])

<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
        <x-input-label for="name" value="Nama Obat" />
        <x-text-input id="name" name="name" :value="old('name', $medicine?->name)" required />
        <x-input-error :messages="$errors->get('name')" />
    </div>

    <div>
        <x-input-label for="dose" value="Dosis Obat" />
        <x-text-input id="dose" name="dose" :value="old('dose', $medicine?->dose)" placeholder="contoh: 500 mg" required />
        <x-input-error :messages="$errors->get('dose')" />
    </div>

    <div>
        <x-input-label for="route" value="Jenis / Rute" />
        <x-select-input id="route" name="route" required>
            <option value="">Pilih rute</option>
            @foreach(['Oral', 'Injeksi', 'Topikal', 'Inhalasi', 'Sublingual', 'Rektal'] as $option)
                <option value="{{ $option }}" @selected(old('route', $medicine?->route) === $option)>{{ $option }}</option>
            @endforeach
        </x-select-input>
        <x-input-error :messages="$errors->get('route')" />
    </div>

    <div>
        <x-input-label for="interval_hours" value="Rentang Waktu (jam)" />
        <x-text-input id="interval_hours" type="number" min="1" max="24" name="interval_hours" :value="old('interval_hours', $medicine?->interval_hours)" placeholder="contoh: 6" required />
        <p class="mt-1 text-xs text-slate-500">Jeda antar dosis dalam jam. Contoh isi 6 berarti tiap 6 jam sekali.</p>
        <x-input-error :messages="$errors->get('interval_hours')" />
    </div>

    <div>
        <x-input-label for="times_per_day" value="Frekuensi (kali per hari)" />
        <x-text-input id="times_per_day" type="number" min="1" max="10" name="times_per_day" :value="old('times_per_day', $medicine?->times_per_day)" placeholder="contoh: 2" required />
        <p class="mt-1 text-xs text-slate-500">Banyaknya dosis per hari. Sistem otomatis menjadwalkan setiap dosis.</p>
        <x-input-error :messages="$errors->get('times_per_day')" />
    </div>

    <div>
        <x-input-label for="quantity" value="Qty" />
        <x-text-input id="quantity" type="number" min="1" max="99" name="quantity" :value="old('quantity', $medicine?->quantity)" required />
        <x-input-error :messages="$errors->get('quantity')" />
    </div>
</div>

<div class="mt-4">
    <x-input-label for="notes" value="Keterangan" />
    <x-textarea-input id="notes" name="notes" rows="3">{{ old('notes', $medicine?->notes) }}</x-textarea-input>
    <x-input-error :messages="$errors->get('notes')" />
</div>
