@extends('layouts.adminapp')

@section('admincontent')
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
            <label>Select Product</label>
            <select name="goods[product_id]" id="product_id" class="form-control" required disabled>
                <option value="">-- Choose Product --</option>
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
            <label>Discount (optional)</label>
            <input type="number" name="discount" id="discount" class="form-control" min="0" value="0">
        </div>

        {{-- Tax --}}
        <div class="mb-3">
            <label>Tax (optional)</label>
            <input type="number" name="tax" id="tax" class="form-control" min="0" value="0">
        </div>

        {{-- Final TOTAL --}}
        <div class="mb-3">
            <label>Final Total</label>
            <input type="text" name="total" id="final_total" class="form-control" readonly>
        </div>

        {{-- PAYMENT TYPE --}}
        <div class="mb-3">
            <label>Payment Type</label>
            <select name="payment_type" id="payment_type" class="form-control" required>
                <option value="full">Full Payment</option>
                <option value="part">Part Payment</option>
            </select>
        </div>

        {{-- AMOUNT PAID (SHOW ONLY IF PART PAYMENT) --}}
        <div class="mb-3 d-none" id="amount_paid_wrapper">
            <label>Amount Paid</label>
            <input type="number" name="amount_paid" id="amount_paid" class="form-control" min="0" value="0">
        </div>

        {{-- BALANCE --}}
        <div class="mb-3">
            <label>Balance</label>
            <input type="text" name="balance" id="balance" class="form-control" readonly>
        </div>

        <button type="submit" class="btn btn-primary">Create Invoice</button>
    </form>
</div>

{{-- JS --}}
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

    const paymentType = document.querySelector('#payment_type');
    const amountPaidWrapper = document.querySelector('#amount_paid_wrapper');
    const amountPaidInput = document.querySelector('#amount_paid');
    const balanceInput = document.querySelector('#balance');

    let products = @json($products);

    // CUSTOMER AUTOFILL
    customers.addEventListener('change', function () {
        const selected = customers.selectedOptions[0];
        emailSpan.textContent = selected.dataset.email;
        phoneSpan.textContent = selected.dataset.phone;
        companySpan.textContent = selected.dataset.company;
    });

    // FILTER PRODUCT BY SHOP
    shopSelect.addEventListener('change', function () {
        const shopId = Number(this.value);
        productSelect.innerHTML = '<option value="">-- Choose Product --</option>';

        if (shopId) {
            productSelect.disabled = false;

            products.filter(p => Number(p.shop_id) === shopId)
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

    // PRICE + TOTAL CALC
    productSelect.addEventListener('change', updatePriceTotal);
    quantityInput.addEventListener('input', updatePriceTotal);
    discountInput.addEventListener('input', updateFinalTotal);
    taxInput.addEventListener('input', updateFinalTotal);
    amountPaidInput.addEventListener('input', calculateBalance);
    paymentType.addEventListener('change', toggleAmountPaid);

    function updatePriceTotal() {
        const selected = productSelect.selectedOptions[0];
        const price = parseFloat(selected?.dataset?.price || 0);
        const qty = Number(quantityInput.value) || 1;

        productPrice.value = price.toFixed(2);
        totalInput.value = (price * qty).toFixed(2);

        updateFinalTotal();
    }

    function updateFinalTotal() {
        const total = Number(totalInput.value) || 0;
        const discount = Number(discountInput.value) || 0;
        const tax = Number(taxInput.value) || 0;

        const finalTotal = total - discount + tax;
        finalTotalInput.value = finalTotal.toFixed(2);

        calculateBalance();
    }

    function toggleAmountPaid() {
        if (paymentType.value === "part") {
            amountPaidWrapper.classList.remove('d-none');
        } else {
            amountPaidWrapper.classList.add('d-none');
            amountPaidInput.value = 0;
        }
        calculateBalance();
    }

    function calculateBalance() {
        const finalTotal = Number(finalTotalInput.value) || 0;
        const amountPaid = Number(amountPaidInput.value) || 0;

        const balance = finalTotal - amountPaid;
        balanceInput.value = balance.toFixed(2);
    }

    function resetPriceTotal() {
        productPrice.value = '';
        totalInput.value = '';
        finalTotalInput.value = '';
        balanceInput.value = '';
    }
</script>

@endsection
