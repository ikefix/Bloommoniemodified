@extends('layouts.adminapp')

@section('admincontent')
<div class="container">
    <h1>Stock Transfer</h1>

    <!-- Display validation errors if any -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form for stock transfer -->
    <form action="{{ route('stock-transfers.store') }}" method="POST">
        @csrf

        <!-- Product Selection -->
        <div class="form-group">
            <label for="product_id">Product</label>
            <select name="product_id" id="product_id" class="form-control">
                <option value="">Select a Product</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- From Shop -->
        <div class="form-group">
            <label for="shop_id">From Shop</label>
            <select name="shop_id" id="shop_id" class="form-control">
                <option value="">Select Source Shop</option>
                @foreach($shops as $shop)
                    <option value="{{ $shop->id }}" {{ old('shop_id') == $shop->id ? 'selected' : '' }}>{{ $shop->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- To Shop -->
        <div class="form-group">
            <label for="to_shop_id">To Shop</label>
            <select name="to_shop_id" id="to_shop_id" class="form-control">
                <option value="">Select Destination Shop</option>
                @foreach($shops as $shop)
                    <option value="{{ $shop->id }}" {{ old('to_shop_id') == $shop->id ? 'selected' : '' }}>{{ $shop->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Quantity -->
        <div class="form-group">
            <label for="quantity">Quantity</label>
            <input type="number" name="quantity" id="quantity" class="form-control" min="1" value="{{ old('quantity') }}">
        </div>

        <!-- Cost Price -->
        <div class="form-group">
            <label for="cost_price">Cost Price</label>
            <input type="number" name="cost_price" id="cost_price" class="form-control" step="0.01" value="{{ old('cost_price') }}">
        </div>

        <!-- Selling Price -->
        <div class="form-group">
            <label for="selling_price">Selling Price</label>
            <input type="number" name="selling_price" id="selling_price" class="form-control" step="0.01" value="{{ old('selling_price') }}">
        </div>

        <!-- Submit -->
        <button type="submit" class="btn btn-primary">Transfer Stock</button>
    </form>

    <!-- Display success message -->
    @if(session('success'))
        <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif
</div>
@endsection
