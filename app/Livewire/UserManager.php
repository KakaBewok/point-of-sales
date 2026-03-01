<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Pengguna')]
class UserManager extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editingId = null;

    public $name = '';
    public $email = '';
    public $password = '';
    public $role = 'cashier';
    public $is_active = true;
    public $permissions = [];

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $this->editingId,
            'role' => 'required|in:admin,cashier',
            'is_active' => 'boolean',
            'permissions' => 'array',
        ];

        // Password is required for new users, optional for editing
        if (!$this->editingId) {
            $rules['password'] = 'required|min:8';
        } else {
            $rules['password'] = 'nullable|min:8';
        }

        return $rules;
    }

    public function updatingSearch() { $this->resetPage(); }

    public function create()
    {
        $this->reset(['name', 'email', 'password', 'role', 'is_active', 'editingId', 'permissions']);
        $this->role = 'cashier';
        $this->is_active = true;
        // Check core permissions by default
        $this->permissions = ['dashboard', 'pos'];
        $this->showModal = true;
    }

    public function edit(User $user)
    {
        $this->editingId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->is_active = $user->is_active;
        $this->permissions = $user->permissions ?? [];
        $this->password = ''; // Don't show existing password
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'is_active' => $this->is_active,
            'permissions' => $this->role === 'admin' ? null : $this->permissions,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->editingId) {
            // Prevent admin from deactivating themselves
            if ($this->editingId === auth()->id() && !$this->is_active) {
                session()->flash('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
                return;
            }
            User::findOrFail($this->editingId)->update($data);
            session()->flash('message', 'Pengguna berhasil diperbarui.');
        } else {
            User::create($data);
            session()->flash('message', 'Pengguna berhasil ditambahkan.');
        }

        $this->showModal = false;
    }

    public function render()
    {
        $users = User::when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.user-manager', ['users' => $users]);
    }
}
