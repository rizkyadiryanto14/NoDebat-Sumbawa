@props(['patient' => null])

@php
    $isEdit = $patient !== null;
@endphp

<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <x-input-label for="name" value="Nama Pasien" />
        <x-text-input id="name" name="name" :value="old('name', $patient?->name)" required />
        <x-input-error :messages="$errors->get('name')" />
    </div>

    <div>
        <x-input-label for="email" :value="'Email '.($isEdit ? '' : '(opsional)')" />
        <x-text-input id="email" type="email" name="email" :value="old('email', $patient?->user?->email)" placeholder="Akan dibuat otomatis bila kosong" />
        <x-input-error :messages="$errors->get('email')" />
    </div>

    <div>
        <x-input-label for="birth_place" value="Tempat Lahir" />
        <x-text-input id="birth_place" name="birth_place" :value="old('birth_place', $patient?->birth_place)" required />
        <x-input-error :messages="$errors->get('birth_place')" />
    </div>

    <div>
        <x-input-label for="birth_date" value="Tanggal Lahir" />
        <x-text-input id="birth_date" type="date" name="birth_date" :value="old('birth_date', $patient?->birth_date?->format('Y-m-d'))" required />
        <x-input-error :messages="$errors->get('birth_date')" />
    </div>

    <div>
        <x-input-label for="gender" value="Jenis Kelamin" />
        <x-select-input id="gender" name="gender" required>
            <option value="">Pilih jenis kelamin</option>
            <option value="laki-laki" @selected(old('gender', $patient?->gender) === 'laki-laki')>Laki - Laki</option>
            <option value="perempuan" @selected(old('gender', $patient?->gender) === 'perempuan')>Perempuan</option>
        </x-select-input>
        <x-input-error :messages="$errors->get('gender')" />
    </div>
</div>

<div class="mt-4">
    <x-input-label for="address" value="Alamat" />
    <x-textarea-input id="address" name="address" rows="3" required>{{ old('address', $patient?->address) }}</x-textarea-input>
    <x-input-error :messages="$errors->get('address')" />
</div>
