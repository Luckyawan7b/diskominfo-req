<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <div class="flex items-center gap-3 flex-wrap">
                <h1 class="text-2xl font-bold text-white">
                    {{ $konteks->dinas->nama_dinas ?? 'Dinas' }}
                </h1>
                @php
                    $statusColors = [
                        'draft'     => 'bg-slate-500/10 text-slate-400 border-slate-500/20',
                        'submitted' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                        'approved'  => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                    ];
                    $statusLabels = [
                        'draft' => 'Sedang Diisi', 'submitted' => 'Laporan Terkirim', 'approved' => 'Selesai',
                    ];
                @endphp
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border {{ $statusColors[$konteks->status] ?? 'bg-slate-700 text-slate-400 border-slate-600' }}">
                    @if($konteks->status === 'submitted')
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    @elseif($konteks->status === 'approved')
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    @endif
                    {{ $statusLabels[$konteks->status] ?? ucfirst($konteks->status) }}
                </span>
            </div>
            <p class="text-sm text-slate-400 mt-1">
                Tahun {{ $konteks->tahun_penilaian }}
                @if($konteks->nama_upr) · UPR: {{ $konteks->nama_upr }} @endif
            </p>
        </div>
        <a href="{{ route('admin.review.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-slate-600 text-sm text-slate-300 hover:bg-slate-700 transition-colors shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
            </svg>
            Kembali ke Monitoring
        </a>
    </div>

    {{-- ── INFO KONTEKS ────────────────────────────────────────────────────── --}}
    <div class="rounded-2xl border border-slate-700/50 bg-slate-800/40 p-5 mb-6 grid grid-cols-1 md:grid-cols-3 gap-5">
        <div>
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Layanan Digital</span>
            <p class="text-slate-200 font-semibold mt-1">{{ $konteks->layanan->nama_layanan ?? '-' }}</p>
        </div>
        <div>
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Tugas UPR</span>
            <p class="text-slate-300 text-sm mt-1">{{ $konteks->tugas_upr ?: '-' }}</p>
        </div>
        <div>
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Struktur Pelaksana</span>
            <p class="text-slate-300 text-sm mt-1">Pemilik: {{ $konteks->strukturPelaksana?->pemilik_risiko ?: '-' }}</p>
            <p class="text-slate-400 text-sm">Koordinator: {{ $konteks->strukturPelaksana?->koordinator_risiko ?: '-' }}</p>
        </div>
    </div>

    {{-- ── STAT MINI-CARDS ─────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="rounded-xl border border-slate-700/50 bg-slate-800/40 p-4 text-center">
            <p class="text-2xl font-bold text-white">{{ $risikoStats['total'] }}</p>
            <p class="text-xs text-slate-500 mt-1">Total Risiko</p>
        </div>
        <div class="rounded-xl border border-red-500/20 bg-red-500/5 p-4 text-center">
            <p class="text-2xl font-bold text-red-400">{{ $risikoStats['sangat_tinggi'] }}</p>
            <p class="text-xs text-red-400/60 mt-1">Sangat Tinggi (&gt;16)</p>
        </div>
        <div class="rounded-xl border border-orange-500/20 bg-orange-500/5 p-4 text-center">
            <p class="text-2xl font-bold text-orange-400">{{ $risikoStats['tinggi'] }}</p>
            <p class="text-xs text-orange-400/60 mt-1">Tinggi (10–16)</p>
        </div>
        <div class="rounded-xl border border-amber-500/20 bg-amber-500/5 p-4 text-center">
            <p class="text-2xl font-bold text-amber-400">{{ $risikoStats['sedang'] }}</p>
            <p class="text-xs text-amber-400/60 mt-1">Sedang (5–9)</p>
        </div>
    </div>

    {{-- ── DAFTAR RISIKO ───────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-bold text-white">Daftar Risiko ({{ $risikoStats['total'] }})</h2>
        <span class="text-xs text-slate-500 bg-slate-800/60 border border-slate-700/50 px-3 py-1 rounded-lg">
            Hanya untuk dipantau — tidak dapat diubah
        </span>
    </div>

    <div class="space-y-4">
        @forelse($risikos as $r)
            @php
                $besaranColor = match(true) {
                    !$r->besaran_risiko   => 'border-slate-700 bg-slate-700/20 text-slate-500',
                    $r->besaran_risiko <= 4 => 'border-emerald-500/30 bg-emerald-500/10 text-emerald-400',
                    $r->besaran_risiko <= 9 => 'border-amber-500/30 bg-amber-500/10 text-amber-400',
                    $r->besaran_risiko <= 16 => 'border-orange-500/30 bg-orange-500/10 text-orange-400',
                    default                 => 'border-red-500/30 bg-red-500/10 text-red-400',
                };
                $levelLabel = match(true) {
                    !$r->besaran_risiko      => 'Belum Dihitung',
                    $r->besaran_risiko <= 4  => 'Rendah',
                    $r->besaran_risiko <= 9  => 'Sedang',
                    $r->besaran_risiko <= 16 => 'Tinggi',
                    default                  => 'Sangat Tinggi',
                };
            @endphp
            <div class="rounded-2xl border border-slate-700/50 bg-slate-800/40 overflow-hidden">

                {{-- Card header --}}
                <div class="flex flex-col sm:flex-row sm:items-center gap-3 px-5 py-4 border-b border-slate-700/40 bg-slate-800/60">
                    <div class="flex items-center gap-3 flex-1 flex-wrap">
                        <span class="font-mono font-bold text-white text-sm bg-slate-700/60 px-2.5 py-1 rounded-lg">
                            {{ $r->kode_risiko }}
                        </span>
                        <span class="text-xs text-slate-400">
                            {{ $r->kategoriRisiko?->nama_kategori ?? 'Tanpa Kategori' }}
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold border {{ $besaranColor }}">
                            {{ $levelLabel }}
                            @if($r->besaran_risiko)
                                · K={{ $r->level_kemungkinan }} × D={{ $r->level_dampak }} = {{ $r->besaran_risiko }}
                            @endif
                        </span>
                    </div>
                </div>

                {{-- Card body --}}
                <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-5 text-sm">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase mb-1">Peristiwa Risiko</p>
                        <p class="text-slate-200">{{ $r->peristiwa_risiko ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase mb-1">Penyebab / Akar Masalah</p>
                        <p class="text-slate-300">{{ $r->penyebab ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase mb-1">Dampak Risiko</p>
                        <p class="text-slate-300">{{ $r->dampak_risiko ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase mb-1">
                            Rencana Perlakuan
                            @if($r->perlakuan?->keputusan_perlakuan)
                                <span class="text-indigo-400 normal-case font-normal">({{ $r->perlakuan->keputusan_perlakuan }})</span>
                            @endif
                        </p>
                        <p class="text-slate-300">{{ $r->perlakuan?->deskripsi_detail_perlakuan ?: '-' }}</p>
                        @if($r->perlakuan?->waktu_rencana_perlakuan || $r->perlakuan?->penanggung_jawab)
                            <p class="text-xs text-slate-500 mt-1.5">
                                @if($r->perlakuan?->waktu_rencana_perlakuan) Target: {{ $r->perlakuan->waktu_rencana_perlakuan }} @endif
                                @if($r->perlakuan?->penanggung_jawab) · PIC: {{ $r->perlakuan->penanggung_jawab }} @endif
                            </p>
                        @endif
                    </div>
                </div>

            </div>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-700/50 px-6 py-12 text-center bg-slate-800/20">
                <p class="text-slate-500 text-sm">Belum ada risiko yang terdaftar untuk layanan ini.</p>
            </div>
        @endforelse
    </div>

</div>