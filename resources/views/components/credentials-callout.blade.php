@props(['credentials'])

<div
    x-data="{ show: false, copied: false, copy(value) { navigator.clipboard.writeText(value); this.copied = true; setTimeout(() => this.copied = false, 1500); } }"
    class="mb-4 rounded-md border border-amber-200 bg-amber-50 px-4 py-4"
>
    <p class="text-sm font-semibold text-amber-900">Kredensial Pasien</p>
    <p class="mt-1 text-xs text-amber-800">Catat atau bagikan kredensial berikut. Kata sandi tidak dapat dilihat kembali setelah halaman dimuat ulang.</p>

    <div class="mt-3 grid gap-2 sm:grid-cols-2">
        <div>
            <p class="text-xs font-medium text-amber-900">Email</p>
            <div class="mt-1 flex items-center gap-2">
                <code class="flex-1 rounded border border-amber-200 bg-white px-2 py-1.5 text-xs text-slate-800">{{ $credentials['email'] }}</code>
                <button type="button" @click="copy('{{ $credentials['email'] }}')" class="rounded border border-amber-200 bg-white px-2 py-1 text-xs font-medium text-amber-900 hover:bg-amber-100">Salin</button>
            </div>
        </div>
        <div>
            <p class="text-xs font-medium text-amber-900">Kata Sandi</p>
            <div class="mt-1 flex items-center gap-2">
                <code class="flex-1 rounded border border-amber-200 bg-white px-2 py-1.5 text-xs text-slate-800">
                    <span x-show="show">{{ $credentials['password'] }}</span>
                    <span x-show="!show">••••••••</span>
                </code>
                <button type="button" @click="show = !show" class="rounded border border-amber-200 bg-white px-2 py-1 text-xs font-medium text-amber-900 hover:bg-amber-100" x-text="show ? 'Sembunyikan' : 'Tampilkan'"></button>
                <button type="button" @click="copy('{{ $credentials['password'] }}')" class="rounded border border-amber-200 bg-white px-2 py-1 text-xs font-medium text-amber-900 hover:bg-amber-100">Salin</button>
            </div>
        </div>
    </div>
    <p x-show="copied" x-transition class="mt-2 text-xs font-medium text-teal-700">Disalin ke clipboard.</p>
</div>
