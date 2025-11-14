@extends('layouts.seller-form-layout')

@section('title', 'Edit Produk - UMKM AKRAB')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/penjual/tambah_produk.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Edit Produk</h1>
            
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('penjual.produk.update', $product->id) }}" method="POST" enctype="multipart/form-data" id="productForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Informasi Dasar Produk -->
                                <div class="mb-4">
                                    <h5>Informasi Dasar Produk</h5>
                                    <hr>
                                    
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nama Produk *</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name', $product->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Deskripsi Produk *</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                                  id="description" name="description" rows="5" required>{{ old('description', $product->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <!-- Gambar Produk -->
                                <div class="mb-4">
                                    <h5>Gambar Produk</h5>
                                    <hr>
                                    
                                    <div class="mb-3">
                                        <label for="images" class="form-label">Tambah Foto Produk</label>
                                        <input type="file" class="form-control @error('images') is-invalid @enderror" 
                                               id="images" name="images[]" multiple accept="image/*">
                                        <div class="form-text">Anda dapat memilih beberapa gambar. Format yang didukung: JPG, PNG, GIF. Maksimal 2MB per gambar.</div>
                                        @error('images')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @error('images.*')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <!-- Gambar yang sudah ada -->
                                    @if($product->images->count() > 0)
                                    <div class="mb-3">
                                        <label class="form-label">Gambar Saat Ini</label>
                                        <div class="row g-2">
                                            @foreach($product->images as $image)
                                            <div class="col-6 col-md-4 col-lg-3 mb-2">
                                                <div class="position-relative">
                                                    <img src="{{ asset('storage/' . $image->image_path) }}" 
                                                         class="img-thumbnail" style="height: 150px; object-fit: cover;" 
                                                         alt="{{ $product->name }}">
                                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" 
                                                            onclick="removeImage({{ $image->id }}, this)">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                    
                                    <!-- Preview Gambar Baru -->
                                    <div id="imagePreview" class="row g-2 mt-2"></div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <!-- Harga dan Stok -->
                                <div class="mb-4">
                                    <h5>Harga & Stok</h5>
                                    <hr>
                                    
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Harga (Rp) *</label>
                                        <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                               id="price" name="price" value="{{ old('price', $product->price) }}" min="0" step="100" required>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="stock" class="form-label">Stok *</label>
                                        <input type="number" class="form-control @error('stock') is-invalid @enderror" 
                                               id="stock" name="stock" value="{{ old('stock', $product->stock) }}" min="0" required>
                                        @error('stock')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="weight" class="form-label">Berat (gram) *</label>
                                        <input type="number" class="form-control @error('weight') is-invalid @enderror" 
                                               id="weight" name="weight" value="{{ old('weight', $product->weight) }}" min="0" step="1" required>
                                        <div class="form-text">Berat dalam gram</div>
                                        @error('weight')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <!-- Kategori -->
                                <div class="mb-4">
                                    <h5>Kategori</h5>
                                    <hr>
                                    
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label">Kategori *</label>
                                        <select class="form-select @error('category_id') is-invalid @enderror" 
                                                id="category_id" name="category_id" required>
                                            <option value="">Pilih Kategori</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="subcategory" class="form-label">Sub-Kategori</label>
                                        <select class="form-select @error('subcategory') is-invalid @enderror"
                                                id="subcategory" name="subcategory">
                                            <option value="">Pilih Sub-Kategori (Opsional)</option>
                                            <!-- Sub-kategori akan diisi berdasarkan kategori yang dipilih -->
                                        </select>
                                        @error('subcategory')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="warranty" class="form-label">Garansi</label>
                                        <input type="text" class="form-control @error('warranty') is-invalid @enderror"
                                               id="warranty" name="warranty" value="{{ old('warranty', $product->warranty) }}"
                                               placeholder="Contoh: 1 tahun, sesuai ketentuan pabrikan, dll">
                                        @error('warranty')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="origin" class="form-label">Asal Wilayah (Kecamatan di Banyuwangi)</label>
                                        <select class="form-select @error('origin') is-invalid @enderror"
                                                id="origin" name="origin">
                                            <option value="">Pilih Kecamatan di Banyuwangi</option>
                                            <option value="Banyuwangi" {{ old('origin', $product->origin) == 'Banyuwangi' ? 'selected' : '' }}>Banyuwangi</option>
                                            <option value="Giri" {{ old('origin', $product->origin) == 'Giri' ? 'selected' : '' }}>Giri</option>
                                            <option value="Wongsorejo" {{ old('origin', $product->origin) == 'Wongsorejo' ? 'selected' : '' }}>Wongsorejo</option>
                                            <option value="Songgon" {{ old('origin', $product->origin) == 'Songgon' ? 'selected' : '' }}>Songgon</option>
                                            <option value="Sempu" {{ old('origin', $product->origin) == 'Sempu' ? 'selected' : '' }}>Sempu</option>
                                            <option value="Kalipuro" {{ old('origin', $product->origin) == 'Kalipuro' ? 'selected' : '' }}>Kalipuro</option>
                                            <option value="Siliragung" {{ old('origin', $product->origin) == 'Siliragung' ? 'selected' : '' }}>Siliragung</option>
                                            <option value="Srono" {{ old('origin', $product->origin) == 'Srono' ? 'selected' : '' }}>Srono</option>
                                            <option value="Glenmore" {{ old('origin', $product->origin) == 'Glenmore' ? 'selected' : '' }}>Glenmore</option>
                                            <option value="Singojuruh" {{ old('origin', $product->origin) == 'Singojuruh' ? 'selected' : '' }}>Singojuruh</option>
                                            <option value="Rogojampi" {{ old('origin', $product->origin) == 'Rogojampi' ? 'selected' : '' }}>Rogojampi</option>
                                            <option value="Kabat" {{ old('origin', $product->origin) == 'Kabat' ? 'selected' : '' }}>Kabat</option>
                                            <option value="Genteng" {{ old('origin', $product->origin) == 'Genteng' ? 'selected' : '' }}>Genteng</option>
                                            <option value="Sukojati" {{ old('origin', $product->origin) == 'Sukojati' ? 'selected' : '' }}>Sukojati</option>
                                            <option value="Glagah" {{ old('origin', $product->origin) == 'Glagah' ? 'selected' : '' }}>Glagah</option>
                                            <option value="Licin" {{ old('origin', $product->origin) == 'Licin' ? 'selected' : '' }}>Licin</option>
                                            <option value="Tegaldlimo" {{ old('origin', $product->origin) == 'Tegaldlimo' ? 'selected' : '' }}>Tegaldlimo</option>
                                            <option value="Blimbing" {{ old('origin', $product->origin) == 'Blimbing' ? 'selected' : '' }}>Blimbing</option>
                                            <option value="Muncar" {{ old('origin', $product->origin) == 'Muncar' ? 'selected' : '' }}>Muncar</option>
                                            <option value="Kalibaru" {{ old('origin', $product->origin) == 'Kalibaru' ? 'selected' : '' }}>Kalibaru</option>
                                            <option value="Gambiran" {{ old('origin', $product->origin) == 'Gambiran' ? 'selected' : '' }}>Gambiran</option>
                                            <option value="Sempol" {{ old('origin', $product->origin) == 'Sempol' ? 'selected' : '' }}>Sempol</option>
                                            <option value="Tegalsari" {{ old('origin', $product->origin) == 'Tegalsari' ? 'selected' : '' }}>Tegalsari</option>
                                        </select>
                                        @error('origin')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Pilih kecamatan asal produk di Kabupaten Banyuwangi</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="material" class="form-label">Material</label>
                                        <input type="text" class="form-control @error('material') is-invalid @enderror"
                                               id="material" name="material" value="{{ old('material', $product->material) }}"
                                               placeholder="Contoh: Katun, Kulit, Kayu, dll">
                                        @error('material')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="size" class="form-label">Ukuran</label>
                                        <input type="text" class="form-control @error('size') is-invalid @enderror"
                                               id="size" name="size" value="{{ old('size', $product->size) }}"
                                               placeholder="Contoh: S, M, L, XL, 30x20x10 cm, 100 cm x 50 cm, dll">
                                        @error('size')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="color" class="form-label">Warna</label>
                                        <input type="text" class="form-control @error('color') is-invalid @enderror"
                                               id="color" name="color" value="{{ old('color', $product->color) }}"
                                               placeholder="Contoh: Merah, Biru, Hitam-Putih, dll">
                                        @error('color')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Spesifikasi & Fitur -->
                                <div class="mb-4">
                                    <h5>Spesifikasi & Fitur</h5>
                                    <hr>

                                    <div class="mb-3">
                                        <label for="specifications" class="form-label">Spesifikasi Produk</label>
                                        <textarea class="form-control @error('specifications') is-invalid @enderror"
                                                  id="specifications" name="specifications"
                                                  placeholder="Contoh: Daya tahan baterai 12 jam, Tekanan maksimal 2 bar, dll">{{ old('specifications', $product->specifications ? implode("\n", (array)$product->specifications) : '') }}</textarea>
                                        @error('specifications')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Gunakan baris baru untuk setiap spesifikasi</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="features" class="form-label">Fitur Produk</label>
                                        <textarea class="form-control @error('features') is-invalid @enderror"
                                                  id="features" name="features"
                                                  placeholder="Contoh: Tahan air, Mudah dibersihkan, Dilengkapi remote, dll">{{ old('features', $product->features ? implode("\n", (array)$product->features) : '') }}</textarea>
                                        @error('features')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Gunakan baris baru untuk setiap fitur</div>
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="mb-4">
                                    <h5>Status</h5>
                                    <hr>
                                    
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status Produk</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                                            <option value="draft" {{ old('status', $product->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                            <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tombol Aksi -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('penjual.produk') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            <div>
                                <button type="button" class="btn btn-danger me-2" onclick="confirmDelete()">
                                    <i class="bi bi-trash"></i> Hapus Produk
                                </button>
                                <button type="submit" class="btn btn-primary" id="saveProductBtn">
                                    <span id="buttonText">Update Produk</span>
                                    <span id="loadingSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                </button>
                            </div>
                        </div>
                    </form>
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
// Definisikan sub-kategori berdasarkan kategori utama
const subcategories = {
    'Kuliner': [
        { value: 'makanan-berat', label: 'Makanan Berat' },
        { value: 'camilan', label: 'Camilan' },
        { value: 'minuman', label: 'Minuman' },
        { value: 'bumbu-bahan-masak', label: 'Bumbu & Bahan Masak' },
        { value: 'kue-kering', label: 'Kue & Kering' },
        { value: 'makanan-ringan', label: 'Makanan Ringan' },
        { value: 'produk-olahan-susu', label: 'Produk Olahan Susu' }
    ],
    'Fashion': [
        { value: 'pakaian-pria', label: 'Pakaian Pria' },
        { value: 'pakaian-wanita', label: 'Pakaian Wanita' },
        { value: 'pakaian-anak', label: 'Pakaian Anak' },
        { value: 'aksesoris', label: 'Aksesoris' },
        { value: 'tas', label: 'Tas' },
        { value: 'sepatu', label: 'Sepatu' },
        { value: 'perhiasan', label: 'Perhiasan' }
    ],
    'Kerajinan Tangan': [
        { value: 'kerajinan-logam', label: 'Kerajinan Logam' },
        { value: 'kerajinan-kayu', label: 'Kerajinan Kayu' },
        { value: 'kerajinan-kertas', label: 'Kerajinan Kertas' },
        { value: 'kerajinan-kain', label: 'Kerajinan Kain' },
        { value: 'kerajinan-tanah-liat', label: 'Kerajinan Tanah Liat' },
        { value: 'souvenir-hadiah', label: 'Souvenir & Hadiah' },
        { value: 'alat-tulis-kerajinan', label: 'Alat Tulis Kerajinan' }
    ],
    'Produk Berkebun': [
        { value: 'tanaman-hias', label: 'Tanaman Hias' },
        { value: 'tanaman-buah', label: 'Tanaman Buah' },
        { value: 'tanaman-sayur', label: 'Tanaman Sayur' },
        { value: 'tanaman-obat', label: 'Tanaman Obat' },
        { value: 'pupuk-nutrisi', label: 'Pupuk & Nutrisi Tanaman' },
        { value: 'peralatan-berkebun', label: 'Peralatan Berkebun' },
        { value: 'pot-media-tanam', label: 'Pot & Media Tanam' }
    ],
    'Produk Kesehatan': [
        { value: 'vitamin-suplemen', label: 'Vitamin & Suplemen' },
        { value: 'obat-herbal', label: 'Obat Herbal' },
        { value: 'alat-kesehatan', label: 'Alat Kesehatan' },
        { value: 'produk-perawatan-diri', label: 'Produk Perawatan Diri' },
        { value: 'produk-terapi', label: 'Produk Terapi' },
        { value: 'diet-nutrisi', label: 'Produk Diet & Nutrisi' },
        { value: 'alat-bantu-kesehatan', label: 'Alat Bantu Kesehatan' }
    ],
    'Mainan': [
        { value: 'mainan-edukatif', label: 'Mainan Edukatif' },
        { value: 'mainan-bayi', label: 'Mainan Bayi' },
        { value: 'mainan-anak', label: 'Mainan Anak' },
        { value: 'mainan-outdoor', label: 'Mainan Outdoor' },
        { value: 'boneka-figure', label: 'Boneka & Action Figure' },
        { value: 'permainan-tradisional', label: 'Permainan Tradisional' },
        { value: 'puzzle-permainan-meja', label: 'Puzzle & Permainan Meja' }
    ],
    'Hampers': [
        { value: 'hampers-makanan', label: 'Hampers Makanan' },
        { value: 'hampers-minuman', label: 'Hampers Minuman' },
        { value: 'hampers-kecantikan', label: 'Hampers Kecantikan' },
        { value: 'hampers-fashion', label: 'Hampers Fashion' },
        { value: 'hampers-bayi', label: 'Hampers Bayi' },
        { value: 'hampers-kesehatan', label: 'Hampers Kesehatan' },
        { value: 'hampers-buah-sayur', label: 'Hampers Buah & Sayur' },
        { value: 'hampers-hari-raya', label: 'Hampers Hari Raya' }
    ]
};

document.addEventListener('DOMContentLoaded', function() {
    // Image preview functionality
    const imagesInput = document.getElementById('images');
    const imagePreview = document.getElementById('imagePreview');
    
    imagesInput.addEventListener('change', function() {
        imagePreview.innerHTML = '';
        
        if (this.files) {
            for (let i = 0; i < this.files.length; i++) {
                const file = this.files[i];
                if (!file.type.match('image.*')) {
                    continue;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const colDiv = document.createElement('div');
                    colDiv.className = 'col-6 col-md-4';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'img-thumbnail';
                    img.style.height = '150px';
                    img.style.objectFit = 'cover';
                    
                    colDiv.appendChild(img);
                    imagePreview.appendChild(colDiv);
                }
                reader.readAsDataURL(file);
            }
        }
    });
    
    // Event listener untuk perubahan kategori di edit form
    const categorySelect = document.getElementById('category_id');
    const subcategorySelect = document.getElementById('subcategory');

    // Fungsi untuk mengisi sub-kategori
    function updateSubcategories(categoryName) {
        // Kosongkan sub-kategori sebelumnya
        subcategorySelect.innerHTML = '<option value="">Pilih Sub-Kategori (Opsional)</option>';

        // Tambahkan sub-kategori berdasarkan kategori yang dipilih
        if (subcategories[categoryName]) {
            subcategories[categoryName].forEach(subcat => {
                const option = document.createElement('option');
                option.value = subcat.value;
                option.textContent = subcat.label;

                // Jika produk memiliki sub-kategori saat ini, pilih opsi yang sesuai
                if ("{{ old('subcategory', $product->subcategory) }}" === subcat.value) {
                    option.selected = true;
                }

                subcategorySelect.appendChild(option);
            });
        }

        // Jika tidak ada sub-kategori untuk kategori ini, tambahkan pesan
        if (!subcategories[categoryName] || subcategories[categoryName].length === 0) {
            const option = document.createElement('option');
            option.value = '';
            option.textContent = 'Tidak ada sub-kategori untuk kategori ini';
            option.disabled = true;
            subcategorySelect.appendChild(option);
        }
    }
    
    // Event listener untuk perubahan kategori
    categorySelect.addEventListener('change', function() {
        const selectedCategoryName = this.options[this.selectedIndex].text;
        updateSubcategories(selectedCategoryName);
    });
    
    // Saat halaman dimuat, isi sub-kategori berdasarkan kategori produk saat ini
    window.addEventListener('load', function() {
        const selectedCategory = categorySelect.options[categorySelect.selectedIndex].text;
        updateSubcategories(selectedCategory);
    });
    
    // Remove image functionality
    function removeImage(imageId, button) {
        if (confirm('Apakah Anda yakin ingin menghapus gambar ini?')) {
            fetch(`/penjual/product-image/${imageId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the image container from DOM
                    button.closest('.col-6').remove();
                    alert('Gambar berhasil dihapus');
                } else {
                    alert('Gagal menghapus gambar: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menghapus gambar');
            });
        }
    }
    
    // Window function to make removeImage accessible globally
    window.removeImage = removeImage;
    
    // Form submission with loading state
    const productForm = document.getElementById('productForm');
    const saveProductBtn = document.getElementById('saveProductBtn');
    const buttonText = document.getElementById('buttonText');
    const loadingSpinner = document.getElementById('loadingSpinner');
    
    productForm.addEventListener('submit', function(e) {
        // Show loading state
        buttonText.textContent = 'Memperbarui...';
        loadingSpinner.classList.remove('d-none');
        saveProductBtn.disabled = true;
        
        // Allow form to submit normally
        return true;
    });
    
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