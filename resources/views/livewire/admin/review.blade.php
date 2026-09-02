<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Monitoring Layanan</h1>
            <p class="text-sm text-slate-400 mt-1">Pantau status pengisian modul Manajemen Risiko dari seluruh OPD</p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="flex flex-wrap gap-3 mb-6">
        <select wire:model.live="filterDesa" class="rounded-lg border border-slate-600 bg-slate-800 text-sm text-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <option value="">Semua Desa / OPD</option>
            @foreach($desaList as $desa)
                <option value="{{ $desa->id }}">{{ $desa->nama_desa }}</option>
            @endforeach
        </select>

        <select wire:model.live="filterStatus" class="rounded-lg border border-slate-600 bg-slate-800 text-sm text-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <option value="">Semua Status MR</option>
            <option value="approved">Selesai (Approved)</option>
            <option value="draft">Sedang Dikerjakan (Draft)</option>
            <option value="submitted">Menunggu Review</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="rounded-xl border border-slate-700/50 bg-slate-800/50 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-700/50">
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Desa / Layanan</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">UPR</th>
                    <th class="text-center px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Risiko</th>
                    <th class="text-center px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Status MR</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Diperbarui</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Detail</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/30">
                @forelse($konteksList as $item)
                    @php
                        $statusConfig = [
                            'draft'     => ['label' => 'Draft',        'class' => 'bg-slate-500/10 text-slate-400 border-slate-500/20'],
                            'submitted' => ['label' => 'Menunggu',     'class' => 'bg-amber-500/10 text-amber-400 border-amber-500/20'],
                            'approved'  => ['label' => 'Selesai',      'class' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20'],
                            'rejected'  => ['label' => 'Ditolak',      'class' => 'bg-red-500/10 text-red-400 border-red-500/20'],
                            'archived'  => ['label' => 'Arsip',        'class' => 'bg-violet-500/10 text-violet-400 border-violet-500/20'],
                        ];
                        $sc = $statusConfig[$item->status] ?? ['label' => $item->status, 'class' => 'bg-slate-700/50 text-slate-400'];
                    @endphp
                    <tr class="hover:bg-slate-700/20 transition-colors">
                        <td class="px-5 py-4">
                            <p class="text-white font-medium">{{ $item->desa->nama_desa ?? '-' }}</p>
                            @if($item->layanan)
                                <p class="text-xs text-slate-500 mt-0.5 truncate max-w-xs" title="{{ $item->layanan->nama_layanan }}">
                                    {{ $item->layanan->nama_layanan }}
                                </p>
                            @else
                                <p class="text-xs text-slate-600 italic mt-0.5">Layanan tidak ditemukan</p>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-slate-300 text-sm">{{ $item->nama_upr ?: '-' }}</td>
                        <td class="px-5 py-4 text-center">
                            <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full bg-slate-700/50 text-slate-300 text-xs font-semibold">
                                {{ $item->risiko_count }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $sc['class'] }}">
                                {{ $sc['label'] }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-slate-400 text-xs">
                            {{ $item->updated_at->format('d M Y, H:i') }}
                        </td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('admin.review.detail', $item) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-700/50 text-slate-300 hover:bg-slate-700 text-xs font-medium transition-colors">
                                Lihat Detail
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-slate-500 text-sm">
                            Tidak ada data yang sesuai dengan filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
