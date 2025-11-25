@extends('layouts.app')

@section('title', 'Checkout')

@section('header')
  @include('components.customer.header.header')
@endsection

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/customer/transaksi/checkout.css') }}"/>
@endpush

@section('content')
  <main class="checkout-page">
    <div class="container">
      <div class="page-header">
        <h1>Checkout</h1>
        <div class="progress-steps">
          <div class="step active">
            <span class="step-number">1</span>
            <span class="step-label">Alamat</span>
          </div>
          <div class="step">
            <span class="step-number">2</span>
            <span class="step-label">Pengiriman</span>
          </div>
          <div class="step">
            <span class="step-number">3</span>
            <span class="step-label">Pembayaran</span>
          </div>
        </div>
      </div>

      <form id="checkoutForm" action="{{ route('checkout.process') }}" method="POST">
        @csrf
        <!-- Field tersembunyi untuk metode pengiriman default -->
        <input type="hidden" name="shipping_method" value="reguler" id="hiddenShippingMethod" />

        <!-- Debug: Menampilkan data yang dikirim -->
        <div style="display: none;">
          <input type="hidden" name="debug_trace" value="checkout_form" />
        </div>

        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <script>
          // Jika ada error validasi, tampilkan form alamat dalam mode edit
          @if($errors->any())
            document.addEventListener('DOMContentLoaded', function() {
              document.getElementById('alamatCard').style.display = 'none';
              document.getElementById('alamatFormSection').style.display = 'block';
              document.getElementById('editAlamatBtn').innerHTML = '<i class="bi bi-save"></i>';
            });
          @endif
        </script>

        <div class="checkout-content">
          <div class="checkout-left">
            <!-- Alamat Pengiriman -->
            <section class="alamat-section">
              <div class="section-header">
                <h2>Alamat Pengiriman</h2>
                <button type="button" class="btn-edit" id="editAlamatBtn">
                  <i class="bi bi-pencil"></i>
                </button>
              </div>

              <div class="alamat-card" id="alamatCard">
                <div class="alamat-content">
                  <div class="primary-badge">Utama</div>
                  <h3 id="alamatNama">{{ $user->name ?? 'Nama Pengguna' }}</h3>
                  <p id="alamatDetail">
                    @if($user)
                      @php
                        $alamatParts = [];

                        // Tambahkan full_address jika ada
                        if($user->full_address) {
                            $alamatParts[] = $user->full_address;
                        }

                        // Tambahkan address jika berbeda dari full_address
                        if($user->address && $user->address !== $user->full_address) {
                            $alamatParts[] = $user->address;
                        }

                        // Tambahkan ward jika ada dan belum ada di alamat
                        if($user->ward) {
                            $alamatParts[] = $user->ward;
                        }

                        // Tambahkan district jika ada dan belum ada di alamat
                        if($user->district) {
                            $alamatParts[] = $user->district;
                        }

                        // Tambahkan city jika ada dan belum ada di alamat
                        if($user->city) {
                            $alamatParts[] = $user->city;
                        }

                        // Tambahkan province jika ada dan belum ada di alamat
                        if($user->province) {
                            $alamatParts[] = $user->province;
                        }

                        // Hapus elemen duplikat dan kosong dari array
                        $uniqueAlamatParts = array_unique(array_filter($alamatParts));
                        $fullAlamat = implode(', ', $uniqueAlamatParts);
                      @endphp

                      @if(count($uniqueAlamatParts) > 0)
                        {{ $fullAlamat }}
                      @else
                        Alamat tidak lengkap
                      @endif
                    @else
                      User tidak ditemukan
                    @endif
                  </p>
                  <p class="alamat-phone" id="alamatPhone">{{ $user->phone ?? 'Nomor telepon tidak tersedia' }}</p>

                  <!-- Debug: Menampilkan data alamat user -->
                  <div style="font-size: 0.8em; color: #666; margin-top: 10px; padding: 5px; background-color: #f9f9f9; border-radius: 4px; display: block;">
                    <p>Debug: Full address: {{ $user->full_address ?? 'kosong' }}</p>
                    <p>Debug: Address: {{ $user->address ?? 'kosong' }}</p>
                    <p>Debug: Province: {{ $user->province ?? 'kosong' }}</p>
                    <p>Debug: City: {{ $user->city ?? 'kosong' }}</p>
                    <p>Debug: District: {{ $user->district ?? 'kosong' }}</p>
                    <p>Debug: Ward: {{ $user->ward ?? 'kosong' }}</p>
                  </div>
                </div>
              </div>

              <!-- Form untuk alamat pengiriman -->
              <div class="alamat-form" id="alamatFormSection" style="display: none;">
                <div class="form-group field">
                  <input type="text" id="recipient_name" name="recipient_name"
                         value="{{ old('recipient_name', $user->name ?? 'Nama Pengguna') }}" required placeholder=" " />
                  <label for="recipient_name">Nama Penerima</label>
                </div>

                <div class="form-group field">
                  <input type="tel" id="phone" name="phone"
                         value="{{ old('phone', $user->phone ?? '') }}" required placeholder=" " />
                  <label for="phone">Nomor Telepon</label>
                </div>

                <div class="form-group field">
                  <input type="text" id="province" name="province"
                         value="{{ old('province', $user->province ?? 'Jawa Barat') }}" required placeholder=" " />
                  <label for="province">Provinsi</label>
                </div>

                <div class="form-group field">
                  <input type="text" id="city" name="city"
                         value="{{ old('city', $user->city ?? 'Kota Bandung') }}" required placeholder=" " />
                  <label for="city">Kota/Kabupaten</label>
                </div>

                <div class="form-group field">
                  <input type="text" id="district" name="district"
                         value="{{ old('district', $user->district ?? 'Lembursitu') }}" required placeholder=" " />
                  <label for="district">Kecamatan</label>
                </div>

                <div class="form-group field">
                  <input type="text" id="ward" name="ward"
                         value="{{ old('ward', $user->ward ?? 'Sukajadi') }}" required placeholder=" " />
                  <label for="ward">Kelurahan</label>
                </div>

                <div class="form-group field">
                  <textarea id="full_address" name="full_address" rows="3" required placeholder=" ">{{ old('full_address', $user->full_address ?? $user->address ?? 'Jl. Merdeka No. 123') }}</textarea>
                  <label for="full_address">Alamat Lengkap</label>
                </div>
              </div>
            </section>

            <!-- Ringkasan Pesanan -->
            <section class="ringkasan-section">
              <div class="section-header">
                <h2>Ringkasan Pesanan</h2>
              </div>

              <div class="ringkasan-content">
                @if($cartItems->count() > 0)
                  @foreach($cartItems as $item)
                    <div class="produk-item">
                      <img src="{{ asset(($item['product'] ?? $item->product)->main_image ?? 'src/default-product.png') }}" alt="{{ ($item['product'] ?? $item->product)->name ?? 'Product' }}" />
                      <div class="item-info">
                        <h4>{{ ($item['product'] ?? $item->product)->name ?? 'Produk tidak ditemukan' }}</h4>
                        <p>{{ $item['quantity'] ?? $item->quantity }} x Rp {{ number_format(($item['product'] ?? $item->product)->price ?? 0, 2, ',', '.') }}</p>
                      </div>
                      <div class="item-harga">Rp {{ number_format((($item['product'] ?? $item->product)->price ?? 0) * ($item['quantity'] ?? $item->quantity), 2, ',', '.') }}</div>
                    </div>
                  @endforeach
                @else
                  <p>Keranjang Anda kosong.</p>
                @endif

                <div class="biaya-detail">
                  <div class="detail-row">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($subTotal, 2, ',', '.') }}</span>
                  </div>
                  <div class="detail-row">
                    <span>Biaya Pengiriman</span>
                    <span id="shippingCost">Rp {{ number_format($shippingCost, 2, ',', '.') }}</span>
                  </div>
                  <div class="detail-row total">
                    <span>Total</span>
                    <span id="totalHarga">Rp {{ number_format($total, 2, ',', '.') }}</span>
                  </div>
                </div>
              </div>
            </section>
          </div>

          <div class="checkout-right">
            <!-- Ringkasan Belanja -->
            <section class="ringkasan-belanja">
              <div class="section-header">
                <h3>Ringkasan Belanja</h3>
              </div>

              <div class="ringkasan-content">
                @if($cartItems->count() > 0)
                  @foreach($cartItems as $item)
                    <div class="produk-preview">
                      <img src="{{ asset(($item['product'] ?? $item->product)->main_image ?? 'src/default-product.png') }}" alt="{{ ($item['product'] ?? $item->product)->name ?? 'Product' }}" />
                      <div class="produk-info">
                        <h4>{{ ($item['product'] ?? $item->product)->name ?? 'Produk tidak ditemukan' }}</h4>
                        <p class="produk-harga">Rp {{ number_format(($item['product'] ?? $item->product)->price ?? 0, 2, ',', '.') }}</p>
                      </div>
                      <span class="produk-qty">x{{ $item['quantity'] ?? $item->quantity }}</span>
                    </div>
                  @endforeach
                @else
                  <p>Keranjang Anda kosong.</p>
                @endif
              </div>

              <div class="total-section">
                <div class="total-row">
                  <span>Total Belanja</span>
                  <span>Rp {{ number_format($total, 2, ',', '.') }}</span>
                </div>

                <button type="submit" class="btn btn-primary btn-checkout" id="prosesPesananBtn">
                  Proses Pesanan
                </button>
              </div>
            </section>
          </div>
        </div>
      </form>
    </div>
  </main>
@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const form = document.getElementById('checkoutForm');
      const shippingCostElement = document.getElementById('shippingCost');
      const totalHargaElement = document.getElementById('totalHarga');
      const prosesPesananBtn = document.getElementById('prosesPesananBtn');

      // Ambil nilai awal dari data Blade
      let subtotal = {{ $subTotal }};
      // Gunakan biaya pengiriman default untuk tampilan awal
      let shippingCost = {{ $shippingCost }};

      // Update tampilan harga awal
      shippingCostElement.textContent = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      }).format(shippingCost).replace('Rp', '').trim();
      totalHargaElement.textContent = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      }).format(subtotal + shippingCost).replace('Rp', '').trim();

      // Edit alamat functionality
      const editBtn = document.getElementById('editAlamatBtn');
      const alamatCard = document.getElementById('alamatCard');
      const alamatFormSection = document.getElementById('alamatFormSection');

      if (editBtn) {
        // Gunakan state untuk melacak mode tombol
        let isEditMode = false;

        editBtn.addEventListener('click', function() {
          if (!isEditMode) {
            // Mode edit: tampilkan form, sembunyikan card
            alamatCard.style.display = 'none';
            alamatFormSection.style.display = 'block';
            editBtn.innerHTML = '<i class="bi bi-check"></i>';
            isEditMode = true;
          } else {
            // Mode simpan: validasi dan kirim ke server untuk update temporary data
            const recipientName = document.getElementById('recipient_name').value;
            const phone = document.getElementById('phone').value;
            const province = document.getElementById('province').value;
            const city = document.getElementById('city').value;
            const district = document.getElementById('district').value;
            const ward = document.getElementById('ward').value;
            const fullAddress = document.getElementById('full_address').value;

            if (!recipientName || !phone || !province || !city || !district || !ward || !fullAddress) {
              alert('Mohon lengkapi semua field alamat');
              return;
            }

            // Simpan alamat hanya di sisi klien (tidak dikirim ke server)
            // Karena sistem kita hanya untuk sementara di sesi checkout ini
            document.getElementById('alamatNama').textContent = recipientName;
            document.getElementById('alamatDetail').innerHTML = fullAddress.replace(/,/g, '<br>');
            document.getElementById('alamatPhone').textContent = phone;

            // Kembali ke tampilan card
            alamatCard.style.display = 'block';
            alamatFormSection.style.display = 'none';
            editBtn.innerHTML = '<i class="bi bi-pencil"></i>';
            isEditMode = false;

            // Tampilkan notifikasi sukses
            alert('Alamat berhasil diperbarui untuk checkout saat ini');
          }
        });
      }

      // Tambahkan validasi sebelum submit
      form.addEventListener('submit', function(e) {
        // Ambil semua nilai field alamat terlepas dari apakah form dalam mode tampil atau edit
        const recipientName = document.getElementById('recipient_name').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const province = document.getElementById('province').value.trim();
        const city = document.getElementById('city').value.trim();
        const district = document.getElementById('district').value.trim();
        const ward = document.getElementById('ward').value.trim();
        const fullAddress = document.getElementById('full_address').value.trim();

        // Validasi apakah semua field alamat telah diisi
        if (!recipientName || !phone || !province || !city || !district || !ward || !fullAddress) {
          e.preventDefault(); // Mencegah pengiriman form

          // Tampilkan pesan notifikasi yang lebih informatif
          alert('Mohon lengkapi semua field alamat pengiriman sebelum melanjutkan proses pesanan. Form alamat akan ditampilkan untuk Anda isi.');

          // Tampilkan form alamat (karena mungkin sedang dalam mode kartu)
          document.getElementById('alamatCard').style.display = 'none';
          document.getElementById('alamatFormSection').style.display = 'block';

          // Kembalikan status tombol
          prosesPesananBtn.innerHTML = 'Proses Pesanan';
          prosesPesananBtn.disabled = false;

          // Fokus ke field pertama yang kosong untuk membantu user
          if (!recipientName) {
            document.getElementById('recipient_name').focus();
          } else if (!phone) {
            document.getElementById('phone').focus();
          } else if (!province) {
            document.getElementById('province').focus();
          } else if (!city) {
            document.getElementById('city').focus();
          } else if (!district) {
            document.getElementById('district').focus();
          } else if (!ward) {
            document.getElementById('ward').focus();
          } else if (!fullAddress) {
            document.getElementById('full_address').focus();
          }

          return false;
        }

        // Tampilkan loading pada tombol
        prosesPesananBtn.innerHTML = 'Memproses...';
        prosesPesananBtn.disabled = true;
      });

      // Reset status tombol jika ada error
      @if($errors->any())
        if (prosesPesananBtn) {
          prosesPesananBtn.innerHTML = 'Proses Pesanan';
          prosesPesananBtn.disabled = false;
        }
      @endif
    });
  </script>
@endpush

@section('footer')
  @include('components.customer.footer.footer')
@endsection"{{-- Debug: Tampilkan data alamat user --}}" 
