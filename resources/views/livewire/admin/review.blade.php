<div>
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-white">Monitoring Laporan</h1>
        <p class="text-sm text-slate-400 mt-1">Pantau status pengisian laporan manajemen risiko seluruh dinas</p>
    </div>

    {{-- ── STAT CARDS ─────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

        {{-- Total Layanan --}}
        <div class="rounded-2xl border border-slate-700/50 bg-slate-800/50 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Layanan</span>
                <div class="w-8 h-8 rounded-lg bg-blue-500/15 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ $stats['total_layanan'] }}</p>
            <p class="text-xs text-slate-500 mt-1">Layanan terdaftar</p>
        </div>

        {{-- Laporan Terkirim --}}
        <div class="rounded-2xl border border-amber-500/25 bg-amber-500/5 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-amber-400/70 uppercase tracking-wider">Laporan Terkirim</span>
                <div class="w-8 h-8 rounded-lg bg-amber-500/15 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-amber-400">{{ $stats['total_submitted'] }}</p>
            <p class="text-xs text-amber-400/60 mt-1">Menunggu ditinjau</p>
        </div>

        {{-- Selesai --}}
        <div class="rounded-2xl border border-emerald-500/25 bg-emerald-500/5 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-emerald-400/70 uppercase tracking-wider">Selesai</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-500/15 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-emerald-400">{{ $stats['total_approved'] }}</p>
            <p class="text-xs text-emerald-400/60 mt-1">Laporan selesai</p>
        </div>

        {{-- Sedang Diisi --}}
        <div class="rounded-2xl border border-slate-600/30 bg-slate-800/30 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Sedang Diisi</span>
                <div class="w-8 h-8 rounded-lg bg-slate-700/50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-slate-300">{{ $stats['total_draft'] }}</p>
            <p class="text-xs text-slate-500 mt-1">Draft / Belum selesai</p>
        </div>

    </div>

    {{-- ── FILTER & SEARCH ─────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-5">
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input wire:model.live.debounce.300ms="search"
                   type="text"
                   placeholder="Cari nama layanan atau dinas..."
                   class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-700 bg-slate-800/60 text-sm text-slate-200 placeholder-slate-500 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/30 outline-none transition">
        </div>
        <select wire:model.live="filterDinas"
                class="px-3 py-2.5 rounded-lg border border-slate-700 bg-slate-800/60 text-sm text-slate-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/30 outline-none transition">
            <option value="">Semua Dinas</option>
            @foreach($dinasList as $dinas)
                <option value="{{ $dinas->id }}">{{ $dinas->nama_dinas }}</option>
            @endforeach
        </select>
        <select wire:model.live="filterStatus"
                class="px-3 py-2.5 rounded-lg border border-slate-700 bg-slate-800/60 text-sm text-slate-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/30 outline-none transition">
            <option value="">Semua Status</option>
            <option value="submitted">Laporan Terkirim</option>
            <option value="approved">Selesai</option>
            <option value="draft">Sedang Diisi</option>
        </select>
    </div>

    {{-- ── TABLE ───────────────────────────────────────────────────────────── --}}
    @php
        $statusColors = [
            'draft'     => 'bg-slate-500/10 text-slate-400 border-slate-500/20',
            'submitted' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
            'approved'  => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
        ];
        $statusLabels = [
            'draft'     => 'Sedang Diisi',
            'submitted' => 'Laporan Terkirim',
            'approved'  => 'Selesai',
        ];
    @endphp

    <div class="rounded-2xl border border-slate-700/50 bg-slate-800/50 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-700/40 flex items-center justify-between">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Daftar Laporan</span>
            <span class="text-xs text-slate-500">{{ $konteksList->count() }} data</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-700/40">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Dinas / Instansi</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Layanan Digital</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Jumlah Risiko</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Terakhir Diperbarui</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/30">
                    @forelse($konteksList as $item)
                        <tr class="hover:bg-slate-700/20 transition-colors">
                            <td class="px-5 py-4">
                                <p class="text-white font-medium">{{ $item->dinas->nama_dinas ?? '-' }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $item->nama_instansi ?? '' }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-slate-200 font-semibold">{{ $item->layanan->nama_layanan ?? '-' }}</p>
                                @if($item->tahun_penilaian)
                                    <p class="text-xs text-slate-500 mt-0.5">Tahun {{ $item->tahun_penilaian }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full bg-slate-700/50 text-slate-300 text-xs font-semibold">
                                    {{ $item->risiko_count }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border {{ $statusColors[$item->status] ?? 'bg-slate-700 text-slate-400 border-slate-600' }}">
                                    {{ $statusLabels[$item->status] ?? ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-slate-400 text-xs">
                                {{ $item->updated_at->format('d M Y') }}
                                <span class="text-slate-600 block">{{ $item->updated_at->format('H:i') }}</span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('admin.review.detail', $item) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-700/60 text-slate-300 hover:bg-slate-600/60 hover:text-white text-xs font-semibold transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <p class="text-slate-500 text-sm">Tidak ada laporan yang sesuai filter.</p>
                                    @if($search || $filterDinas || $filterStatus)
                                        <button wire:click="$set('search', ''); $set('filterDinas', null); $set('filterStatus', '')"
                                                class="text-xs text-blue-400 hover:text-blue-300 underline cursor-pointer">
                                            Hapus semua filter
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
