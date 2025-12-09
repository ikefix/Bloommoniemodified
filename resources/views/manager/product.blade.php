@extends('layouts.managerapp')

@section('managercontent')

<div class="container">

    <div class="flex-container">

        <!-- Display Success Message -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="position: fixed; right: 20px; top: 20px; z-index: 9999;">
                <strong>Success!</strong> {{ session('success') }}
                <!-- Close Button with Bootstrap's dismiss functionality -->
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if(Auth::user()->role === 'manager')
            @php
                $hasProductAccess = \App\Models\ProductPermission::where('manager_id', Auth::user()->id)->exists();
            @endphp

            @if($hasProductAccess)
                <form action="{{ route('products.store') }}" method="POST" id="product-form">
                    <h2>Add New Product</h2>
                    @csrf

                    <!-- Select Store -->
                    <div class="mb-3">
                        <label for="shop_id">Select Shop</label>
                        <select name="shop_id" id="shop_id" class="form-control" required>
                            <option value="">-- Select Category --</option>
                            @foreach ($shops as $shop)
                                <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Select Category -->
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select name="category_id" id="category_id" class="form-control" required>
                            <option value="">-- Select Category --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Product Name with suggestions -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name</label>
                        <input list="product_suggestions" name="name" id="name" class="form-control" required>
                        <datalist id="product_suggestions"></datalist>
                    </div>

                    <!-- Price -->
                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" name="price" id="price" class="form-control" required>
                    </div>

                    <!-- Cost Price -->
                    <div class="mb-3">
                        <label for="cost_price" class="form-label">Cost Price</label>
                        <input type="number" name="cost_price" id="cost_price" class="form-control" required>
                    </div>

                    <!-- Stock Quantity -->
                    <div class="mb-3">
                        <label for="stock_quantity" class="form-label">Stock Quantity</label>
                        <input type="number" name="stock_quantity" id="stock_quantity" class="form-control" required>
                    </div>

                    <!-- Stock Limit -->
                    <div class="mb-3">
                        <label for="stock_limit" class="form-label">Stock Limit</label>
                        <input type="number" name="stock_limit" id="stock_limit" class="form-control" value="{{ old('stock_limit') }}" required>
                    </div>

                    <!-- Hidden Product ID (used for editing) -->
                    <input type="hidden" name="product_id" id="product_id">

                    <!-- 🔥 Hidden _method field to switch POST/PUT via JS -->
                    <input type="hidden" name="_method" id="form_method" value="POST">

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary">Add Product</button>
                </form>
                @else
                    <p>You do not have access to this page.</p>
                @endif
            @endif
            

        <!-- Product Table (can be hidden based on access) -->
        <div class="">
            <h2 class="mb-4">Available Product 🧾</h2>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <input type="text" id="live-search" class="form-control mb-3" placeholder="🔍 Start typing to search...">
            <div id="product-table">
                <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Selling Price (₦)</th>
                            <th>Cost Price (₦)</th>
                            <th>Remaining Stock</th>
                            <th>Actions</th>
                            <th>Shop</th>
                            <th>Barcode</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr id="product-row-{{ $product->id }}">
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->category->name ?? 'N/A' }}</td>
                                <td>{{ number_format($product->price, 2) }}</td>
                                <td>{{ number_format($product->cost_price, 2) }}</td>
                                <td>{{ $product->stock_quantity }}</td>
                                <td class="product-btn">
                                    <button type="button" class="btn btn-sm btn-warning edit-btn"
                                        data-id="{{ $product->id }}"
                                        data-name="{{ $product->name }}"
                                        data-category="{{ $product->category_id }}"
                                        data-price="{{ $product->price }}"
                                        data-cost="{{ $product->cost_price }}"
                                        data-stock="{{ $product->stock_quantity }}"
                                        data-limit="{{ $product->stock_limit }}">
                                        Edit
                                    </button>

                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display:inline-block;" class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?')">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                                <td>{{ $product->shop->name ?? 'Not assigned' }}</td>
                                <td>{{ $product->barcode ?? 'Not assigned' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center">No products found.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-center mt-3">
                    @if ($products->hasPages())
                        <nav>
                            <ul class="pagination">
                                {{-- Prev Button --}}
                                <li class="page-item {{ $products->onFirstPage() ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $products->previousPageUrl() }}" tabindex="-1">
                                        ⬅️ Prev
                                    </a>
                                </li>

                                {{-- Page Numbers --}}
                                @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                                    <li class="page-item {{ $products->currentPage() == $page ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                    </li>
                                @endforeach

                                {{-- Next Button --}}
                                <li class="page-item {{ !$products->hasMorePages() ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $products->nextPageUrl() }}">
                                        Next ➡️
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>


<script>
    const form = document.querySelector('#product-form');
    const productIdField = document.querySelector('#product_id');

    // Set default form action and method field on load
    const methodField = document.querySelector('#form_method');
    const csrfToken = document.querySelector('input[name="_token"]').value;

    form.addEventListener('submit', function(e) {
        const productId = productIdField.value.trim();

        let url, method;

        if (productId) {
            // Editing mode
            url = `/products/${productId}`;
            method = 'PUT';
        } else {
            // Creating new product
            url = `{{ route('products.store') }}`;
            method = 'POST';
        }

        e.preventDefault();

        const formData = new FormData(form);
        formData.append('_method', method);

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        })
        .then(response => {
            if (response.ok) {
                location.reload();
            } else {
                alert(method + ' failed.');
            }
        })
        .catch(error => console.error('Error:', error));
    });
</script>


<script>
document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', () => {
        document.querySelector('#product_id').value = button.dataset.id;
        document.querySelector('#name').value = button.dataset.name;
        document.querySelector('#category_id').value = button.dataset.category;
        document.querySelector('#price').value = button.dataset.price;
        document.querySelector('#cost_price').value = button.dataset.cost;
        document.querySelector('#stock_quantity').value = button.dataset.stock;
        document.querySelector('#stock_limit').value = button.dataset.limit;
        
            // Optionally scroll to form
            window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});
    </script>

<script>
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // stop form from reloading page
    
            if (!confirm('Delete this product?')) return;
    
            const action = this.getAttribute('action');
            const token = this.querySelector('input[name="_token"]').value;
    
            fetch(action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: new URLSearchParams({
                    '_method': 'DELETE'
                })
            })
            .then(response => {
                if (response.ok) {
                    // Remove the row from DOM
                    const row = this.closest('tr');
                    row.remove();
    
                    // Show success alert
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success alert-dismissible fade show';
                    alert.style.position = 'fixed';
                    alert.style.top = '20px';
                    alert.style.right = '20px';
                    alert.style.zIndex = 9999;
                    alert.innerHTML = `
                        <strong>Deleted!</strong> Product deleted successfully.
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    `;
                    document.body.appendChild(alert);
                } else {
                    alert("Something went wrong.");
                }
            })
            .catch(err => console.error(err));
        });
    });
    </script>
    <script>
        document.getElementById('live-search').addEventListener('input', function () {
            let query = this.value;
    
            fetch(`/products/search?query=${query}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.text())
            .then(html => {
                document.getElementById('product-table').innerHTML = html;
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            // Live search
            $('#live-search').on('keyup', function () {
                let search = $(this).val();
                fetchProducts(1, search);
            });
    
            // Handle pagination clicks
            $(document).on('click', '.pagination a', function (e) {
                e.preventDefault();
                let page = $(this).attr('href').split('page=')[1];
                let search = $('#live-search').val();
                fetchProducts(page, search);
            });
    
            function fetchProducts(page, search = '') {
                $.ajax({
                    url: `?page=${page}&search=${search}`,
                    success: function (data) {
                        $('#product-table').html(data);
                    }
                });
            }
        });
    </script>
    <script>
        $(document).ready(function () {
            // Pagination clicks
            $(document).on('click', '#product-table .pagination a', function (e) {
                e.preventDefault();
                let url = $(this).attr('href');
    
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        $('#product-table').html(data);
                    },
                    error: function () {
                        alert('Something went wrong with pagination.');
                    }
                });
            });
        });
    </script>
@endsection
