<?php

namespace App\Livewire;

use App\Models\Transaction;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Detail Transaksi')]
class ReportDetail extends Component
{
    public Transaction $transaction;

    public function mount($id)
    {
        $this->transaction = Transaction::with(['user', 'payment', 'items.product', 'voucher'])
            ->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.report-detail');
    }
}
