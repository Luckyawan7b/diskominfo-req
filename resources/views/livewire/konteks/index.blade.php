<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Daftar Konteks Risiko</h1>
            <p class="text-sm text-slate-400 mt-1">Kelola dokumen manajemen risiko per tahun penilaian</p>
        </div>

        @if(auth()->user()->isOperator())
            <button wire:click="$set('showCreateModal', true)"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-gradient-to-r from-emerald-500 to-teal-600 text-sm font-semibold text-white shadow-lg shadow-emerald-500/25 hover:from-emerald-600 hover:to-teal-700 transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Buat Konteks Baru
            </button>
        @endif
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap gap-3 mb-6">
        @if(auth()->user()->isAdmin() && $desaList->isNotEmpty())
            <select wire:model.live="filterDesa" class="rounded-lg border border-slate-600 bg-slate-800 text-sm text-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">Semua Desa</option>
                @foreach($desaList as $desa)
                    <option value="{{ $desa->id }}">{{ $desa->nama_desa }}</option>
                @endforeach
            </select>
        @endif
        <select wire:model.live="filterStatus" class="rounded-lg border border-slate-600 bg-slate-800 text-sm text-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <option value="">Semua Status</option>
            <option value="draft">Draft</option>
            <option value="submitted">Submitted</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
            <option value="archived">Archived</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="rounded-xl border border-slate-700/50 bg-slate-800/50 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-700/50">
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Tahun</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Desa / Instansi</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">UPR</th>
                    <th class="text-center px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Risiko</th>
                    <th class="text-center px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Status</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/30">
                @forelse($konteks as $item)
                    <tr class="hover:bg-slate-700/20 transition-colors">
                        <td class="px-5 py-4 text-white font-semibold">{{ $item->tahun_penilaian }}</td>
                        <td class="px-5 py-4">
                            <p class="text-slate-200">{{ $item->desa->nama_desa ?? '-' }}</p>
                            <p class="text-xs text-slate-500">{{ $item->nama_instansi }}</p>
                        </td>
                        <td class="px-5 py-4 text-slate-300">{{ $item->nama_upr ?: '-' }}</td>
                        <td class="px-5 py-4 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-700/50 text-slate-300 text-sm font-medium">
                                {{ $item->risiko_count }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center">
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
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $statusColors[$item->status] ?? '' }}">
                                {{ $statusLabels[$item->status] ?? $item->status }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('konteks.form', $item) }}" class="inline-flex items-center gap-1 text-emerald-400 hover:text-emerald-300 text-sm font-medium transition-colors">
                                {{ $item->isEditableByOperator() && auth()->user()->isOperator() ? 'Edit' : 'Lihat' }}
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="text-slate-400 text-sm">Belum ada konteks risiko</p>
                                <p class="text-slate-500 text-xs mt-1">Klik "Buat Konteks Baru" untuk memulai</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Create Modal --}}
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data x-init="$el.querySelector('input')?.focus()">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" wire:click="$set('showCreateModal', false)"></div>
            <div class="relative bg-slate-800 border border-slate-700/50 rounded-2xl shadow-2xl w-full max-w-md p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Buat Konteks Baru</h3>
                <form wire:submit="createKonteks">
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Tahun Penilaian</label>
                    <input wire:model="newTahun" type="number" min="2020" max="2099"
                        class="w-full rounded-lg border border-slate-600 bg-slate-700/50 px-4 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    @error('newTahun')
                        <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                    <div class="flex gap-3 mt-6">
                        <button type="button" wire:click="$set('showCreateModal', false)" class="flex-1 rounded-lg border border-slate-600 px-4 py-2.5 text-sm text-slate-300 hover:bg-slate-700 transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="flex-1 rounded-lg bg-gradient-to-r from-emerald-500 to-teal-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-emerald-500/25 hover:from-emerald-600 hover:to-teal-700 transition-all cursor-pointer">Buat</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
