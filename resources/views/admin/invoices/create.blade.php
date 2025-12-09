@extends(auth()->user()->role === 'admin' ? 'layouts.adminapp' : 'layouts.managerapp')

@section(auth()->user()->role === 'admin' ? 'admincontent' : 'managercontent')

<div class="container">
    <h2 class="mb-4">Create Invoice</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form id="invoiceForm"
    action="{{ auth()->user()->role === 'admin'
        ? route('admin.invoices.store')
        : route('manager.invoices.store') }}"
    method="POST">

        @csrf

        <!-- CUSTOMER SECTION -->
        <div class="card mb-4">
            <div class="card-header"><strong>Customer Details</strong></div>
            <div class="card-body">
                <select name="customer_id" class="form-control" required>
                    <option value="">Select Customer</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }} - {{ $customer->phone }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- ITEMS SECTION -->
        <div class="card mb-4">
            <div class="card-header"><strong>Invoice Items</strong></div>
            <div class="card-body">

                <table class="table table-bordered" id="itemsTable">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th width="100">Qty</th>
                            <th width="120">Price</th>
                            <th width="120">Total</th>
                            <th width="50"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select class="form-control productSelect" name="items[0][product_id]" required>
                                    <option value="">Select product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}"
                                            data-price="{{ $product->selling_price }}">
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            <td><input type="number" class="form-control qty" name="items[0][qty]" value="1"></td>
                            <td><input type="number" class="form-control price" name="items[0][price]" readonly></td>
                            <td><input type="number" class="form-control total" name="items[0][total]" readonly></td>
                            <td><button type="button" class="btn btn-danger removeRow">X</button></td>
                        </tr>
                    </tbody>
                </table>

                <button type="button" id="addRow" class="btn btn-secondary">Add Item</button>
            </div>
        </div>

        <!-- SUMMARY -->
        <div class="card mb-4">
            <div class="card-header"><strong>Payment Summary</strong></div>
            <div class="card-body">

                <div class="row mb-3">
                    <div class="col">
                        <label>Subtotal</label>
                        <input type="number" name="subtotal" id="subtotal" class="form-control" readonly>
                    </div>
                    <div class="col">
                        <label>Discount</label>
                        <input type="number" name="discount" id="discount" class="form-control" value="0">
                    </div>
                    <div class="col">
                        <label>Total</label>
                        <input type="number" name="total" id="total" class="form-control" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col">
                        <label>Amount Paid</label>
                        <input type="number" name="amount_paid" id="amount_paid" class="form-control">
                    </div>
                    <div class="col">
                        <label>Balance</label>
                        <input type="number" name="balance" id="balance" class="form-control" readonly>
                    </div>
                </div>

                <label>Notes</label>
                <textarea name="notes" class="form-control"></textarea>

            </div>
        </div>

        <button class="btn btn-primary">Create Invoice</button>

    </form>

</div>

<script>
let row = 1;

document.getElementById('addRow').onclick = function() {
    let table = document.getElementById('itemsTable').querySelector('tbody');
    let newRow = table.insertRow();

    newRow.innerHTML = `
        <td>
            <select class="form-control productSelect" name="items[${row}][product_id]">
                <option value="">Select product</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" data-price="{{ $product->selling_price }}">
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>
        </td>
        <td><input type="number" class="form-control qty" name="items[${row}][qty]" value="1"></td>
        <td><input type="number" class="form-control price" name="items[${row}][price]" readonly></td>
        <td><input type="number" class="form-control total" name="items[${row}][total]" readonly></td>
        <td><button type="button" class="btn btn-danger removeRow">X</button></td>
    `;

    row++;
};

// Auto calculate
document.addEventListener('input', function() {
    let subtot = 0;

    document.querySelectorAll('#itemsTable tbody tr').forEach(function(tr) {
        let price = tr.querySelector('.productSelect')?.selectedOptions[0]?.dataset.price || 0;
        tr.querySelector('.price').value = price;

        let qty = tr.querySelector('.qty').value || 0;
        let total = qty * price;

        tr.querySelector('.total').value = total;
        subtot += total;
    });

    document.getElementById('subtotal').value = subtot;

    let discount = document.getElementById('discount').value || 0;
    let total = subtot - discount;

    document.getElementById('total').value = total;

    let paid = document.getElementById('amount_paid').value || 0;
    document.getElementById('balance').value = total - paid;
});

// Remove row
document.addEventListener('click', function(e){
    if(e.target.classList.contains('removeRow')){
        e.target.closest('tr').remove();
    }
});
</script>

@endsection
