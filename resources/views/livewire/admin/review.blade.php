<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Review & Approval Dokumen</h1>
            <p class="text-sm text-slate-400 mt-1">Pemeriksaan dan persetujuan dokumen manajemen risiko dari desa</p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="flex flex-wrap gap-3 mb-6">
        <select wire:model.live="filterDesa" class="rounded-lg border border-slate-600 bg-slate-800 text-sm text-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <option value="">Semua Desa</option>
            @foreach($desaList as $desa)
                <option value="{{ $desa->id }}">{{ $desa->nama_desa }}</option>
            @endforeach
        </select>

        <select wire:model.live="filterStatus" class="rounded-lg border border-slate-600 bg-slate-800 text-sm text-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <option value="">Semua Status</option>
            <option value="submitted">Menunggu Review (Submitted)</option>
            <option value="approved">Disetujui (Approved)</option>
            <option value="rejected">Ditolak (Rejected)</option>
            <option value="draft">Draft</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="rounded-xl border border-slate-700/50 bg-slate-800/50 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-700/50">
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Desa / Instansi</th>
                    <th class="text-center px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Tahun</th>
                    <th class="text-center px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Jumlah Risiko</th>
                    <th class="text-center px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Status</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Tanggal Diajukan</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/30">
                @forelse($konteksList as $item)
                    @php
                        $statusColors = [
                            'draft'     => 'bg-slate-500/10 text-slate-400 border-slate-500/20',
                            'submitted' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                            'approved'  => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                            'rejected'  => 'bg-red-500/10 text-red-400 border-red-500/20',
                            'archived'  => 'bg-violet-500/10 text-violet-400 border-violet-500/20',
                        ];
                        $statusLabels = [
                            'draft' => 'Draft', 'submitted' => 'Menunggu Review',
                            'approved' => 'Disetujui', 'rejected' => 'Ditolak', 'archived' => 'Arsip',
                        ];
                    @endphp
                    <tr class="hover:bg-slate-700/20 transition-colors">
                        <td class="px-5 py-4">
                            <p class="text-white font-medium">{{ $item->desa->nama_desa ?? '-' }}</p>
                            <p class="text-xs text-slate-500">{{ $item->nama_instansi }}</p>
                        </td>
                        <td class="px-5 py-4 text-center text-slate-200 font-semibold">{{ $item->tahun_penilaian }}</td>
                        <td class="px-5 py-4 text-center">
                            <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full bg-slate-700/50 text-slate-300 text-xs font-semibold">
                                {{ $item->risiko_count }} Risiko
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $statusColors[$item->status] ?? '' }}">
                                {{ $statusLabels[$item->status] ?? $item->status }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-slate-400 text-xs">
                            {{ $item->updated_at->format('d M Y, H:i') }}
                        </td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('admin.review.detail', $item) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-500/15 text-emerald-400 hover:bg-emerald-500/25 text-xs font-semibold transition-colors">
                                Review Dokumen
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-slate-500 text-sm">
                            Tidak ada dokumen yang sesuai dengan filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
