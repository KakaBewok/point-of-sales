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

    public $showDeleteModal = false;
    public $itemToDeleteId = null;
    public $itemToDeleteName = null;
    public $deleteType = 'single';

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

    public function confirmDelete($id, $name = '')
    {
        $this->itemToDeleteId = $id;
        $this->itemToDeleteName = $name;
        $this->deleteType = 'single';
        $this->showDeleteModal = true;
    }

    public function processDelete()
    {
        if ($this->deleteType === 'single' && $this->itemToDeleteId) {
            $this->delete($this->itemToDeleteId);
        }
        
        $this->showDeleteModal = false;
        $this->reset(['itemToDeleteId', 'itemToDeleteName', 'deleteType']);
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
            return;
        }
        
        try {
            $user->delete();
            session()->flash('message', 'Pengguna berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                session()->flash('error', 'Pengguna tidak bisa dihapus karena masih terkait dengan data lain (misal: transaksi/stok).');
            } else {
                session()->flash('error', 'Terjadi kesalahan saat menghapus pengguna.');
            }
        }
    }

    public function render()
    {
        $users = User::when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.user-manager', ['users' => $users]);
    }
}
