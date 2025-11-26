@extends('layouts.managerapp')

@section('managercontent')
<div class="container">
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

    <div id="receipt">
        @include('admin.partials.sales_table')
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
<!-- 💡 Add this script library from CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>

function downloadReceipt() {
const receipt = document.getElementById('receipt');

// Use html2pdf.js to generate PDF
html2pdf().from(receipt).save('receipt.pdf');
}
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
                    fetchSales(); // refresh total + table
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