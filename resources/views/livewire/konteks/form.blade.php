<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Penetapan Konteks — {{ $konteks->tahun_penilaian }}</h1>
            <p class="text-sm text-slate-400 mt-1">Formulir 1 & 4: Identitas instansi dan selera risiko</p>
        </div>
        <div class="flex gap-3">
            @if($isEditable)
                <button wire:click="save" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-gradient-to-r from-emerald-500 to-teal-600 text-sm font-semibold text-white shadow-lg shadow-emerald-500/25 hover:from-emerald-600 hover:to-teal-700 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Draft
                </button>
            @endif
            <a href="{{ route('sasaran.form', $konteks) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-slate-600 text-sm text-slate-300 hover:bg-slate-700 transition-colors">
                Sasaran UPR
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
        </div>
    </div>

    {{-- Wizard steps --}}
    <div class="flex items-center gap-2 mb-8 overflow-x-auto pb-2">
        @php
            $steps = [
                ['label' => 'Konteks', 'route' => route('konteks.form', $konteks), 'active' => true],
                ['label' => 'Sasaran', 'route' => route('sasaran.form', $konteks), 'active' => false],
                ['label' => 'Struktur', 'route' => route('struktur.form', $konteks), 'active' => false],
                ['label' => 'Risiko', 'route' => route('risiko.index', $konteks), 'active' => false],
                ['label' => 'Peta Risiko', 'route' => route('risiko.peta', $konteks), 'active' => false],
            ];
        @endphp
        @foreach($steps as $i => $step)
            @if($i > 0)
                <div class="w-8 h-px bg-slate-700 shrink-0"></div>
            @endif
            <a href="{{ $step['route'] }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium whitespace-nowrap transition-colors {{ $step['active'] ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' : 'text-slate-400 hover:text-slate-200 border border-slate-700 hover:border-slate-600' }}">
                <span class="w-5 h-5 rounded-full {{ $step['active'] ? 'bg-emerald-500 text-white' : 'bg-slate-700 text-slate-400' }} flex items-center justify-center text-xs font-bold">{{ $i + 1 }}</span>
                {{ $step['label'] }}
            </a>
        @endforeach
    </div>

    {{-- Form --}}
    <div class="rounded-xl border border-slate-700/50 bg-slate-800/50 p-6 sm:p-8 space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Nama Instansi --}}
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1.5">Nama Instansi <span class="text-red-400">*</span></label>
                <input wire:model="nama_instansi" type="text" {{ !$isEditable ? 'disabled' : '' }}
                    class="w-full rounded-lg border border-slate-600 bg-slate-700/50 px-4 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    placeholder="Nama instansi/desa">
                @error('nama_instansi') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>

            {{-- Nama UPR --}}
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1.5">Nama UPR <span class="text-red-400">*</span></label>
                <input wire:model="nama_upr" type="text" {{ !$isEditable ? 'disabled' : '' }}
                    class="w-full rounded-lg border border-slate-600 bg-slate-700/50 px-4 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    placeholder="Unit Pemilik Risiko">
                @error('nama_upr') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Tugas UPR --}}
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1.5">Tugas UPR</label>
            <textarea wire:model="tugas_upr" rows="3" {{ !$isEditable ? 'disabled' : '' }}
                class="w-full rounded-lg border border-slate-600 bg-slate-700/50 px-4 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed resize-y"
                placeholder="Tugas pokok UPR"></textarea>
        </div>

        {{-- Fungsi UPR --}}
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1.5">Fungsi UPR</label>
            <textarea wire:model="fungsi_upr" rows="3" {{ !$isEditable ? 'disabled' : '' }}
                class="w-full rounded-lg border border-slate-600 bg-slate-700/50 px-4 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed resize-y"
                placeholder="Fungsi UPR"></textarea>
        </div>

        {{-- Selera Risiko --}}
        <div class="border-t border-slate-700/50 pt-6">
            <label class="block text-sm font-medium text-slate-300 mb-3">
                Selera Risiko (Formulir 4)
                <span class="text-slate-500 font-normal ml-1">— batas besaran risiko yang dapat diterima</span>
            </label>
            <div class="flex items-center gap-6">
                <input wire:model.live="selera_risiko" type="range" min="1" max="25" {{ !$isEditable ? 'disabled' : '' }}
                    class="flex-1 h-2 rounded-full appearance-none bg-slate-700 accent-emerald-500 disabled:opacity-50">
                <div class="text-center min-w-[80px]">
                    <span class="text-3xl font-bold text-white">{{ $selera_risiko }}</span>
                    <p class="text-xs mt-0.5 font-medium
                        {{ match($riskLabel) {
                            'Rendah'        => 'text-emerald-400',
                            'Sedang'        => 'text-amber-400',
                            'Tinggi'        => 'text-orange-400',
                            'Sangat Tinggi' => 'text-red-400',
                            default         => 'text-slate-400',
                        } }}">{{ $riskLabel }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
