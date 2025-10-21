<table class="table table-bordered">
    <thead>
        <tr>
            <th>Product Name</th>
            <th>Category</th>
            <th>Quantity</th>
            <th>Total Price</th>
            <th>Payment Method</th>
            <th>Date</th>
            <th>Shop</th>
            <th>Transaction ID</th>
            <th>Discount Value</th>
        </tr>
    </thead>
    <tbody>
        @forelse($sales as $sale)
            <tr>
                <td>{{ $sale->product->name ?? 'Product Deleted' }}</td>
                <td>{{ $sale->product->category->name ?? 'Category Missing' }}</td>
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
                <td>{{ $sale->transaction_id ?? 'Unknown Transaction' }}</td>
                <td>{{ $sale->discount_value ?? 'Unknown Transaction' }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center">No sales found</td></tr>
        @endforelse
    </tbody>
</table>
