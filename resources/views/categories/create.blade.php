@extends('layouts.adminapp')

@section('admincontent')


<div class="container">
    
    {{-- @if(Auth::user()->role === 'admin')
    <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">Dashboard</a>
    @endif --}}

    <form action="{{ route('categories.store') }}" method="POST">
        @csrf
        <h1>Create Category</h1>
        <div class="form-group">
            <label for="name">Category Name:</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
    
        {{-- <div class="form-group">
            <label for="products">Select Existing Product:</label>
            <select name="product_id" id="products" class="form-control">
                <option value="">-- Select a Product (Optional) --</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </div> --}}
        <br>
        <button type="submit" class="btn btn-primary">Create Category</button>
    </form>
</div>
@endsection