<?php

namespace App\Livewire;

use App\Models\Voucher;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Voucher')]
class VoucherManager extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editingId = null;

    public $showDeleteModal = false;
    public $itemToDeleteId = null;
    public $itemToDeleteName = null;
    public $deleteType = 'single';

    public $code = '';
    public $discount_type = 'percentage';
    public $discount_value = '';
    public $max_discount = '';
    public $min_transaction = 0;
    public $usage_limit = null;
    public $valid_from = '';
    public $valid_until = '';
    public $is_active = true;

    protected function rules()
    {
        return [
            'code' => 'required|string|max:50|unique:vouchers,code,' . $this->editingId,
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0' . ($this->discount_type === 'percentage' ? '|max:100' : ''),
            'max_discount' => 'nullable|numeric|min:0',
            'min_transaction' => 'required|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'is_active' => 'boolean',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->reset(['code', 'discount_type', 'discount_value', 'max_discount', 'min_transaction', 'usage_limit', 'valid_from', 'valid_until', 'is_active', 'editingId']);
        $this->discount_type = 'percentage';
        $this->min_transaction = 0;
        $this->is_active = true;
        $this->showModal = true;
    }

    public function edit(Voucher $voucher)
    {
        $this->editingId = $voucher->id;
        $this->code = $voucher->code;
        $this->discount_type = $voucher->discount_type;
        $this->discount_value = $voucher->discount_value;
        $this->max_discount = $voucher->max_discount;
        $this->min_transaction = $voucher->min_transaction;
        $this->usage_limit = $voucher->usage_limit;
        $this->valid_from = $voucher->valid_from ? $voucher->valid_from->format('Y-m-d\TH:i') : null;
        $this->valid_until = $voucher->valid_until ? $voucher->valid_until->format('Y-m-d\TH:i') : null;
        $this->is_active = $voucher->is_active;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'code' => strtoupper(trim($this->code)),
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'max_discount' => $this->discount_type === 'percentage' ? ($this->max_discount === '' ? null : $this->max_discount) : null,
            'min_transaction' => $this->min_transaction,
            'usage_limit' => $this->usage_limit ?: null,
            'valid_from' => $this->valid_from ?: null,
            'valid_until' => $this->valid_until ?: null,
            'is_active' => $this->is_active,
        ];

        if ($this->editingId) {
            Voucher::findOrFail($this->editingId)->update($data);
            session()->flash('message', 'Voucher berhasil diperbarui.');
        } else {
            Voucher::create($data);
            session()->flash('message', 'Voucher berhasil ditambahkan.');
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
        $voucher = Voucher::findOrFail($id);
        if ($voucher->used_count > 0) {
            session()->flash('error', 'Voucher tidak bisa dihapus karena sudah pernah digunakan.');
            return;
        }
        $voucher->delete();
        session()->flash('message', 'Voucher berhasil dihapus.');
    }

    public function render()
    {
        $vouchers = Voucher::withCount('transactions')
            ->when($this->search, fn ($q) => $q->where('code', 'like', "%{$this->search}%"))
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('livewire.voucher-manager', ['vouchers' => $vouchers]);
    }
}
