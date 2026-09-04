<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Daftar Layanan Digital</h1>
            <p class="text-sm text-slate-400 mt-1">
                Kelola layanan digital perangkat daerah Anda
                @if($totalLayanan > 0)
                    <span class="text-slate-500">· {{ $totalLayanan }} layanan terdaftar</span>
                @endif
            </p>
        </div>
        <a href="{{ route('layanan.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-sm font-semibold text-white transition-colors shadow-lg shadow-emerald-600/25 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Layanan
        </a>
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="mb-5 px-4 py-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Search & Filter --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-7">
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input wire:model.live.debounce.300ms="search"
                   type="text"
                   placeholder="Cari nama layanan..."
                   class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-700 bg-slate-800/60 text-sm text-slate-200 placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/30 outline-none transition">
        </div>
        <select wire:model.live="filterStatus"
                class="px-3 py-2.5 rounded-lg border border-slate-700 bg-slate-800/60 text-sm text-slate-300 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/30 outline-none transition">
            <option value="">Semua Status</option>
            <option value="berjalan">Berjalan</option>
            <option value="direncanakan">Direncanakan</option>
            <option value="dihentikan">Dihentikan</option>
        </select>
    </div>

    @php
        $statusColors = [
            'berjalan'     => 'bg-emerald-500/15 text-emerald-400 border-emerald-500/25',
            'direncanakan' => 'bg-blue-500/15 text-blue-400 border-blue-500/25',
            'dihentikan'   => 'bg-red-500/15 text-red-400 border-red-500/25',
        ];
        $statusLabels = [
            'berjalan' => 'Berjalan', 'direncanakan' => 'Direncanakan', 'dihentikan' => 'Dihentikan',
        ];
    @endphp

    {{-- ⭐ Layanan Prioritas --}}
    @if($layananPrioritas->isNotEmpty())
        <div class="mb-8">
            <div class="flex items-center gap-2 mb-4">
                <span class="text-base font-bold text-amber-400">⭐ Layanan Prioritas</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/15 text-amber-400 border border-amber-500/20">
                    {{ $layananPrioritas->count() }}
                </span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                @foreach($layananPrioritas as $layanan)
                    @include('livewire.layanan._card', ['layanan' => $layanan, 'isPrioritas' => true])
                @endforeach
            </div>
        </div>
    @endif

    {{-- Layanan Lainnya --}}
    @if($layananBiasa->isNotEmpty())
        <div>
            @if($layananPrioritas->isNotEmpty())
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-base font-bold text-slate-300">Layanan Lainnya</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-700 text-slate-400 border border-slate-600/50">
                        {{ $layananBiasa->count() }}
                    </span>
                </div>
            @endif
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                @foreach($layananBiasa as $layanan)
                    @include('livewire.layanan._card', ['layanan' => $layanan, 'isPrioritas' => false])
                @endforeach
            </div>
        </div>
    @endif

    {{-- Empty state --}}
    @if($layananPrioritas->isEmpty() && $layananBiasa->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-700/60 px-6 py-16 text-center bg-slate-800/20">
            <div class="w-16 h-16 rounded-2xl bg-slate-800 flex items-center justify-center mx-auto mb-4 border border-slate-700">
                <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            @if($search || $filterStatus)
                <h3 class="text-base font-bold text-slate-300 mb-1">Tidak ada layanan yang cocok</h3>
                <p class="text-sm text-slate-500 mb-5">Coba ubah kata kunci atau hapus filter yang aktif.</p>
                <button wire:click="$set('search', ''); $set('filterStatus', '')"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-600 text-slate-300 hover:bg-slate-700 text-sm font-medium transition-colors cursor-pointer">
                    Hapus Filter
                </button>
            @else
                <h3 class="text-base font-bold text-slate-300 mb-1">Belum ada Layanan Digital</h3>
                <p class="text-sm text-slate-500 mb-5 max-w-sm mx-auto">
                    Mulai dengan mendaftarkan layanan digital pertama Anda untuk dapat mengisi modul manajemen.
                </p>
                <a href="{{ route('layanan.create') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-sm font-semibold text-white transition-colors shadow-lg shadow-emerald-600/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Layanan Pertama
                </a>
            @endif
        </div>
    @endif
</div>


