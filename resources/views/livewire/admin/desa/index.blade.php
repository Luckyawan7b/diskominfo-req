<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Kelola Data Desa</h1>
            <p class="text-sm text-slate-400 mt-1">Master data wilayah desa yang terdaftar dalam sistem SPBE</p>
        </div>
        <button wire:click="openCreateModal"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-gradient-to-r from-emerald-500 to-teal-600 text-sm font-semibold text-white shadow-lg shadow-emerald-500/25 hover:from-emerald-600 hover:to-teal-700 transition-all cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Desa Baru
        </button>
    </div>

    {{-- Search --}}
    <div class="mb-4 max-w-sm">
        <input wire:model.live.debounce.300ms="search" type="text"
            class="w-full rounded-lg border border-slate-600 bg-slate-800 text-sm text-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500"
            placeholder="Cari desa atau kecamatan...">
    </div>

    {{-- Table --}}
    <div class="rounded-xl border border-slate-700/50 bg-slate-800/50 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-700/50">
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase">Kode</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase">Nama Desa</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase">Kecamatan / Kabupaten</th>
                    <th class="text-center px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase">Operator</th>
                    <th class="text-center px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase">Dokumen</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/30">
                @forelse($desas as $desa)
                    <tr class="hover:bg-slate-700/20 transition-colors">
                        <td class="px-5 py-4 font-mono font-bold text-emerald-400">{{ $desa->kode_desa }}</td>
                        <td class="px-5 py-4 text-white font-medium">{{ $desa->nama_desa }}</td>
                        <td class="px-5 py-4 text-slate-400 text-xs">
                            {{ $desa->kecamatan ?: '-' }}, {{ $desa->kabupaten ?: '-' }}
                        </td>
                        <td class="px-5 py-4 text-center text-slate-300">{{ $desa->users_count }}</td>
                        <td class="px-5 py-4 text-center text-slate-300">{{ $desa->konteks_count }}</td>
                        <td class="px-5 py-4 text-right space-x-2">
                            <button wire:click="openEditModal({{ $desa->id }})" class="text-emerald-400 hover:text-emerald-300 text-xs font-medium cursor-pointer">
                                Edit
                            </button>
                            <button wire:click="deleteDesa({{ $desa->id }})" wire:confirm="Hapus desa ini beserta datanya?" class="text-red-400 hover:text-red-300 text-xs font-medium cursor-pointer">
                                Hapus
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-slate-500 text-sm">
                            Tidak ada data desa.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Create / Edit --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" wire:click="$set('showModal', false)"></div>
            <div class="relative bg-slate-800 border border-slate-700/50 rounded-2xl shadow-2xl w-full max-w-md p-6 space-y-4">
                <h3 class="text-base font-semibold text-white">
                    {{ $editingId ? 'Edit Data Desa' : 'Tambah Desa Baru' }}
                </h3>

                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-1">Kode Desa <span class="text-red-400">*</span></label>
                        <input wire:model="kode_desa" type="text" class="w-full rounded-lg border border-slate-600 bg-slate-700/50 px-3 py-2 text-white text-sm uppercase focus:ring-2 focus:ring-emerald-500 focus:outline-none" placeholder="SKM">
                        @error('kode_desa') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-1">Nama Desa <span class="text-red-400">*</span></label>
                        <input wire:model="nama_desa" type="text" class="w-full rounded-lg border border-slate-600 bg-slate-700/50 px-3 py-2 text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none" placeholder="Desa Sukamaju">
                        @error('nama_desa') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-1">Kecamatan</label>
                        <input wire:model="kecamatan" type="text" class="w-full rounded-lg border border-slate-600 bg-slate-700/50 px-3 py-2 text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none" placeholder="Kecamatan Ciawi">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-1">Kabupaten</label>
                        <input wire:model="kabupaten" type="text" class="w-full rounded-lg border border-slate-600 bg-slate-700/50 px-3 py-2 text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none" placeholder="Kabupaten Bogor">
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 rounded-lg border border-slate-600 text-sm text-slate-300 hover:bg-slate-700 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-sm font-semibold text-white cursor-pointer">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
