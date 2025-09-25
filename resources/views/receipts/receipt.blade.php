<!DOCTYPE html>
<html>
<head>
    <title>Receipt</title>
    <style>
        body { font-family: sans-serif; }
        .receipt-box { padding: 20px; border: 1px solid #000; width: 300px; margin: auto; }
        .total { font-weight: bold; }
        .actions { margin-top: 20px; text-align: center; }
        button { margin: 5px; padding: 10px 15px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; }
        .header p { margin: 2px 0; font-size: 14px; }
    </style>
</head>
<body>
    <div class="receipt-box" id="receipt">
        <div class="header">
            <h3>{{ $shopName }}</h3>
            <p>Cashier: {{ $cashier }}</p>
            <p>Date: {{ $items->first()->created_at->format('Y-m-d H:i') }}</p>
        </div>

        <hr>
        @foreach ($items as $item)
            <p>{{ $item->product->name }} x {{ $item->quantity }} - ₦{{ number_format($item->total_price, 2) }}</p>
        @endforeach
        <hr>
        <p class="total">Total: ₦{{ number_format($total, 2) }}</p>

        <div class="actions">
            <button onclick="window.print()">🖨️ Print</button>
            <button onclick="downloadReceipt()">📥 Download PDF</button>
        </div>
    </div>

    <script>
        function downloadReceipt() {
            const receipt = document.getElementById('receipt');
            html2pdf().from(receipt).save('receipt.pdf');
        }
    </script>

    <!-- 💡 Add this script library from CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</body>
</html>
