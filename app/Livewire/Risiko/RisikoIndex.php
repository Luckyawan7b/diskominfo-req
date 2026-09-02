<?php
namespace App\Livewire\Risiko;

use App\Models\MrKonteks;
use App\Models\MrRisiko;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class RisikoIndex extends Component
{
    public MrKonteks $konteks;
    public string $filterStatus = '';

    public function mount(MrKonteks $konteks): void { $this->konteks = $konteks; }

    public function render()
    {
        $query = $this->konteks->risiko()->with('kategoriRisiko')->orderBy('prioritas_risiko');
        if ($this->filterStatus) $query->where('status', $this->filterStatus);
        $user = auth()->user();
        $availableKonteks = collect();
        if ($user->isOperator()) {
            $availableKonteks = MrKonteks::where('desa_id', $user->desa_id)
                ->orderByDesc('tahun_penilaian')
                ->get();
        } elseif ($user->isAdmin()) {
            $availableKonteks = MrKonteks::where('desa_id', $this->konteks->desa_id)
                ->orderByDesc('tahun_penilaian')
                ->get();
        }

        return view('livewire.risiko.index', [
            'risikos' => $query->get(),
            'isEditable' => $this->konteks->isEditableByOperator() || auth()->user()->isAdmin(),
            'breadcrumb' => ['Manajemen Risiko' => route('konteks.index'), 'Konteks ' . $this->konteks->tahun_penilaian . ' / ' . $this->konteks->tahun_pelaksanaan => route('konteks.form', $this->konteks), 'Daftar Risiko' => null],
        ])->layout('components.layouts.app', [
            'konteks' => $this->konteks,
            'availableKonteks' => $availableKonteks,
        ]);
    }
}
