@extends('layouts.seller-layout')

@section('title', 'Dashboard Penjual - UMKM AKRAB')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Dashboard Penjual</h1>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Produk</h5>
                            <h2>45</h2>
                            <p class="card-text">Produk Aktif</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Penjualan</h5>
                            <h2>128</h2>
                            <p class="card-text">Bulan Ini</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Pendapatan</h5>
                            <h2>Rp 4.5M</h2>
                            <p class="card-text">Bulan Ini</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">Pesanan Perlu Diproses</h5>
                            <h2 id="pending-orders-count">Memuat...</h2>
                            <p class="card-text">Status: <span id="order-status">Memuat...</span></p>
                            <div class="small mt-2" style="color: rgba(255,255,255,0.7);">* Data diperbarui otomatis</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Statistik Penjualan</h5>
                        </div>
                        <div class="card-body">
                            <p>Di sini akan ditampilkan grafik statistik penjualan toko Anda</p>
                            <div class="placeholder" style="height: 300px; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                <span class="text-muted">Grafik Statistik Penjualan</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Produk Terlaris</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold">Produk A</div>
                                        <small class="text-muted">Terjual 24 buah</small>
                                    </div>
                                    <span class="badge bg-success rounded-pill">24</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold">Produk B</div>
                                        <small class="text-muted">Terjual 18 buah</small>
                                    </div>
                                    <span class="badge bg-success rounded-pill">18</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold">Produk C</div>
                                        <small class="text-muted">Terjual 15 buah</small>
                                    </div>
                                    <span class="badge bg-success rounded-pill">15</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold">Produk D</div>
                                        <small class="text-muted">Terjual 12 buah</small>
                                    </div>
                                    <span class="badge bg-success rounded-pill">12</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold">Produk E</div>
                                        <small class="text-muted">Terjual 9 buah</small>
                                    </div>
                                    <span class="badge bg-success rounded-pill">9</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@section('scripts')
<script>
    // Ambil jumlah tugas mendesak secara dinamis
    function fetchUrgentTasks() {
        console.log('Fetching urgent tasks...');
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        const csrfTokenValue = csrfToken ? csrfToken.getAttribute('content') : '';

        fetch('{{ route('penjual.urgent.tasks') }}', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfTokenValue
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Received data:', data);
            if (data && data.success && data.urgent_tasks) {
                const pendingOrdersCount = data.urgent_tasks.pending_orders;
                console.log('Pending orders count:', pendingOrdersCount);

                // Pastikan element tersedia sebelum update
                const countElement = document.getElementById('pending-orders-count');
                if (countElement) {
                    countElement.textContent = pendingOrdersCount;
                }

                // Update status text
                const orderStatusElement = document.getElementById('order-status');
                if (orderStatusElement) {
                    if (pendingOrdersCount > 0) {
                        orderStatusElement.textContent = 'Perlu Ditindak';
                        orderStatusElement.className = 'text-success';
                    } else {
                        orderStatusElement.textContent = 'Tidak Ada';
                        orderStatusElement.className = 'text-muted';
                    }
                }
            } else {
                console.error('Error fetching urgent tasks:', data.message || 'Invalid response format');
                if(data.message) {
                    console.error('Error message:', data.message);
                }
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            // Tampilkan error di UI untuk debugging
            const countElement = document.getElementById('pending-orders-count');
            if (countElement) {
                countElement.textContent = 'Err';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, fetching urgent tasks...');
        // Panggil fungsi untuk pertama kali
        fetchUrgentTasks();

        // Refresh setiap 30 detik
        const intervalId = setInterval(fetchUrgentTasks, 30000);
        console.log('Auto-refresh started with interval ID:', intervalId);
    });
</script>
@endsection