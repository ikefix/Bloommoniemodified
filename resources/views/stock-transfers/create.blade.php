@extends('layouts.adminapp')

@section('admincontent')
<div class="container">
    <h1>Stock Transfer</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
        </div>
    @endif

    <form action="{{ route('stock-transfers.store') }}" method="POST">
        @csrf

        <!-- From Shop -->
        <div class="form-group">
            <label for="shop_id">From Shop</label>
            <select name="shop_id" id="shop_id" class="form-control">
                <option value="">Select Source Shop</option>
                @foreach($shops as $shop)
                    <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Product (loaded dynamically) -->
        <div class="form-group">
            <label for="product_id">Product</label>
            <select name="product_id" id="product_id" class="form-control">
                <option value="">Select a Product</option>
            </select>
        </div>

        <!-- To Shop -->
        <div class="form-group">
            <label for="to_shop_id">To Shop</label>
            <select name="to_shop_id" id="to_shop_id" class="form-control">
                <option value="">Select Destination Shop</option>
                @foreach($shops as $shop)
                    <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Quantity -->
        <div class="form-group">
            <label for="quantity">Quantity</label>
            <input type="number" name="quantity" id="quantity" class="form-control" min="1">
        </div>

        <!-- Cost Price -->
        <div class="form-group">
            <label for="cost_price">Cost Price</label>
            <input type="number" name="cost_price" id="cost_price" class="form-control" step="0.01">
        </div>

        <!-- Selling Price -->
        <div class="form-group">
            <label for="selling_price">Selling Price</label>
            <input type="number" name="selling_price" id="selling_price" class="form-control" step="0.01">
        </div>

        <button type="submit" class="btn btn-primary">Transfer Stock</button>
    </form>

    @if(session('success'))
        <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif
</div>

<script>
    document.getElementById('shop_id').addEventListener('change', function () {
        const shopId = this.value;
        const productSelect = document.getElementById('product_id');
        productSelect.innerHTML = '<option>Loading...</option>';

        if (shopId) {
            fetch(`/products-by-shop/${shopId}`)
                .then(response => response.json())
                .then(products => {
                    productSelect.innerHTML = '<option value="">Select a Product</option>';
                    products.forEach(product => {
                        const option = document.createElement('option');
                        option.value = product.id;
                        option.textContent = product.name;
                        productSelect.appendChild(option);
                    });
                })
                .catch(err => {
                    console.error(err);
                    productSelect.innerHTML = '<option>Error loading products</option>';
                });
        }
    });
</script>
@endsection
