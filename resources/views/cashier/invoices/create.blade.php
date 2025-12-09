@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <h2>Create Invoice</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ auth()->user()->role === 'admin' ? route('admin.invoices.store') : (auth()->user()->role === 'manager' ? route('manager.invoices.store') : route('cashier.invoices.store')) }}" method="POST">
        @csrf

        {{-- Customer Selection --}}
        <div class="mb-3">
            <label for="customer_id">Select Customer</label>
            <select name="customer_id" id="customer_id" class="form-control" required>
                <option value="">-- Choose Customer --</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}"
                        data-email="{{ $customer->email }}"
                        data-phone="{{ $customer->phone }}"
                        data-company="{{ $customer->company }}">
                        {{ $customer->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Display Selected Customer Info --}}
        <div class="mb-3">
            <p>Email: <span id="customer_email">-</span></p>
            <p>Phone: <span id="customer_phone">-</span></p>
            <p>Company: <span id="customer_company">-</span></p>
        </div>

        {{-- Shop Selection --}}
        <div class="mb-3">
            <label for="shop_id">Select Shop</label>
            <select name="shop_id" id="shop_id" class="form-control" required>
                <option value="">-- Choose Shop --</option>
                @foreach($shops as $shop)
                    <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Product Selection --}}
        <div class="mb-3">
            <label for="product_id">Select Product</label>
            <select name="goods[product_id]" id="product_id" class="form-control" required disabled>
                <option value="">-- Choose Product --</option>
                {{-- Options will be populated via JS when shop is selected --}}
            </select>
        </div>

        {{-- Display Price --}}
        <div class="mb-3">
            <label>Price</label>
            <input type="text" id="product_price" class="form-control" readonly>
        </div>

        {{-- Quantity --}}
        <div class="mb-3">
            <label>Quantity</label>
            <input type="number" name="goods[quantity]" id="product_quantity" class="form-control" min="1" value="1">
        </div>

        {{-- Total --}}
        <div class="mb-3">
            <label>Total</label>
            <input type="text" name="goods[total_price]" id="product_total" class="form-control" readonly>
        </div>

        {{-- Discount --}}
        <div class="mb-3">
            <label>Discount (Optional)</label>
            <input type="number" name="discount" id="discount" class="form-control" min="0" value="0">
        </div>

        {{-- Tax --}}
        <div class="mb-3">
            <label>Tax (Optional)</label>
            <input type="number" name="tax" id="tax" class="form-control" min="0" value="0">
        </div>

        {{-- Final Total --}}
        <div class="mb-3">
            <label>Final Total</label>
            <input type="text" name="total" id="final_total" class="form-control" readonly>
        </div>

        <button type="submit" class="btn btn-primary">Create Invoice</button>
    </form>
</div>

{{-- JS for dynamic behavior --}}
@push('scripts')
<script>
    const customers = document.querySelector('#customer_id');
    const emailSpan = document.querySelector('#customer_email');
    const phoneSpan = document.querySelector('#customer_phone');
    const companySpan = document.querySelector('#customer_company');

    const shopSelect = document.querySelector('#shop_id');
    const productSelect = document.querySelector('#product_id');
    const productPrice = document.querySelector('#product_price');
    const quantityInput = document.querySelector('#product_quantity');
    const totalInput = document.querySelector('#product_total');
    const discountInput = document.querySelector('#discount');
    const taxInput = document.querySelector('#tax');
    const finalTotalInput = document.querySelector('#final_total');

    let products = @json($products);

    // Show customer info on selection
    customers.addEventListener('change', function(){
        const selected = customers.selectedOptions[0];
        emailSpan.textContent = selected.dataset.email || '-';
        phoneSpan.textContent = selected.dataset.phone || '-';
        companySpan.textContent = selected.dataset.company || '-';
    });

    // Enable products when shop is selected
    shopSelect.addEventListener('change', function(){
        productSelect.innerHTML = '<option value="">-- Choose Product --</option>';
        const shopId = parseInt(this.value);
        if(shopId){
            productSelect.disabled = false;
            products.filter(p => p.shop_id === shopId)
                .forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p.id;
                    opt.dataset.price = p.price;
                    opt.textContent = p.name;
                    productSelect.appendChild(opt);
                });
        } else {
            productSelect.disabled = true;
        }
        resetPriceTotal();
    });

    // Update price when product changes
    productSelect.addEventListener('change', updatePriceTotal);
    quantityInput.addEventListener('input', updatePriceTotal);
    discountInput.addEventListener('input', updateFinalTotal);
    taxInput.addEventListener('input', updateFinalTotal);

    function updatePriceTotal(){
        const price = parseFloat(productSelect.selectedOptions[0]?.dataset.price || 0);
        const qty = parseInt(quantityInput.value) || 1;
        productPrice.value = price.toFixed(2);
        totalInput.value = (price * qty).toFixed(2);
        updateFinalTotal();
    }

    function updateFinalTotal(){
        const total = parseFloat(totalInput.value) || 0;
        const discount = parseFloat(discountInput.value) || 0;
        const tax = parseFloat(taxInput.value) || 0;
        finalTotalInput.value = (total - discount + tax).toFixed(2);
    }

    function resetPriceTotal(){
        productPrice.value = '';
        totalInput.value = '';
        finalTotalInput.value = '';
    }
</script>
@endpush

@endsection
