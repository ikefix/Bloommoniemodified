@extends('layouts.adminapp')

@section('admincontent')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <h2>Daily Sales</h2>
        <div>
            <button id="downloadPDF" class="btn btn-success btn-sm">📥 Download PDF</button>
            <button onclick="window.print()" class="btn btn-primary btn-sm">🖨️ Print</button>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-3">
            <input type="text" id="search-input" class="form-control" placeholder="Search product name" value="{{ $search ?? '' }}">
        </div>

        <div class="col-md-3">
            <input type="date" id="date-input" class="form-control" value="{{ $date ?? now()->toDateString() }}">
        </div>

        <div class="col-md-3">
            <select id="shop-input" class="form-control">
                <option value="">All Shops</option>
                @foreach($shops as $s)
                    <option value="{{ $s->id }}" @if(isset($shop) && $shop == $s->id) selected @endif>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div id="sales-table">
        @include('admin.partials.sales_table', ['sales' => $sales, 'grandTotal' => $sales->sum(fn($sale) => max($sale->total_price - ($sale->discount ?? 0), 0))])
    </div>
</div>

<!-- JS for filtering -->
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
    .then(data => tableWrapper.innerHTML = data);
}

searchInput.addEventListener('input', fetchSales);
dateInput.addEventListener('change', fetchSales);
shopSelect.addEventListener('change', fetchSales);

window.addEventListener('DOMContentLoaded', fetchSales);

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
                } else {
                    alert(data.message ?? "Failed to delete sale");
                }
            })
            .catch(err => console.error(err));
        }
    }
});

</script>
@endsection






