<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Sasaran UPR — {{ $konteks->tahun_penilaian }}</h1>
            <p class="text-sm text-slate-400 mt-1">Formulir 2: Sasaran, indikator, dan target kinerja</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('konteks.form', $konteks) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-slate-600 text-sm text-slate-300 hover:bg-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
                Konteks
            </a>
            <a href="{{ route('struktur.form', $konteks) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-slate-600 text-sm text-slate-300 hover:bg-slate-700 transition-colors">
                Struktur Pelaksana
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
        </div>
    </div>

    <div class="rounded-xl border border-slate-700/50 bg-slate-800/50 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-700/50">
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase w-8">#</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase">Sasaran UPR</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase">Indikator Kinerja</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase">Target Kinerja</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase">Sasaran Pembangunan Nasional</th>
                    @if($isEditable)
                        <th class="px-4 py-3 w-24"></th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/30">
                @forelse($rows as $i => $row)
                    <tr class="hover:bg-slate-700/20">
                        <td class="px-4 py-3 text-slate-500">{{ $i + 1 }}</td>
                        <td class="px-4 py-2">
                            <textarea wire:model.blur="rows.{{ $i }}.sasaran_upr" rows="2" {{ !$isEditable ? 'disabled' : '' }}
                                class="w-full rounded-lg border border-slate-600 bg-slate-700/30 px-3 py-2 text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none disabled:opacity-50 resize-y"></textarea>
                        </td>
                        <td class="px-4 py-2">
                            <textarea wire:model.blur="rows.{{ $i }}.indikator_kinerja" rows="2" {{ !$isEditable ? 'disabled' : '' }}
                                class="w-full rounded-lg border border-slate-600 bg-slate-700/30 px-3 py-2 text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none disabled:opacity-50 resize-y"></textarea>
                        </td>
                        <td class="px-4 py-2">
                            <textarea wire:model.blur="rows.{{ $i }}.target_kinerja" rows="2" {{ !$isEditable ? 'disabled' : '' }}
                                class="w-full rounded-lg border border-slate-600 bg-slate-700/30 px-3 py-2 text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none disabled:opacity-50 resize-y"></textarea>
                        </td>
                        <td class="px-4 py-2">
                            <textarea wire:model.blur="rows.{{ $i }}.sasaran_pembangunan_nasional" rows="2" {{ !$isEditable ? 'disabled' : '' }}
                                class="w-full rounded-lg border border-slate-600 bg-slate-700/30 px-3 py-2 text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none disabled:opacity-50 resize-y"></textarea>
                        </td>
                        @if($isEditable)
                            <td class="px-4 py-2">
                                <div class="flex gap-1">
                                    <button wire:click="saveRow({{ $i }})" class="p-2 rounded-lg text-emerald-400 hover:bg-emerald-500/10 transition-colors cursor-pointer" title="Simpan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                    <button wire:click="deleteRow({{ $i }})" wire:confirm="Hapus baris sasaran ini?" class="p-2 rounded-lg text-red-400 hover:bg-red-500/10 transition-colors cursor-pointer" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $isEditable ? 6 : 5 }}" class="px-4 py-8 text-center text-slate-500 text-sm">Belum ada sasaran. Klik "Tambah Baris" untuk memulai.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($isEditable)
        <button wire:click="addRow" class="mt-4 inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-dashed border-slate-600 text-sm text-slate-400 hover:text-emerald-400 hover:border-emerald-500/50 transition-colors cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Baris
        </button>
    @endif
</div>
