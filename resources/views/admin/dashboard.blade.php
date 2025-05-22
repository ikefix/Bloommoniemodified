@extends('layouts.adminapp')

@section('admincontent')
    <h1>Dashboard</h1>

<div class="container">
    <div class="dashboard-stats">
        <div class="stat-box">
            <h4>🛒 Total Sales For The Week</h4>
            <p>₦{{ $totalSalesThisWeek }}</p>
        </div>

        <div class="stat-box">
            <h4>💰 Revenue Today</h4>
            <p>₦{{ $totalRevenueToday }}</p>
        </div>

        <div class="stat-box">
            <h4>📦 Products in Stock</h4>
            <p>{{ $productsInStock }}</p>
        </div>
        <div class="stat-box">
            <h4>🧾 Top Selling Products</h4>
            <ul>
                @foreach($topSelling as $item)
                    <li>{{ $item->product->name ?? 'Unknown Product' }} - Sold: {{ $item->total_sold }}</li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="chart-container-flex">
        <div class="chart-box">
            <h4>📈 Sales Trend</h4>
            <canvas id="salesTrendChart"></canvas>
        </div>
    
        <div class="chart-box">
            <h4>🥧 Top Selling Products</h4>
            <canvas id="topSellingProductsChart"></canvas>
        </div>
    </div>
    
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const salesTrendCtx = document.getElementById('salesTrendChart').getContext('2d');
        const salesTrendChart = new Chart(salesTrendCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($salesTrendLabels ?? []) !!},
                datasets: [{
                    label: 'Sales Trend',
                    data: {!! json_encode($salesTrendData ?? []) !!},
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    fill: true
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        const topSellingProductsCtx = document.getElementById('topSellingProductsChart').getContext('2d');
        const topSellingProductsChart = new Chart(topSellingProductsCtx, {
            type: 'pie',
            data: {
                labels: {!! json_encode($topSellingProductNames ?? []) !!},
                datasets: [{
                    label: 'Top Selling Products',
                    data: {!! json_encode($topSellingProductSales ?? []) !!},
                    backgroundColor: [
                        'rgba(30, 136, 229, 0.9)',   // Electric blue
                        'rgba(94, 53, 177, 0.9)',    // Deep purple
                        'rgba(255, 87, 34, 0.9)',    // Burnt orange
                        'rgba(46, 125, 50, 0.9)',    // Dark green
                        'rgba(255, 193, 7, 0.9)',    // Gold pop
                        'rgba(233, 30, 99, 0.9)',    // Vivid pink
                        'rgba(0, 172, 193, 0.9)'     // Teal sharp
                    ],
                    hoverOffset: 4
                }]
            }
        });
    </script>
@endsection
