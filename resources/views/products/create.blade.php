@extends('layouts.adminapp')

@section('admincontent')
<div class="container" style="margin: 0 0; max-width:1400px;">
    
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

            <!-- Barcode Section -->
            <div class="mb-3">
                <label for="barcode" class="form-label">Barcode</label>
                <div class="input-group mb-2">
                    <input type="text" name="barcode" id="barcode" class="form-control" required>
                    <button type="button" id="generate-barcode" class="btn btn-secondary">Generate Code</button>
                </div>

                <!-- Barcode preview -->
                <svg id="barcode-preview" style="max-height:80px;"></svg>

                <!-- Download button -->
                <button type="button" id="download-barcode" class="btn btn-success mt-2" style="display:none;">Download Barcode</button>
            </div>



        
            <!-- Hidden Product ID (used for editing) -->
            <input type="hidden" name="product_id" id="product_id">
        
            <!-- 🔥 Hidden _method field to switch POST/PUT via JS -->
            <input type="hidden" name="_method" id="form_method" value="POST">
        
            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">Add Product</button>
        </form>
        

        <div class="product-table">
            {{-- <h2 class="mb-4">Product Inventory Overview 🧾</h2> --}}
            <h2 class="mb-4">Available Product <i class='bx bx-receipt'></i></h2>
        
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <input type="text" id="live-search" class="form-control mb-3" 
       placeholder="🔍 Start typing to search..." 
       value="{{ $search ?? '' }}">

            <div id="product-table">
                @include('products.partials.table')
            </div>
        
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>


<script>
document.getElementById('generate-barcode').addEventListener('click', function() {
    const code = 'BC' + Date.now();
    document.getElementById('barcode').value = code;

    // Generate the barcode in SVG
    JsBarcode("#barcode-preview", code, {
        format: "CODE128",
        lineColor: "#000",
        width: 2,
        height: 60,
        displayValue: true
    });

    // Show the download button
    document.getElementById('download-barcode').style.display = 'inline-block';
});

document.getElementById('download-barcode').addEventListener('click', function() {
    const svg = document.getElementById('barcode-preview');
    const serializer = new XMLSerializer();
    const svgBlob = new Blob([serializer.serializeToString(svg)], {type: 'image/svg+xml'});
    const url = URL.createObjectURL(svgBlob);

    // Convert SVG → Canvas → PNG for download
    const img = new Image();
    img.onload = function() {
        const canvas = document.createElement('canvas');
        canvas.width = img.width;
        canvas.height = img.height;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(img, 0, 0);
        const pngUrl = canvas.toDataURL("image/png");

        // Create download link
        const link = document.createElement('a');
        link.href = pngUrl;
        link.download = `${document.getElementById('barcode').value}.png`;
        link.click();

        URL.revokeObjectURL(url);
    };
    img.src = url;
});

// Prevent submitting without barcode
document.getElementById('product-form').addEventListener('submit', function(e) {
    const barcode = document.getElementById('barcode').value.trim();
    if (!barcode) {
        e.preventDefault();
        alert('Please generate a barcode before adding the product.');
    }
});
</script>




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
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('edit-btn')) {
        const button = e.target;

        document.querySelector('#product_id').value = button.dataset.id;
        document.querySelector('#name').value = button.dataset.name;
        document.querySelector('#category_id').value = button.dataset.category;
        document.querySelector('#price').value = button.dataset.price;
        document.querySelector('#cost_price').value = button.dataset.cost;
        document.querySelector('#stock_quantity').value = button.dataset.stock;
        document.querySelector('#stock_limit').value = button.dataset.limit;

        // Scroll to form for better UX
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
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
    function fetchProducts(url = '?', search = '') {
        $.ajax({
            url: url,
            type: 'GET',
            data: { search: search }, // <-- use 'search' to match controller
            success: function (data) {
                $('#product-table').html(data);
            },
            error: function () {
                alert('Something went wrong while fetching products.');
            }
        });
    }

    // Live search
    $('#live-search').on('input', function () {
        let search = $(this).val();
        fetchProducts('?page=1', search); // reset to page 1 when searching
    });

    // Pagination
    $(document).on('click', '#product-table .pagination a', function (e) {
        e.preventDefault();
        let url = $(this).attr('href');
        let search = $('#live-search').val();
        fetchProducts(url, search);
    });
});

</script>

    
@endsection
