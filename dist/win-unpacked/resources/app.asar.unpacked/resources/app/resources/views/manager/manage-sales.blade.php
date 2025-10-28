@extends('layouts.managerapp')

@section('managercontent')
<div class="container">
    <div class="actions">
        <button onclick="window.print()">🖨️ Print</button>
        <button onclick="downloadReceipt()">📥 Download PDF</button>
    </div>
    <h2>Sales For The Day</h2>
    <div id="receipt">
        @include('admin.partials.sales_table')
    </div>
</div>
<!-- 💡 Add this script library from CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>

function downloadReceipt() {
const receipt = document.getElementById('receipt');

// Use html2pdf.js to generate PDF
html2pdf().from(receipt).save('receipt.pdf');
}
</script>

@endsection