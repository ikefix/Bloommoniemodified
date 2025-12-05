@extends('layouts.adminapp')

@section('admincontent')
<div class="container">
    <h2 class="mb-4"><i class='bx bx-package'></i> Barcode Manager</h2>

    <!-- Generate barcode from code -->
    <div class="card p-3 mb-4">
        <h5>Generate Barcode from Code</h5>
        <div class="input-group mb-3">
            <input type="text" id="manual-barcode" class="form-control" placeholder="Enter existing barcode number">
            <button type="button" id="generate-manual-barcode" class="btn btn-primary">Generate Barcode</button>
        </div>
        <div id="manual-barcode-preview" class="text-center" style="margin-bottom:20px;"></div>
    </div>

    <!-- Saved Barcodes Section -->
    <div class="card p-3">
        <h5>Saved Barcodes (Local Storage)</h5>
        <div id="saved-barcodes" class="d-flex flex-wrap gap-3 mt-3 flex-wrap">
            <!-- Generated barcodes will appear here -->
        </div>


        <div class="mt-4">
            <button id="download-all-barcodes" class="btn btn-success me-2" style="display:none;">Download All (ZIP)</button>
            <button id="clear-all-barcodes" class="btn btn-danger">Clear All</button>
        </div>
    </div>
</div>

<!-- ✅ Libraries -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

<script>
function renderSavedBarcodes() {
    const saved = JSON.parse(localStorage.getItem('barcodes') || '[]');
    const container = document.getElementById('saved-barcodes');
    container.innerHTML = '';

    saved.forEach(item => {
        container.innerHTML += `
            <div class="text-center border p-2 rounded">
                <img src="${item.image}" alt="${item.code}" height="80" style="border:1px solid #ccc; padding:5px;">
                <p class="mt-2 fw-bold">${item.name || 'Unnamed Product'}</p>
            </div>
        `;
    });

    document.getElementById('download-all-barcodes').style.display = saved.length ? 'inline-block' : 'none';
}


// ✅ Generate barcode manually
// ✅ Generate barcode manually
document.getElementById('generate-manual-barcode').addEventListener('click', async function() {
    const code = document.getElementById('manual-barcode').value.trim();
    if (!code) return alert('Please enter a barcode number');

    // 🔍 Fetch product name from database
    let productName = 'Unknown Product';
    try {
        const res = await fetch(`/get-product/${code}`);
        const data = await res.json();
        if (data.success && data.name) {
            productName = data.name;
        }
    } catch (error) {
        console.error('Error fetching product:', error);
    }

    // ✅ Generate barcode image
    const canvas = document.createElement('canvas');
    JsBarcode(canvas, code, {
        format: "CODE128",
        lineColor: "#000",
        width: 2,
        height: 80,
        displayValue: true
    });
    const imgData = canvas.toDataURL('image/png');

    // ✅ Show barcode + product name in preview
    const preview = document.getElementById('manual-barcode-preview');
    preview.innerHTML = `
        <div style="text-align:center">
            <img src="${imgData}" alt="${code}" height="80" style="border:1px solid #ccc; padding:5px;"><br>
            <strong>${productName}</strong>
        </div>
    `;

    // ✅ Save in localStorage with product name
    let saved = JSON.parse(localStorage.getItem('barcodes') || '[]');
    if (!saved.some(b => b.code === code)) {
        saved.push({ code: code, image: imgData, name: productName });
        localStorage.setItem('barcodes', JSON.stringify(saved));
    }

    renderSavedBarcodes();
});


// ✅ Download all barcodes as ZIP
document.getElementById('download-all-barcodes').addEventListener('click', async function() {
    const saved = JSON.parse(localStorage.getItem('barcodes') || '[]');
    if (saved.length === 0) return alert('No barcodes to download');

    const zip = new JSZip();
    saved.forEach(item => {
        const imgData = item.image.split(',')[1];
        zip.file(`${item.code}.png`, atob(imgData), { binary: true });
    });

    const blob = await zip.generateAsync({ type: 'blob' });
    saveAs(blob, 'barcodes.zip');
});

// ✅ Clear all barcodes
document.getElementById('clear-all-barcodes').addEventListener('click', function() {
    if (confirm('Are you sure you want to clear all saved barcodes?')) {
        localStorage.removeItem('barcodes');
        renderSavedBarcodes();
    }
});

// ✅ On page load
renderSavedBarcodes();
</script>
@endsection
