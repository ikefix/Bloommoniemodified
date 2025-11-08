{{-- @extends('layouts.adminapp')

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


@endsection --}}








@extends('layouts.adminapp')

@section('admincontent')
<div class="container">
    {{-- <h2>Daily Sales</h2> --}}

    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <h2>Daily Sales</h2>
        <div>
            <button id="downloadPDF" class="btn btn-success btn-sm">📥 Download PDF</button>
            <button onclick="window.print()" class="btn btn-primary btn-sm">🖨️ Print</button>
        </div>
    </div>

    @php
        // Calculate total directly in the view (safe for small datasets)
        $grandTotal = 0;
        foreach ($sales as $sale) {
            $priceAfterDiscount = $sale->total_price;

            if (!empty($sale->discount_value) && $sale->discount_value > 0) {
                $priceAfterDiscount = $sale->total_price - $sale->discount;
            }

            $grandTotal += $priceAfterDiscount;
        }
    @endphp

    <h1 style="margin-bottom: 20px; font-size: 24px; color: #28a745; text-align:center;">
        Total Sales: ₦{{ number_format($grandTotal, 2) }}
    </h1>

    <div class="row mb-3">
        <div class="col-md-3">
            <input type="text" id="search-input" class="form-control" placeholder="Search product name">
        </div>

        <div class="col-md-3">
            <input type="date" id="date-input" class="form-control" value="{{ $date ?? now()->toDateString() }}">
        </div>

        <div class="col-md-3">
            <select id="shop-input" class="form-control">
                <option value="">All Shops</option>
                @foreach ($shops as $shop)
                    <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div id="sales-table">
        @include('admin.partials.sales_table')
    </div>
</div>

<!-- ✅ PDF Libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>

<script>
    const searchInput = document.getElementById('search-input');
    const dateInput = document.getElementById('date-input');
    const shopSelect = document.getElementById('shop-input');
    const tableWrapper = document.getElementById('sales-table');

    function fetchSales() {
        const search = searchInput.value;
        const date = dateInput.value;
        const shop = shopSelect.value;

        fetch(`/admin/filter-sales?search=${encodeURIComponent(search)}&date=${date}&shop=${shop}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(data => {
            tableWrapper.innerHTML = data;
        });
    }

    searchInput.addEventListener('input', fetchSales);
    dateInput.addEventListener('change', fetchSales);
    shopSelect.addEventListener('change', fetchSales);

    window.addEventListener('DOMContentLoaded', fetchSales);
</script>

<script>
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('delete-sale')) {
        let id = e.target.getAttribute('data-id');
        if (confirm('Are you sure you want to delete this sale?')) {
            fetch(`/admin/sales/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById(`sale-${id}`).remove();
                    alert(data.message);
                } else {
                    alert('Failed to delete sale.');
                }
            })
            .catch(err => console.error(err));
        }
    }
});
</script>

@endsection
