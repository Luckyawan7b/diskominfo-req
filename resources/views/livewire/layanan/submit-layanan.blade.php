<div>
    @if($isSubmitted)
        {{-- Sudah disubmit / approved --}}
        <div class="flex items-center gap-2 px-4 py-2.5 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-sm text-emerald-400 font-medium">
            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            Layanan Telah Dikunci
        </div>
    @else
        {{-- Belum disubmit --}}
        <button wire:click="openModal" type="button"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-gradient-to-r from-amber-500 to-orange-600 text-sm font-semibold text-white shadow-lg shadow-amber-500/25 hover:from-amber-600 hover:to-orange-700 transition-all cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Kunci & Selesaikan Layanan
        </button>
    @endif

    {{-- Modal Konfirmasi --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" wire:click="$set('showModal', false)"></div>
            <div class="relative bg-slate-800 border border-slate-700/50 rounded-2xl shadow-2xl w-full max-w-lg p-6 space-y-5">

                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-white">Kunci & Selesaikan Layanan</h3>
                        <p class="text-sm text-slate-400 mt-0.5">Setelah dikunci, data layanan ini tidak dapat diedit kembali.</p>
                    </div>
                </div>

                {{-- Validasi error jika ada --}}
                @if(! empty($validationErrors))
                    <div class="rounded-lg border border-red-500/30 bg-red-500/10 p-4 space-y-1">
                        <p class="text-xs font-semibold text-red-400 mb-2">Lengkapi data berikut sebelum melanjutkan:</p>
                        @foreach($validationErrors as $err)
                            <div class="flex items-start gap-1.5 text-xs text-red-300">
                                <svg class="w-3.5 h-3.5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                {{ $err }}
                            </div>
                        @endforeach
                    </div>
                @else
                    {{-- Info progres --}}
                    <div class="rounded-lg border border-slate-700/50 bg-slate-700/30 p-4 space-y-2">
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Status Modul</p>
                        @php
                            $modulStatus = [
                                ['label' => 'Manajemen Risiko',         'done' => (bool) $mrKonteks, 'note' => null],
                                ['label' => 'Manajemen Pengetahuan',    'done' => false, 'note' => 'Segera hadir'],
                                ['label' => 'Manajemen Perubahan',      'done' => false, 'note' => 'Segera hadir'],
                                ['label' => 'Manajemen Keberlangsungan','done' => false, 'note' => 'Segera hadir'],
                                ['label' => 'Manajemen Relasi',         'done' => false, 'note' => 'Segera hadir'],
                            ];
                        @endphp
                        @foreach($modulStatus as $m)
                            <div class="flex items-center gap-2 text-xs">
                                @if($m['done'])
                                    <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    <span class="text-emerald-300">{{ $m['label'] }}</span>
                                @elseif($m['note'])
                                    <svg class="w-4 h-4 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="text-slate-600">{{ $m['label'] }}</span>
                                    <span class="text-slate-700 italic">({{ $m['note'] }})</span>
                                @else
                                    <svg class="w-4 h-4 text-red-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="text-red-300">{{ $m['label'] }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="flex justify-end gap-3 pt-1">
                    <button type="button" wire:click="$set('showModal', false)"
                        class="px-4 py-2 rounded-lg border border-slate-600 text-sm text-slate-300 hover:bg-slate-700 cursor-pointer transition-colors">
                        Batal
                    </button>
                    @if(empty($validationErrors))
                        <button wire:click="submitLayanan" type="button"
                            class="px-4 py-2.5 rounded-lg bg-gradient-to-r from-amber-500 to-orange-600 text-sm font-semibold text-white shadow-lg shadow-amber-500/25 hover:from-amber-600 hover:to-orange-700 transition-all cursor-pointer">
                            Ya, Kunci Layanan
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
