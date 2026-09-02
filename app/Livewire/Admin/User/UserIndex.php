<?php

namespace App\Livewire\Admin\User;

use App\Models\Desa;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class UserIndex extends Component
{
    public string $search = '';
    public bool $showModal = false;
    public ?int $editingId = null;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public ?int $role_id = null;
    public ?int $desa_id = null;

    public function openCreateModal(): void
    {
        $this->reset(['editingId', 'name', 'email', 'password', 'role_id', 'desa_id']);
        $operatorRole = Role::where('name', 'operator')->first();
        $this->role_id = $operatorRole?->id;
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $user = User::findOrFail($id);
        $this->editingId = $user->id;
        $this->name      = $user->name;
        $this->email     = $user->email;
        $this->password  = ''; // kosongkan password jika tidak ingin ganti
        $this->role_id   = $user->role_id;
        $this->desa_id   = $user->desa_id;
        $this->showModal = true;
    }

    public function save(): void
    {
        $operatorRole = Role::where('name', 'operator')->first();
        $isOperator = $this->role_id == $operatorRole?->id;

        $rules = [
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255|unique:users,email,' . $this->editingId,
            'role_id' => 'required|exists:roles,id',
            'desa_id' => $isOperator 
                ? 'required|exists:desa,id|unique:users,desa_id,' . $this->editingId 
                : 'nullable|exists:desa,id',
        ];

        if (! $this->editingId) {
            $rules['password'] = 'required|min:6';
        } else {
            $rules['password'] = 'nullable|min:6';
        }

        $this->validate($rules, [
            'desa_id.unique' => 'OPD/Desa ini sudah memiliki akun operator. Satu OPD hanya boleh memiliki 1 akun.',
        ]);

        $selectedRole = Role::find($this->role_id);
        $finalDesaId = ($selectedRole && $selectedRole->name === 'admin') ? null : $this->desa_id;

        $data = [
            'name'    => $this->name,
            'email'   => $this->email,
            'role_id' => $this->role_id,
            'desa_id' => $finalDesaId,
        ];

        if (! empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        User::updateOrCreate(
            ['id' => $this->editingId],
            $data
        );

        $this->showModal = false;
        session()->flash('success', 'Data User berhasil disimpan.');
    }

    public function deleteUser(int $id): void
    {
        if ($id === auth()->id()) {
            session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
            return;
        }

        $user = User::findOrFail($id);
        $user->delete();
        session()->flash('success', 'User berhasil dihapus.');
    }

    public function render()
    {
        $query = User::with(['role', 'desa']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        $users = $query->orderBy('name')->get();

        return view('livewire.admin.user.index', [
            'users'      => $users,
            'roles'      => Role::all(),
            'desas'      => Desa::orderBy('nama_desa')->get(),
            'breadcrumb' => [
                'Admin' => null,
                'Kelola User' => null,
            ],
        ]);
    }
}
