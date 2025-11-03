<table class="table table-bordered">
    <thead>
        <tr>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Total Price</th>
            <th>Payment Method</th>
            <th>Date</th>
            <th>Shop</th>
            <th>Transaction ID</th>
            <th>Discount Value</th>
            <th>Cashier</th>
            <th>Action</th> {{-- 👈 new --}}
        </tr>
    </thead>
    <tbody>
        @forelse($sales as $sale)
            <tr id="sale-{{ $sale->id }}">
                <td>{{ $sale->product->name ?? 'Product Deleted' }}</td>
                <td>{{ $sale->quantity }}</td>
                <td>
                    @if(!empty($sale->discount_value) && $sale->discount_value > 0)
                        <span style="text-decoration: line-through; color: red;">
                            ₦{{ number_format($sale->total_price, 2) }}
                        </span><br>
                        <span style="color: #28a745; font-weight: bold;">
                            ₦{{ number_format($sale->total_price - $sale->discount, 2) }}
                        </span>
                    @else
                        <span style="color: #000;">
                            ₦{{ number_format($sale->total_price, 2) }}
                        </span>
                    @endif
                </td>
                <td>{{ ucfirst($sale->payment_method) }}</td>
                <td>{{ $sale->created_at->format('Y-m-d H:i:s') }}</td>
                <td>{{ $sale->shop->name ?? 'Unknown Shop' }}</td>
                <td>{{ $sale->transaction_id ?? '-' }}</td>
                <td>{{ $sale->discount_value ?? '-' }}</td>
                <td>{{ $sale->cashier->name ?? 'Unknown' }}</td> {{-- optional --}}
                <td>
                    <button class="btn btn-danger btn-sm delete-sale" data-id="{{ $sale->id }}">
                        Revoke Sale
                    </button>
                </td>
            </tr>
        @empty
            <tr><td colspan="10" class="text-center">No sales found</td></tr>
        @endforelse
    </tbody>
</table>
