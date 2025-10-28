@extends('layouts.adminapp')

@section('admincontent')
<div class="container">
    <h2>Daily Sales</h2>


    <div class="row mb-3">
        <div class="col-md-4">
            <input type="text" id="search-input" class="form-control" placeholder="Search product name">
        </div>
        <div class="col-md-3">
            <input type="date" id="date-input" class="form-control" value="{{ $date ?? now()->toDateString() }}">
        </div>
    </div>

    <div id="sales-table">
        @include('admin.partials.sales_table')
    </div>
</div>

<script>
    const searchInput = document.getElementById('search-input');
    const dateInput = document.getElementById('date-input');
    const tableWrapper = document.getElementById('sales-table');

    function fetchSales() {
        const search = searchInput.value;
        const date = dateInput.value;

        fetch(`/admin/filter-sales?search=${search}&date=${date}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(data => {
            tableWrapper.innerHTML = data;
        });
    }

    searchInput.addEventListener('input', fetchSales);
    dateInput.addEventListener('change', fetchSales);

    // 🔥 Auto-filter today's sales on page load
    window.addEventListener('DOMContentLoaded', fetchSales);
</script>


@endsection
