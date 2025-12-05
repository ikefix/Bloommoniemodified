@extends('layouts.adminapp')

@section('admincontent')


<div class="container">
    
    <div class="flex-container">
        {{-- @if(Auth::user()->role === 'admin')
    <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">Dashboard</a>
    @endif --}}

    <form id="category-form" action="{{ route('categories.store') }}" method="POST">
        @csrf
        <input type="hidden" id="method-field" name="_method" value="POST">
        <input type="hidden" id="category_id">

        <h1 id="form-title">Create Category</h1>

        <div class="form-group">
            <label for="name">Category Name:</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>

        <br>
        <button type="submit" id="submit-btn" class="btn btn-primary">Create Category</button>
    </form>


    <div class="">
            {{-- <h2 class="mb-4">Product Inventory Overview 🧾</h2> --}}
            <h2 class="mb-4">Available Categories <i class='bx bx-receipt'></i></h2>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <input type="text" id="live-search" class="form-control mb-3" placeholder="🔍 Start typing to search...">
            <div id="product-table">
                @include('categories.partials.table')
            </div>
        
        </div>
    </div>
</div>

        
<script>
document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', () => {
        const id = button.dataset.id;
        const name = button.dataset.name;

        // Update form fields
        document.querySelector('#name').value = name;
        document.querySelector('#form-title').innerText = 'Update Category';
        document.querySelector('#submit-btn').innerText = 'Update Category';
        document.querySelector('#category_id').value = id;

        // Change form action & method
        const form = document.querySelector('#category-form');
        form.action = `/categories/${id}`;
        document.querySelector('#method-field').value = 'PUT';

        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});

// Optional: Reset to create mode after successful creation or if user cancels
document.querySelector('#category-form').addEventListener('reset', () => {
    const form = document.querySelector('#category-form');
    form.action = "{{ route('categories.store') }}";
    document.querySelector('#method-field').value = 'POST';
    document.querySelector('#form-title').innerText = 'Create Category';
    document.querySelector('#submit-btn').innerText = 'Create Category';
});
</script>

@endsection