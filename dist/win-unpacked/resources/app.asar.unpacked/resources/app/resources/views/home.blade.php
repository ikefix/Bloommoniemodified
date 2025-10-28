@extends('layouts.app')


@vite(['resources/sass/app.scss', 'resources/js/app.js', 'resources/css/app.css'])

@section('content')
<div class="container">
    <h2>Cashier Sales</h2>
<!-- 🔍 Receipt Search by Tracking ID -->
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<form action="{{ route('receipt.search') }}" method="GET" class="mb-4" target="_blank">
    <div class="input-group">
        <input type="text" name="transaction_id" class="form-control" placeholder="Enter Receipt Tracking ID" required>
        <button type="submit" class="btn btn-primary">Search Receipt</button>
    </div>
</form>
    <form class="form" method="POST" action="{{ route('purchaseitem.store') }}">
        @csrf

        <!-- Product Search Input -->
        <div class="form-group">
            <label for="product_name">Search Product</label>
            <input type="text" id="product_name" class="form-control" placeholder="Search product name" autocomplete="off">
            <div id="product_suggestions" class="suggestions-box"></div> <!-- Suggestions will be displayed here -->
            <small id="product-error" class="text-danger" style="display: none;">Product does not exist</small>
        </div>

        <!-- Hidden Product ID Input -->
        <input type="hidden" id="product" name="product_id">

        <!-- Product Price Display (Non-editable) -->
        <div class="form-group">
            <label for="price">Price</label>
            <input type="text" id="price" class="form-control" readonly>
        </div>

        <!-- Quantity Input -->
        <div class="form-group">
            <label for="quantity">Quantity</label>
            <input type="number" id="quantity" name="quantity" class="form-control" required min='1'>
        </div>

        <!-- Total Price Display (Non-editable) -->
        <div class="form-group">
            <label for="total_price">Total Price</label>
            <input type="text" id="total_price" class="form-control" readonly>
        </div>

        <!-- Payment Method Selection -->
        <div class="form-group">
            <label for="payment_method">Payment Method</label>
            <select name="payment_method" id="payment_method" class="form-control" required>
                <option value="">Select Payment Method</option>
                <option value="cash">Cash</option>
                <option value="card">Card</option>
                <option value="transfer">Bank Transfer</option>
            </select>
        </div>
    <!-- Discount Section -->
    <div class="form-group">
        <label for="discount_type">Discount Type</label>
        <select id="discount_type" name="discount_type" class="form-control">
            <option value="none">No Discount</option>
            <option value="percentage">Percentage (%)</option>
            <option value="flat">Flat (₦)</option>
        </select>
    </div>

    <div class="form-group">
        <label for="discount_value">Discount Value</label>
        <input type="number" id="discount_value" name="discount_value" class="form-control" placeholder="Enter discount value" min="0" value="0">
    </div>


        <!-- Submit Button -->
        <div class="form-submit">
            <button type="submit" class="btn-add-product">Add Product</button>
        </div>
    </form>

    <!-- Final Preview Section -->
    <div id="preview-box" class="card mt-4 d-none">
        <div class="card-header">🛒 Final Preview</div>
        <div class="card-body">
            <p><strong>Product:</strong> <span id="preview-name"></span></p>
            <p><strong>Price:</strong> ₦<span id="preview-price"></span></p>
            <p><strong>Quantity:</strong> 
                <button type="button" class="btn btn-sm btn-secondary" id="minus-btn">−</button>
                <span id="preview-quantity">1</span>
                <button type="button" class="btn btn-sm btn-secondary" id="plus-btn">+</button>
            </p>
            <p><strong>Total:</strong> ₦<span id="preview-total"></span></p>
            <form id="final-submit-form" method="POST" action="{{ route('purchaseitem.store') }}">
                @csrf
                <input type="hidden" name="product_id" id="final-product-id">
                <input type="hidden" name="quantity" id="preview-total">
                <button type="submit" class="btn btn-success">✅ Complete</button>
            </form>
        </div>
    </div>
</div>
<script>
let productsList = [];

const form = document.querySelector('.form');
const previewBox = document.querySelector('#preview-box');
const previewBody = document.querySelector('.card-body');
const finalForm = document.querySelector('#final-submit-form');

// Handle 'Add Product'
form.addEventListener('submit', function (e) {
    e.preventDefault();

    const name = document.querySelector('#product_name').value;
    const price = parseFloat(document.querySelector('#price').value);
    const productId = document.querySelector('#product').value;
    const quantity = parseInt(document.querySelector('#quantity').value);
    const paymentMethod = document.querySelector('#payment_method').value;

    if (!productId || quantity < 1 || !paymentMethod) return;

    // Fetch available stock for the product
    fetch(`/api/product-stock/${productId}`)
        .then(res => res.json())
        .then(data => {
            const availableStock = data.stock;

            if (quantity > availableStock) {
                alert(`Not enough stock for ${name}. Available: ${availableStock}`);
                return;
            }

            // Add product to cart
            productsList.push({ name, price, productId, quantity, paymentMethod, stock: availableStock });

            // Reset fields
            document.querySelector('#product_name').value = '';
            document.querySelector('#product').value = '';
            document.querySelector('#price').value = '';
            document.querySelector('#quantity').value = '';
            document.querySelector('#total_price').value = '';
            document.querySelector('#payment_method').value = '';

            updateCartPreview();
            updateFinalForm();

            previewBox.classList.remove('d-none');
        })
        .catch(err => {
            console.error(err);
            alert('Could not check stock 😵');
        });
});

function updateCartPreview() {
    previewBody.innerHTML = '';
    let totalSum = 0;

    productsList.forEach((item, index) => {
        const subtotal = item.price * item.quantity;
        totalSum += subtotal;

        const itemDiv = document.createElement('div');
        itemDiv.classList.add('mb-2', 'border-bottom', 'pb-2');

        itemDiv.innerHTML = `
            <p><strong>Product:</strong> ${item.name}</p>
            <p><strong>Price:</strong> ₦${item.price.toFixed(2)}</p>
            <p><strong>Quantity:</strong> 
                <button type="button" class="btn btn-sm btn-secondary minus-btn" data-index="${index}">−</button>
                <span id="preview-quantity-${index}">${item.quantity}</span>
                <button type="button" class="btn btn-sm btn-secondary plus-btn" data-index="${index}">+</button>
            </p>
            <p><strong>Total:</strong> ₦<span id="preview-total-${index}">${subtotal.toFixed(2)}</span></p>
            <p><strong>Payment:</strong>
                <select class="form-control form-control-sm payment-select" data-index="${index}">
                    <option value="cash" ${item.paymentMethod === 'cash' ? 'selected' : ''}>Cash</option>
                    <option value="card" ${item.paymentMethod === 'card' ? 'selected' : ''}>Card</option>
                    <option value="transfer" ${item.paymentMethod === 'transfer' ? 'selected' : ''}>Transfer</option>
                </select>
            </p>
            <button type="button" class="btn btn-sm btn-danger remove-btn" data-index="${index}">❌ Remove</button>
        `;

        previewBody.appendChild(itemDiv);
    });

    // Calculate discount
    const discountType = document.querySelector('#discount_type')?.value || 'none';
    const discountValue = parseFloat(document.querySelector('#discount_value')?.value) || 0;

    let discountedTotal = totalSum;
    if (discountType === 'percentage') {
        discountedTotal -= (totalSum * discountValue / 100);
    } else if (discountType === 'flat') {
        discountedTotal -= discountValue;
    }
    if (discountedTotal < 0) discountedTotal = 0;

    // Display total and discount summary
    const totalDiv = document.createElement('div');
    totalDiv.id = 'cart-total-div';
    totalDiv.innerHTML = `
        <p><strong>Subtotal: ₦${totalSum.toFixed(2)}</strong></p>
        ${discountType !== 'none' ? `<p><strong>Discount:</strong> ${discountType === 'percentage' ? discountValue + '%' : '₦' + discountValue}</p>` : ''}
        <p><strong>Final Total: ₦<span id="cart-total">${discountedTotal.toFixed(2)}</span></strong></p>
    `;
    previewBody.appendChild(totalDiv);

    previewBody.appendChild(finalForm);

    attachQtyListeners();
    attachRemoveListeners();
    attachPaymentListeners();
}

function attachQtyListeners() {
    document.querySelectorAll('.plus-btn').forEach(btn => {
        btn.onclick = function () {
            const index = parseInt(this.dataset.index);
            productsList[index].quantity++;
            refreshQty(index);
        };
    });

    document.querySelectorAll('.minus-btn').forEach(btn => {
        btn.onclick = function () {
            const index = parseInt(this.dataset.index);
            if (productsList[index].quantity > 1) {
                productsList[index].quantity--;
                refreshQty(index);
            }
        };
    });
}

function attachRemoveListeners() {
    document.querySelectorAll('.remove-btn').forEach(btn => {
        btn.onclick = function () {
            const index = parseInt(this.dataset.index);
            productsList.splice(index, 1); // remove product
            updateCartPreview();
            updateFinalForm();
        };
    });
}

function attachPaymentListeners() {
    document.querySelectorAll('.payment-select').forEach(select => {
        select.onchange = function () {
            const index = parseInt(this.dataset.index);
            productsList[index].paymentMethod = this.value;
            updateFinalForm();
        };
    });
}

function refreshQty(index) {
    const item = productsList[index];
    const newTotal = item.price * item.quantity;

    document.getElementById(`preview-quantity-${index}`).textContent = item.quantity;
    document.getElementById(`preview-total-${index}`).textContent = newTotal.toFixed(2);

    updateCartTotal();
    updateFinalForm();
}

function updateCartTotal() {
    let totalSum = productsList.reduce((acc, item) => acc + (item.price * item.quantity), 0);
    const totalSpan = document.querySelector('#cart-total');
    if (totalSpan) totalSpan.textContent = totalSum.toFixed(2);
}

function updateFinalForm() {
    finalForm.innerHTML = `@csrf`;

    productsList.forEach((item, index) => {
        finalForm.innerHTML += `
            <input type="hidden" name="products[${index}][product_id]" value="${item.productId}">
            <input type="hidden" name="products[${index}][quantity]" value="${item.quantity}">
            <input type="hidden" name="products[${index}][payment_method]" value="${item.paymentMethod}">
        `;
    });

    if (productsList.length) {
        finalForm.innerHTML += `
            <button type="submit" class="btn btn-success mt-2">✅ Complete</button>
        `;
    }
}

finalForm.addEventListener('submit', function (e) {
    e.preventDefault();

    if (productsList.length === 0) {
        alert('Bruh, you need to add at least one product 😑');
        return;
    }

    // ✅ Get discount values from the form
    const discountType = document.querySelector('#discount_type')?.value || 'none';
    const discountValue = parseFloat(document.querySelector('#discount_value')?.value) || 0;

    // ✅ Build payload for backend
    const payload = {
        products: productsList.map(item => ({
            product_id: item.productId,
            quantity: item.quantity,
            discount_type: discountType,
            discount_value: discountValue,
        })),
        payment_method: productsList[0].paymentMethod // can adjust per item if needed
    };

    fetch(finalForm.action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(payload)
    })
    .then(res => {
        if (!res.ok) {
            return res.json().then(err => Promise.reject(err));
        }
        return res.json();
    })
    .then(data => {
        if (data.success) {
            window.open(`/purchaseitem/receipt/${data.receipt_id}`, '_blank');
            productsList = [];
            updateCartPreview();
            alert('Sale completed successfully! 💸');
        } else {
            alert('❌ ' + (data.message || 'Failed to complete sale'));
        }
    })
    .catch(err => {
        console.error('Server/validation error:', err);
        alert('⚠️ ' + (err.message || 'Network/server error'));
    });
});


</script>
@endsection