<div class="h-screen flex flex-col overflow-hidden bg-zinc-100 dark:bg-zinc-950 -m-6 rounded-none">
    {{-- TOP SEARCH BAR --}}
    <x-pos.header />

    {{-- MAIN LAYOUT: RESPONSIVE FLEXBOX (Fixed for Mobile Stacking) --}}
    <div class="flex flex-1 overflow-y-auto overflow-x-hidden md:overflow-hidden flex-col md:flex-row">
        
        {{-- ====================================================================
             LEFT SIDE: PRODUCT CATALOG (w-full on mobile, 60-70% on desktop)
             ==================================================================== --}}
        <section class="flex-none md:flex-1 overflow-visible md:overflow-hidden flex flex-col min-w-0 bg-zinc-50 dark:bg-zinc-950/20 w-full md:w-[60%] lg:w-[65%] xl:w-[70%]">
            
            {{-- Category Scroller --}}
            <x-pos.category-nav 
                :categories="$categories" 
                :selectedCategory="$selectedCategory" 
            />

            {{-- THE PRODUCT GRID --}}
            <x-pos.product-grid :products="$products" />
        </section>

        {{-- ====================================================================
             RIGHT SIDE: CART & CHECKOUT
             ==================================================================== --}}
        <x-pos.order-summary 
            :cart="$cart"
            :manualDiscountType="$manualDiscountType"
            :manualDiscountValue="$manualDiscountValue"
            :manualDiscountAmount="$this->manualDiscountAmount"
            :voucherApplied="$voucherApplied"
            :voucherCode="$voucherCode"
            :voucherDiscount="$this->voucherDiscount"
            :subtotal="$this->subtotal"
            :totalDiscount="$this->totalDiscount"
            :taxRate="$this->taxRate"
            :taxAmount="$this->taxAmount"
            :grandTotal="$this->grandTotal"
        />
    </div>

    {{-- ====================================================================
         MODALS
         ==================================================================== --}}
    
    <x-pos.payment-modal 
        :grandTotal="$this->grandTotal"
        :paymentMethod="$paymentMethod"
        :cashReceived="$cashReceived"
        :changeAmount="$this->changeAmount"
        :qrisNotConfigured="$qrisNotConfigured"
        :enableVirtualKeypad="$enable_virtual_keypad"
    />

    <x-pos.result-modal 
        :paymentResult="$paymentResult"
        :qrisImageData="$qrisImageData"
    />

    <x-pos.discount-modal 
        :tempDiscountType="$tempDiscountType"
        :tempDiscountValue="$tempDiscountValue"
        :enableVirtualKeypad="$enable_virtual_keypad"
    />

    <x-pos.voucher-modal 
        :tempVoucherCode="$tempVoucherCode"
        :voucherError="$voucherError"
        :enableVirtualKeypad="$enable_virtual_keypad"
    />

    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        .customized-scrollbar::-webkit-scrollbar { width: 4px; }
        .customized-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</div>
