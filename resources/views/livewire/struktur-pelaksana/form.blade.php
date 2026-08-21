<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Struktur Pelaksana — {{ $konteks->tahun_penilaian }}</h1>
            <p class="text-sm text-slate-400 mt-1">Formulir 3: Pemilik, koordinator, dan pengelola risiko</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('sasaran.form', $konteks) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-slate-600 text-sm text-slate-300 hover:bg-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
                Sasaran
            </a>
            @if($isEditable)
                <button wire:click="save" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-gradient-to-r from-emerald-500 to-teal-600 text-sm font-semibold text-white shadow-lg shadow-emerald-500/25 hover:from-emerald-600 hover:to-teal-700 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan
                </button>
            @endif
            <a href="{{ route('risiko.index', $konteks) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-slate-600 text-sm text-slate-300 hover:bg-slate-700 transition-colors">
                Daftar Risiko
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
        </div>
    </div>

    <div class="rounded-xl border border-slate-700/50 bg-slate-800/50 p-6 sm:p-8 space-y-6 max-w-2xl">
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1.5">Pemilik Risiko</label>
            <input wire:model="pemilik_risiko" type="text" {{ !$isEditable ? 'disabled' : '' }}
                class="w-full rounded-lg border border-slate-600 bg-slate-700/50 px-4 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1.5">Koordinator Risiko</label>
            <input wire:model="koordinator_risiko" type="text" {{ !$isEditable ? 'disabled' : '' }}
                class="w-full rounded-lg border border-slate-600 bg-slate-700/50 px-4 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1.5">Pengelola Risiko</label>
            <textarea wire:model="pengelola_risiko" rows="4" {{ !$isEditable ? 'disabled' : '' }}
                class="w-full rounded-lg border border-slate-600 bg-slate-700/50 px-4 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed resize-y"
                placeholder="Satu nama per baris"></textarea>
        </div>
    </div>
</div>
