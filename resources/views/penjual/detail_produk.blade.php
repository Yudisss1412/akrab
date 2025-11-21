@extends('layouts.seller-layout')

@section('title', 'Detail Produk - UMKM AKRAB')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Detail Produk</h1>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-5">
                                    <!-- Gambar Utama Produk -->
                                    @if($product->images->count() > 0)
                                        <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" 
                                             class="img-fluid rounded" alt="{{ $product->name }}">
                                    @else
                                        <img src="https://via.placeholder.com/400x400?text=Tidak+Ada+Gambar" 
                                             class="img-fluid rounded" alt="{{ $product->name }}">
                                    @endif
                                    
                                    <!-- Thumbnail Gambar -->
                                    @if($product->images->count() > 1)
                                        <div class="row mt-3 g-2">
                                            @foreach($product->images->skip(1) as $image)
                                                <div class="col-3">
                                                    <img src="{{ asset('storage/' . $image->image_path) }}" 
                                                         class="img-fluid rounded cursor-pointer" 
                                                         alt="{{ $product->name }}" 
                                                         onclick="changeMainImage(this.src)">
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="col-md-7">
                                    <h2>{{ $product->name }}</h2>
                                    
                                    <div class="mb-3">
                                        <span class="badge bg-{{ $product->status === 'aktif' ? 'success' : ($product->status === 'draft' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($product->status) }}
                                        </span>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <h4 class="text-primary">Rp {{ number_format($product->price, 0, ',', '.') }}</h4>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <p class="text-muted">
                                            <i class="bi bi-folder"></i> 
                                            {{ $product->category ? $product->category->name : 'Tidak Ada Kategori' }}
                                        </p>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <p><strong>Stok:</strong> {{ $product->stock }}</p>
                                        </div>
                                        <div class="col-6">
                                            <p><strong>Berat:</strong> {{ $product->weight }} gram</p>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <a href="{{ route('penjual.produk.edit', $product->id) }}" class="btn btn-primary">
                                            <i class="bi bi-pencil"></i> Edit Produk
                                        </a>
                                        <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                                            <i class="bi bi-trash"></i> Hapus Produk
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <h5>Deskripsi Produk</h5>
                                <hr>
                                <p>{{ $product->description }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Varian Produk -->
                    @if($product->variants->count() > 0)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Varian Produk</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Nama Varian</th>
                                            <th>Tambahan Harga</th>
                                            <th>Stok</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($product->variants as $variant)
                                        <tr>
                                            <td>{{ $variant->name }}</td>
                                            <td>Rp {{ number_format($variant->additional_price, 0, ',', '.') }}</td>
                                            <td>{{ $variant->stock }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                
                <div class="col-md-4">
                    <!-- Statistik Produk -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Statistik Produk</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6 mb-3">
                                    <div class="stat-number">{{ $totalSales }}</div>
                                    <div class="stat-label">Terjual</div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="stat-number text-success">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                                    <div class="stat-label">Pendapatan</div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-number">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($averageRating))
                                                <i class="bi bi-star-fill text-warning"></i>
                                            @elseif($i - $averageRating <= 0.5)
                                                <i class="bi bi-star-half text-warning"></i>
                                            @else
                                                <i class="bi bi-star text-warning"></i>
                                            @endif
                                        @endfor
                                        <div class="stat-label mt-1">{{ number_format($averageRating, 1) }}/5</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-number">{{ $totalReviews }}</div>
                                    <div class="stat-label">Ulasan</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Ulasan Terbaru -->
                    @if($product->reviews->count() > 0)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Ulasan Terbaru</h5>
                        </div>
                        <div class="card-body">
                            @foreach($product->reviews->take(3) as $review)
                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between">
                                    <strong>{{ $review->user->name }}</strong>
                                    <span class="text-muted">{{ $review->created_at->format('d M Y') }}</span>
                                </div>
                                <div class="mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            <i class="bi bi-star-fill text-warning"></i>
                                        @else
                                            <i class="bi bi-star text-warning"></i>
                                        @endif
                                    @endfor
                                </div>
                                <p class="mb-0">{{ $review->comment ?? 'Tidak ada komentar' }}</p>
                            </div>
                            @endforeach
                            
                            @if($product->reviews->count() > 3)
                            <a href="#" class="btn btn-sm btn-outline-primary">Lihat Semua Ulasan</a>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus produk "<strong>{{ $product->name }}</strong>"? Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Hapus Produk</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to change main image
    function changeMainImage(src) {
        document.querySelector('.img-fluid.rounded').src = src;
    }
    
    // Make function globally accessible
    window.changeMainImage = changeMainImage;
    
    // Delete confirmation
    function confirmDelete() {
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
    
    // Confirm delete action
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        fetch("{{ route('penjual.produk.destroy', $product->id) }}", {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Produk berhasil dihapus');
                window.location.href = "{{ route('penjual.produk') }}";
            } else {
                alert('Gagal menghapus produk: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus produk');
        });
    });
    
    // Make functions globally accessible
    window.confirmDelete = confirmDelete;
});
</script>
@endpush