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
        </tr>
    </thead>
    <tbody>
        @forelse($sales as $sale)
            <tr>
                <td>{{ $sale->product->name ?? 'Product Deleted' }}</td>
                <td>{{ $sale->product->category->name ?? 'Category Missing' }}</td>
                <td>{{ $sale->quantity }}</td>
                <td>₦{{ number_format($sale->total_price, 2) }}</td>
                <td>{{ ucfirst($sale->payment_method) }}</td>
                <td>{{ $sale->created_at->format('Y-m-d H:i:s') }}</td>
                <td>{{ $sale->shop->name ?? 'Unknown Shop' }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center">No sales found</td></tr>
        @endforelse
    </tbody>
</table>
