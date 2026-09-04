<?php

namespace App\Livewire\Admin\User;

use App\Models\Dinas;
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
    public ?int $dinas_id = null;

    public function openCreateModal(): void
    {
        $this->reset(['editingId', 'name', 'email', 'password', 'role_id', 'dinas_id']);
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
        $this->dinas_id   = $user->dinas_id;
        $this->showModal = true;
    }

    public function messages(): array
    {
        return [
            'dinas_id.unique' => 'Dinas ini sudah memiliki akun operator aktif.',
        ];
    }

    public function save(): void
    {
        $rules = [
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255|unique:users,email,' . $this->editingId,
            'role_id' => 'required|exists:roles,id',
            'dinas_id' => [
                'nullable', 
                'exists:dinas,id',
                \Illuminate\Validation\Rule::unique('users', 'dinas_id')->ignore($this->editingId)
            ],
        ];

        if (! $this->editingId) {
            $rules['password'] = 'required|min:6';
        } else {
            $rules['password'] = 'nullable|min:6';
        }

        $this->validate($rules);

        $selectedRole = Role::find($this->role_id);
        $finalDesaId = ($selectedRole && $selectedRole->name === 'admin') ? null : $this->dinas_id;

        $data = [
            'name'    => $this->name,
            'email'   => $this->email,
            'role_id' => $this->role_id,
            'dinas_id' => $finalDesaId,
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
        $query = User::with(['role', 'dinas']);

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
            'dinasList'      => Dinas::orderBy('nama_dinas')->get(),
            'breadcrumb' => [
                'Admin' => null,
                'Kelola User' => null,
            ],
        ]);
    }
}
