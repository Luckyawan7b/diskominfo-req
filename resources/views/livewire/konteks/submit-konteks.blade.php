<div>
    @if($canSubmit)
        <button wire:click="openModal"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 text-sm font-semibold text-white shadow-lg shadow-blue-500/25 hover:from-blue-700 hover:to-indigo-700 transition-all cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            Serahkan Dokumen ke Admin
        </button>

        {{-- Modal Konfirmasi --}}
        @if($showModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" wire:click="$set('showModal', false)"></div>
                <div class="relative bg-slate-800 border border-slate-700/50 rounded-2xl shadow-2xl w-full max-w-lg p-6 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-white">Konfirmasi Penyerahan Dokumen</h3>
                            <p class="text-xs text-slate-400">Tahun Penilaian: {{ $konteks->tahun_penilaian }}</p>
                        </div>
                    </div>

                    @if(!empty($validationErrors))
                        <div class="p-4 rounded-xl border border-red-500/30 bg-red-950/30 space-y-2">
                            <p class="text-xs font-semibold text-red-400">Dokumen belum siap diserahkan:</p>
                            <ul class="text-xs text-red-300 space-y-1 list-disc list-inside">
                                @foreach($validationErrors as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="p-4 rounded-xl border border-slate-700 bg-slate-900/40 text-xs text-slate-300 leading-relaxed">
                            Setelah diserahkan, dokumen akan berstatus <strong>Menunggu Review (Submitted)</strong> dan Anda tidak dapat mengubah data hingga admin memberikan persetujuan atau catatan revisi.
                        </div>
                    @endif

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 rounded-lg border border-slate-600 text-sm text-slate-300 hover:bg-slate-700 transition-colors cursor-pointer">
                            Tutup
                        </button>
                        @if(empty($validationErrors))
                            <button type="button" wire:click="submitToAdmin" class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-500 text-sm font-semibold text-white transition-colors cursor-pointer">
                                Ya, Serahkan Sekarang
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
