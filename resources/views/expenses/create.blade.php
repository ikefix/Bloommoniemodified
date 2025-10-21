@extends('layouts.adminapp')

@section('admincontent')
<div class="container">
    <h3>➕ Add New Expense</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
        </div>
    @endif

    <form action="{{ route('expenses.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Expense Title</label>
            <input type="text" name="title" class="form-control" required placeholder="e.g. Generator Fuel">
        </div>

        <div class="mb-3">
            <label class="form-label">Amount (₦)</label>
            <input type="number" step="0.01" name="amount" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Select Shop</label>
            <select name="shop_id" class="form-control" required>
                <option value="">-- Choose Shop --</option>
                @foreach(App\Models\Shop::all() as $shop)
                    <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                @endforeach
            </select>
        </div>


        <div class="mb-3">
            <label class="form-label">Date</label>
            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description (optional)</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Describe the expense..."></textarea>
        </div>

        <button type="submit" class="btn btn-success">Save Expense</button>
        <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Cancel</a>
    </form>

    {{-- <div id="">
        @include('expenses.index')
    </div> --}}
</div>
@endsection
