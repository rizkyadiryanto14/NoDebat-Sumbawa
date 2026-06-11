<x-layouts.guest>
    <div class="rounded-lg border border-slate-200 bg-white px-6 py-7">
        <h2 class="text-lg font-semibold text-slate-900">Masuk ke Akun Anda</h2>
        <p class="mt-1 text-xs text-slate-500">Gunakan kredensial yang diberikan oleh perawat.</p>

        <form method="POST" action="{{ route('login') }}" class="mt-5 space-y-4" x-data="{ show: false }">
            @csrf

            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus />
                <x-input-error :messages="$errors->get('email')" />
            </div>

            <div>
                <x-input-label for="password" value="Kata Sandi" />
                <div class="flex gap-2">
                    <x-text-input id="password" name="password" required x-bind:type="show ? 'text' : 'password'" />
                    <button type="button" @click="show = !show" class="rounded-md border border-slate-300 px-3 text-xs font-medium text-slate-700 hover:bg-slate-50" x-text="show ? 'Sembunyikan' : 'Tampilkan'"></button>
                </div>
                <x-input-error :messages="$errors->get('password')" />
            </div>

            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                Ingat saya
            </label>

            <x-primary-button class="w-full">Masuk</x-primary-button>
        </form>
    </div>
</x-layouts.guest>
