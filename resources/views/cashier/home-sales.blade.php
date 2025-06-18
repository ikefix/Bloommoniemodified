@extends('layouts.app')

@vite(['resources/sass/app.scss', 'resources/js/app.js', 'resources/css/app.css'])

@section('content')

<div class="container py-4">
    <h2 class="mb-4 fw-bold text-primary">📊 Sales For The Day</h2>

    <div class="row g-3 align-items-end mb-4">
        <div class="col-md-4">
            <label for="search-input" class="form-label fw-semibold">Search Product</label>
            <input type="text" id="search-input" class="form-control shadow-sm" placeholder="🔍 Type product name...">
        </div>
        <div class="col-md-3">
            <label for="date-input" class="form-label fw-semibold">Select Date</label>
            <input type="date" id="date-input" class="form-control shadow-sm" value="{{ $date ?? now()->toDateString() }}">
        </div>
        <div class="col-md-3 mt-4 mt-md-0">
            <button onclick="downloadReceipt()" class="btn btn-success w-100 shadow-sm">
                📥 Download PDF
            </button>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-3" id="sales-table">
            @include('admin.partials.sales_table')
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
    function downloadReceipt() {
        const receipt = document.getElementById('receipt');
        if (!receipt) {
            alert("Receipt element not found.");
            return;
        }
        html2pdf().from(receipt).save('receipt.pdf');
    }

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
    window.addEventListener('DOMContentLoaded', fetchSales);
</script>

@endsection
