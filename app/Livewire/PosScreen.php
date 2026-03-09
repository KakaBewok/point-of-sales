<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Services\QrisService;
use App\Services\TransactionService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Kasir (POS)')]
class PosScreen extends Component
{
    // Cart
    public $cart = [];
    public $search = '';
    public $selectedCategory = '';

    // Discount & Voucher
    public $voucherCode = '';
    public $voucherApplied = false;
    public $voucherDiscount = 0;
    public $voucherError = '';
    public $manualDiscountType = '';
    public $manualDiscountValue = 0;

    // Modal States & Temporary Inputs
    public $showDiscountModal = false;
    public $tempDiscountType = '';
    public $tempDiscountValue = '';

    public $showVoucherModal = false;
    public $tempVoucherCode = '';

    // Payment
    public $showPaymentModal = false;
    public $paymentMethod = 'cash';
    public $cashReceived = 0;
    public $notes = '';

    // Payment result
    public $showResultModal = false;
    public $paymentResult = null;
    public $qrisImageData = '';    // base64 data-URI of the generated QR image

    // QRIS availability
    public $qrisNotConfigured = false;

    // Computed totals
    public function getSubtotalProperty(): float
    {
        return collect($this->cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
    }

    public function getManualDiscountAmountProperty(): float
    {
        if (!$this->manualDiscountType || $this->manualDiscountValue <= 0) {
            return 0;
        }

        if ($this->manualDiscountType === 'percentage') {
            return $this->subtotal * ($this->manualDiscountValue / 100);
        }

        return min($this->manualDiscountValue, $this->subtotal);
    }

    public function getTotalDiscountProperty(): float
    {
        return $this->voucherDiscount + $this->manualDiscountAmount;
    }

    public function getTaxRateProperty(): float
    {
        return Setting::isTaxEnabled() ? Setting::getTaxRate() : 0;
    }

    public function getTaxAmountProperty(): float
    {
        if ($this->taxRate <= 0) return 0;
        return round(($this->subtotal - $this->totalDiscount) * ($this->taxRate / 100));
    }

    public function getGrandTotalProperty(): float
    {
        $total = $this->subtotal - $this->totalDiscount + $this->taxAmount;
        return max(0, $total);
    }

    public function getChangeAmountProperty(): float
    {
        return max(0, (float) $this->cashReceived - $this->grandTotal);
    }

    public function updatedManualDiscountType($value)
    {
        $this->validateDiscount();
    }

    public function updatedManualDiscountValue($value)
    {
        $this->validateDiscount();
    }

    public function validateDiscount()
    {
        $this->manualDiscountValue = (float) $this->manualDiscountValue;
        
        if ($this->manualDiscountValue < 0) {
            $this->manualDiscountValue = 0;
        }

        if ($this->manualDiscountType === 'percentage' && $this->manualDiscountValue > 100) {
            $this->manualDiscountValue = 100;
        } elseif ($this->manualDiscountType === 'fixed' && $this->manualDiscountValue > $this->subtotal) {
            $this->manualDiscountValue = $this->subtotal;
        }
    }

    // ─── Cart Actions ──────────────────────────────────────────

    public function addToCart(int $productId)
    {
        $product = Product::find($productId);
        if (!$product || !$product->is_active) return;

        $key = (string) $productId;
        $isService = $product->isServiceType();

        if (isset($this->cart[$key])) {
            if (!$isService && $this->cart[$key]['quantity'] >= $product->stock) {
                session()->flash('error', "Stok {$product->name} tidak cukup.");
                return;
            }
            $this->cart[$key]['quantity']++;
        } else {
            if (!$isService && $product->stock <= 0) {
                session()->flash('error', "Stok {$product->name} habis.");
                return;
            }
            $this->cart[$key] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => (float) $product->price,
                'quantity' => 1,
                'stock' => $isService ? null : $product->stock,
                'type' => $product->type,
            ];
        }
    }

    public function updateQuantity(string $key, int $quantity)
    {
        if (!isset($this->cart[$key])) return;

        if ($quantity <= 0) {
            $this->removeFromCart($key);
            return;
        }

        $isService = ($this->cart[$key]['type'] ?? 'product') === 'service';

        if (!$isService && $quantity > $this->cart[$key]['stock']) {
            session()->flash('error', 'Melebihi stok tersedia.');
            return;
        }

        $this->cart[$key]['quantity'] = $quantity;
    }

    public function incrementQuantity(string $key)
    {
        if (!isset($this->cart[$key])) return;
        
        $isService = ($this->cart[$key]['type'] ?? 'product') === 'service';

        if (!$isService && $this->cart[$key]['quantity'] >= $this->cart[$key]['stock']) {
            session()->flash('error', 'Stok tidak cukup.');
            return;
        }
        $this->cart[$key]['quantity']++;
    }

    public function decrementQuantity(string $key)
    {
        if (!isset($this->cart[$key])) return;
        if ($this->cart[$key]['quantity'] <= 1) {
            $this->removeFromCart($key);
            return;
        }
        $this->cart[$key]['quantity']--;
    }

    public function removeFromCart(string $key)
    {
        unset($this->cart[$key]);
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->resetVoucher();
        $this->manualDiscountType = '';
        $this->manualDiscountValue = 0;
    }

    // ─── Voucher ────────────────────────────────────────────────

    public function openVoucherModal()
    {
        $this->tempVoucherCode = $this->voucherCode;
        $this->voucherError = '';
        $this->showVoucherModal = true;
    }

    public function appendVoucherKeypad($value)
    {
        $this->tempVoucherCode .= $value;
    }

    public function removeVoucherKeypad()
    {
        $this->tempVoucherCode = substr($this->tempVoucherCode, 0, -1);
    }

    public function clearVoucherKeypad()
    {
        $this->tempVoucherCode = '';
    }

    public function applyVoucherAction()
    {
        $this->voucherCode = $this->tempVoucherCode;
        $this->applyVoucher();
        if (!$this->voucherError) {
            $this->showVoucherModal = false;
        }
    }

    public function applyVoucher()
    {
        $this->voucherError = '';
        $this->voucherApplied = false;
        $this->voucherDiscount = 0;

        if (empty($this->voucherCode)) {
            $this->voucherError = 'Masukkan kode voucher.';
            return;
        }

        try {
            $voucherService = app(\App\Services\VoucherService::class);
            $voucher = $voucherService->validateVoucher($this->voucherCode, $this->subtotal);
            $this->voucherDiscount = $voucherService->applyVoucher($voucher, $this->subtotal);
            $this->voucherApplied = true;
        } catch (\Exception $e) {
            $this->voucherError = $e->getMessage();
        }
    }

    public function resetVoucher()
    {
        $this->voucherCode = '';
        $this->voucherApplied = false;
        $this->voucherDiscount = 0;
        $this->voucherError = '';
    }

    // ─── Discount ───────────────────────────────────────────────

    public function openDiscountModal()
    {
        $this->tempDiscountType = $this->manualDiscountType ?: 'percentage';
        $this->tempDiscountValue = $this->manualDiscountValue ?: '';
        $this->showDiscountModal = true;
    }

    public function appendDiscountKeypad($value)
    {
        $current = (string) $this->tempDiscountValue;
        if ($current === '0') {
            if ($value === '0' || $value === '00' || $value === '000') {
                return;
            }
            $this->tempDiscountValue = $value;
        } else {
            $this->tempDiscountValue = $current . $value;
        }
    }

    public function removeDiscountKeypad()
    {
        $currentStr = (string) $this->tempDiscountValue;
        if (strlen($currentStr) <= 1) {
            $this->tempDiscountValue = '';
        } else {
            $this->tempDiscountValue = substr($currentStr, 0, -1);
        }
    }

    public function clearDiscountKeypad()
    {
        $this->tempDiscountValue = '';
    }

    public function applyDiscountAction()
    {
        $this->manualDiscountType = $this->tempDiscountType;
        $this->manualDiscountValue = (float) $this->tempDiscountValue;
        $this->validateDiscount();
        $this->showDiscountModal = false;
    }

    public function resetDiscount()
    {
        $this->manualDiscountType = '';
        $this->manualDiscountValue = 0;
    }

    // ─── Payment ────────────────────────────────────────────────

    public function openPaymentModal()
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Keranjang masih kosong.');
            return;
        }

        // Check QRIS availability when switching to QRIS
        $this->qrisNotConfigured = false;
        $store = auth()->user()->store ?? null;
        if (! $store || empty($store->qris_payload)) {
            $this->qrisNotConfigured = true;
        }

        $this->paymentMethod = 'cash';
        $this->cashReceived = ceil($this->grandTotal / 1000) * 1000;
        $this->showPaymentModal = true;
    }

    public function updatedPaymentMethod()
    {
        // Re-check QRIS when user switches payment method
        if ($this->paymentMethod === 'qris') {
            $store = auth()->user()->store ?? null;
            $this->qrisNotConfigured = ! $store || empty($store->qris_payload);
        }
    }

    public function appendKeypad($value)
    {
        $current = (string) $this->cashReceived;
        
        // If current is 0, replace it unless appending 00/000
        if ($current === '0') {
            if ($value === '0' || $value === '00' || $value === '000') {
                return;
            }
            $this->cashReceived = (float) $value;
        } else {
            $this->cashReceived = (float) ($current . $value);
        }
    }

    public function removeKeypad()
    {
        $currentStr = (string) $this->cashReceived;
        if (strlen($currentStr) <= 1) {
            $this->cashReceived = 0;
        } else {
            $this->cashReceived = (float) substr($currentStr, 0, -1);
        }
    }

    public function processPayment()
    {
        if (empty($this->cart)) return;

        try {
            $transactionService = app(TransactionService::class);

            // Build cart items
            $cartItems = collect($this->cart)->map(fn ($item) => [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
            ])->values()->toArray();

            // Create transaction
            $transaction = $transactionService->createTransaction(
                cartItems: $cartItems,
                voucherCode: $this->voucherApplied ? $this->voucherCode : null,
                discountType: $this->manualDiscountType ?: null,
                discountValue: (float) $this->manualDiscountValue,
                notes: $this->notes ?: null,
            );

            // Process payment based on method
            if ($this->paymentMethod === 'cash') {
                $payment = $transactionService->processCashPayment($transaction, (float) $this->cashReceived);
                $this->paymentResult = [
                    'success' => true,
                    'method' => 'cash',
                    'invoice' => $transaction->invoice_number,
                    'grand_total' => $transaction->grand_total,
                    'cash_received' => $payment->cash_received,
                    'change' => $payment->change_amount,
                    'transaction_id' => $transaction->id,
                ];

            } elseif ($this->paymentMethod === 'qris') {
                $store = auth()->user()->store;

                if (! $store || empty($store->qris_payload)) {
                    throw new \Exception('QRIS belum dikonfigurasi. Silakan upload gambar QRIS di halaman Pengaturan.');
                }

                // Generate dynamic QRIS payload locally
                $qrisService = app(QrisService::class);
                $dynamicPayload = $qrisService->generateDynamicPayload(
                    $store->qris_payload,
                    $transaction->grand_total
                );
                $this->qrisImageData = $qrisService->generateQrImage($dynamicPayload);

                // Record payment as pending (cashier confirms visually after customer scans)
                $payment = \App\Models\Payment::create([
                    'transaction_id' => $transaction->id,
                    'method'         => 'qris',
                    'amount'         => $transaction->grand_total,
                    'status'         => 'pending',
                ]);

                $this->paymentResult = [
                    'success'        => true,
                    'method'         => 'qris',
                    'invoice'        => $transaction->invoice_number,
                    'grand_total'    => $transaction->grand_total,
                    'qris_image'     => $this->qrisImageData,
                    'transaction_id' => $transaction->id,
                    'payment_id'     => $payment->id,
                ];
            }

            $this->clearCart();
            $this->cashReceived = 0;
            $this->paymentMethod = 'cash';
            $this->showPaymentModal = false;
            $this->showResultModal = true;

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    /**
     * Called when the cashier confirms the customer has paid via QRIS.
     * Marks the payment as successful and completes the transaction.
     */
    public function confirmQrisPayment()
    {
        if (! $this->paymentResult || $this->paymentResult['method'] !== 'qris') return;

        $payment = \App\Models\Payment::find($this->paymentResult['payment_id'] ?? null);

        if ($payment) {
            $payment->markAsSuccess();

            $transaction = $payment->transaction;
            if ($transaction && $transaction->isPending()) {
                app(TransactionService::class)->completeTransaction($transaction);
            }
        }

        $this->paymentResult['qris_confirmed'] = true;
    }

    public function newTransaction()
    {
        $this->clearCart();
        $this->showResultModal = false;
        $this->paymentResult = null;
        $this->qrisImageData = '';
        $this->notes = '';
    }

    public function render()
    {
        $products = Product::active()
            ->inStock()
            ->with('category')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->selectedCategory, fn ($q) => $q->where('category_id', $this->selectedCategory))
            ->orderBy('name')
            ->get();

        $categories = Category::active()->ordered()->withCount(['products' => fn ($q) => $q->active()->inStock()])->get();

        return view('livewire.pos-screen', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
