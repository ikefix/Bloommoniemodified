@extends('layouts.app')


@vite(['resources/sass/app.scss', 'resources/js/app.js', 'resources/css/app.css'])

@section('content')
<div class="container">
    <h2>Sales For The Day</h2>
    <button onclick="downloadReceipt()">📥 Download PDF</button>
    <div id="receipt">
        @include('admin.partials.sales_table')
    </div>
</div>
<script>
    function downloadReceipt() {
    const receipt = document.getElementById('receipt');

    // Use html2pdf.js to generate PDF
    html2pdf().from(receipt).save('receipt.pdf');
}
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
@endsection