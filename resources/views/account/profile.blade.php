<x-layouts.app :title="'Akun'">
    <x-slot:header>
        <x-page-header title="Akun Saya" subtitle="Perbarui kata sandi dan zona waktu Anda." />
    </x-slot:header>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <x-card title="Informasi Akun">
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Nama</dt>
                    <dd class="mt-0.5 text-slate-900">{{ $user->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Email</dt>
                    <dd class="mt-0.5 text-slate-900">{{ $user->email }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Peran</dt>
                    <dd class="mt-0.5 text-slate-900 capitalize">{{ $user->role }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Zona Waktu</dt>
                    <dd class="mt-0.5 text-slate-900">{{ \App\Models\User::TIMEZONES[$user->timezone] ?? $user->timezone }}</dd>
                </div>
            </dl>
        </x-card>

        <div class="space-y-4 lg:col-span-2">
            <x-card title="Zona Waktu">
                <p class="mb-3 text-xs text-slate-500">Mempengaruhi tampilan jam dosis dan jadwal pengingat obat.</p>
                <form method="POST" action="{{ route('account.timezone') }}" class="flex flex-col gap-3 md:flex-row md:items-end">
                    @csrf
                    @method('PUT')
                    <div class="flex-1">
                        <x-input-label for="timezone" value="Pilih Zona Waktu" />
                        <x-select-input id="timezone" name="timezone">
                            @foreach(\App\Models\User::TIMEZONES as $tz => $label)
                                <option value="{{ $tz }}" @selected($user->timezone === $tz)>{{ $label }}</option>
                            @endforeach
                        </x-select-input>
                        <x-input-error :messages="$errors->get('timezone')" />
                    </div>
                    <x-primary-button>Simpan Zona Waktu</x-primary-button>
                </form>
            </x-card>

            <x-card title="Ubah Kata Sandi">
                @if($errors->updatePassword->any())
                    <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                        <ul class="list-inside list-disc space-y-1">
                            @foreach($errors->updatePassword->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" class="space-y-4" x-data="{ show: false }">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="current_password" value="Kata Sandi Saat Ini" />
                        <x-text-input id="current_password" name="current_password" required x-bind:type="show ? 'text' : 'password'" />
                    </div>

                    <div>
                        <x-input-label for="password" value="Kata Sandi Baru" />
                        <x-text-input id="password" name="password" required x-bind:type="show ? 'text' : 'password'" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" value="Konfirmasi Kata Sandi" />
                        <x-text-input id="password_confirmation" name="password_confirmation" required x-bind:type="show ? 'text' : 'password'" />
                    </div>

                    <label class="flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" x-model="show" class="h-4 w-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                        Tampilkan kata sandi
                    </label>

                    <div class="flex justify-end">
                        <x-primary-button>Simpan Kata Sandi</x-primary-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-layouts.app>
