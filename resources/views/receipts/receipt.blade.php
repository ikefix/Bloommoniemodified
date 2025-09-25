<!DOCTYPE html>
<html>
<head>
    <title>Receipt</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            margin: 0;
            padding: 0;
            /* width: 58mm; For 58mm printer, change to 80mm if needed */
            width: 70mm;
        }
        .receipt-box {
            padding: 5px;
            width: 100%;
        }
        .header {
            text-align: center;
            margin-bottom: 5px;
        }
        .header h3 {
            margin: 0;
            font-size: 14px;
        }
        .header p {
            margin: 2px 0;
            font-size: 12px;
        }
        hr {
            border: 0;
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        .item-line {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
        }
        .total {
            font-weight: bold;
            text-align: right;
            font-size: 13px;
        }
        .actions {
            margin-top: 10px;
            text-align: center;
        }
        button {
            margin: 3px;
            padding: 5px 10px;
            font-size: 12px;
        }
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
            <div class="item-line">
                <span>{{ $item->product->name }} x {{ $item->quantity }}</span>
                <span>₦{{ number_format($item->total_price, 2) }}</span>
            </div>
        @endforeach
        <hr>
        <p class="total">Total: ₦{{ number_format($total, 2) }}</p>
        <hr>

        <p style="text-align:center; font-size:11px;">Thanks for your purchase 🙏</p>

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

    <!-- PDF library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</body>
</html>
