<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Daftar Pengetahuan</h1>
            <p class="text-sm text-slate-400 mt-1">{{ $konteks->dinas->nama_dinas }} (Tahun {{ $konteks->tahun_penilaian }})</p>
        </div>
        <a href="{{ route('mpn.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-slate-600 text-sm text-slate-300 hover:bg-slate-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
            Kembali
        </a>
    </div>

    {{-- Alert --}}
    @if (session('success'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- List --}}
    <div class="space-y-4">
        @forelse($pengetahuanList as $p)
            @php
                $isSudah = $p->status_dokumentasi === 'sudah';
                $hasPengumpulan = $p->pengumpulan !== null;
            @endphp
            <div class="rounded-xl border border-slate-700/50 bg-slate-800/40 p-5 flex flex-col sm:flex-row gap-4 hover:border-slate-600 transition-colors">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="font-mono text-xs font-semibold px-2.5 py-1 rounded-md bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                            {{ $p->kode_pengetahuan }}
                        </span>
                        @if($isSudah)
                            <span class="text-[10px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Sudah Terdokumentasi</span>
                        @else
                            <span class="text-[10px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20">Belum Terdokumentasi</span>
                        @endif
                    </div>
                    <h3 class="text-base font-semibold text-white mb-1">{{ $p->nama_pengetahuan }}</h3>
                    <div class="text-sm text-slate-400 space-y-1">
                        <p><span class="text-slate-500">Layanan:</span> {{ $p->layanan->nama_layanan }}</p>
                        <p><span class="text-slate-500">Jenis:</span> {{ $p->jenis_pengetahuan }}</p>
                    </div>
                </div>
                
                <div class="flex flex-col gap-2 sm:w-48 shrink-0 justify-center">
                    <a href="{{ route('mpn.pengumpulan.form', ['konteks' => $konteks->id, 'pengetahuan' => $p->id]) }}" 
                       class="inline-flex justify-center items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors border {{ $hasPengumpulan ? 'bg-indigo-600 hover:bg-indigo-500 text-white border-transparent' : 'bg-slate-800 border-indigo-500/30 text-indigo-300 hover:bg-indigo-500/10' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Form 2 (Pengumpulan)
                    </a>
                    
                    @if($isSudah)
                        <a href="{{ route('mpn.pemanfaatan.form', ['konteks' => $konteks->id, 'pengetahuan' => $p->id]) }}" 
                           class="inline-flex justify-center items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors border border-emerald-500/30 text-emerald-400 bg-slate-800 hover:bg-emerald-500/10">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                            Form 3 (Pemanfaatan)
                        </a>
                    @else
                        <button disabled class="inline-flex justify-center items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium bg-slate-800/50 text-slate-500 border border-slate-700/50 cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Form 3 (Terkunci)
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-slate-700/40 px-4 py-16 text-center bg-slate-800/20">
                <p class="text-slate-400">Belum ada pengetahuan yang didaftarkan.</p>
            </div>
        @endforelse
    </div>
</div>
