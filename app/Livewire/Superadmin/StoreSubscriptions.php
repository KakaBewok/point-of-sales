<?php

namespace App\Livewire\Superadmin;

use App\Models\Store;
use Livewire\Component;
use Livewire\WithPagination;

class StoreSubscriptions extends Component
{
    use WithPagination;

    public $search = '';

    // Modals state
    public $editStoreId = null;
    public $editSubscriptionPlan = null;
    public $editSubscriptionStatus = null;
    public $editSubscriptionStartsAt = null;
    public $editSubscriptionEndsAt = null;

    protected $rules = [
        'editSubscriptionPlan' => 'nullable|in:monthly,yearly',
        'editSubscriptionStatus' => 'required|in:active,trial,suspended,cancelled,expired',
        'editSubscriptionStartsAt' => 'nullable|date',
        'editSubscriptionEndsAt' => 'nullable|date',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        // Add layout title here just in case, but usually handled in component
    }

    public function editStore($storeId)
    {
        $store = Store::findOrFail($storeId);
        $this->editStoreId = $store->id;
        $this->editSubscriptionPlan = $store->subscription_plan;
        $this->editSubscriptionStatus = $store->subscription_status;
        $this->editSubscriptionStartsAt = $store->subscription_starts_at ? $store->subscription_starts_at->format('Y-m-d') : null;
        $this->editSubscriptionEndsAt = $store->subscription_ends_at ? $store->subscription_ends_at->format('Y-m-d') : null;
    }

    public function cancelEdit()
    {
        $this->reset(['editStoreId', 'editSubscriptionPlan', 'editSubscriptionStatus', 'editSubscriptionStartsAt', 'editSubscriptionEndsAt']);
    }

    public function saveSubscription()
    {
        $this->validate();

        $store = Store::findOrFail($this->editStoreId);
        $store->update([
            'subscription_plan' => $this->editSubscriptionPlan ?: null,
            'subscription_status' => $this->editSubscriptionStatus,
            'subscription_starts_at' => $this->editSubscriptionStartsAt ?: null,
            'subscription_ends_at' => $this->editSubscriptionEndsAt ?: null,
        ]);

        session()->flash('success', 'Subscription updated successfully for ' . $store->name);

        $this->cancelEdit();
    }

    public function render()
    {
        $stores = Store::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('slug', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.superadmin.store-subscriptions', [
            'stores' => $stores,
        ])->layout('layouts.app', ['title' => 'Superadmin - Store Subscriptions']);
    }
}
