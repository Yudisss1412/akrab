@extends('layouts.app')

@section('title', 'Verifikasi Pembayaran - Admin')

@section('header')
  @include('components.admin.header.header')
@endsection

@section('content')
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar untuk navigasi admin -->
      @include('components.admin.sidebar.sidebar')

      <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2">Verifikasi Pembayaran</h1>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <div class="input-group">
              <input type="text" class="form-control" placeholder="Cari nomor pesanan, nama pelanggan, atau nama penjual..." id="searchInput">
              <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                <i class="fas fa-search"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- Daftar pembayaran yang menunggu verifikasi -->
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>Nomor Pesanan</th>
                <th>Pelanggan</th>
                <th>Penjual</th>
                <th>Total Pembayaran</th>
                <th>Tanggal</th>
                <th>Bukti Pembayaran</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody id="pendingPaymentsTable">
              @forelse($pendingPayments as $order)
                <tr>
                  <td>{{ $order->order_number }}</td>
                  <td>{{ $order->user->name ?? 'Pelanggan Tidak Ditemukan' }}</td>
                  <td>{{ $order->items->first() ? ($order->items->first()->product->seller->store_name ?? 'Penjual Tidak Ditemukan') : 'Tidak Ada Produk' }}</td>
                  <td>Rp{{ number_format($order->total_amount, 0, ',', '.') }}</td>
                  <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                  <td>
                    @if($order->payment && $order->payment->proof_image)
                      <a href="{{ asset('storage/' . $order->payment->proof_image) }}" target="_blank">
                        <img src="{{ asset('storage/' . $order->payment->proof_image) }}" alt="Bukti Pembayaran" width="80" height="80" class="img-thumbnail">
                      </a>
                    @else
                      <span class="text-muted">Tidak ada bukti</span>
                    @endif
                  </td>
                  <td>
                    <span class="badge bg-warning">{{ ucfirst(str_replace('_', ' ', $order->payment->payment_status ?? 'Tidak Ditemukan')) }}</span>
                  </td>
                  <td>
                    <div class="btn-group" role="group">
                      <button type="button" class="btn btn-sm btn-success verify-btn" data-order-id="{{ $order->id }}">Verifikasi</button>
                      <button type="button" class="btn btn-sm btn-danger reject-btn" data-order-id="{{ $order->id }}">Tolak</button>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="text-center">Tidak ada pembayaran yang menunggu verifikasi</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        {{ $pendingPayments->links() }}
      </main>
    </div>
  </div>

  <!-- Modal untuk verifikasi pembayaran -->
  <div class="modal fade" id="verificationModal" tabindex="-1" aria-labelledby="verificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="verificationModalLabel">Verifikasi Pembayaran</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Apakah Anda yakin ingin memverifikasi pembayaran ini?</p>
          <p class="fw-bold">Nomor Pesanan: <span id="orderNumberForVerification"></span></p>
          <p>Pelanggan: <span id="customerNameForVerification"></span></p>
          <p>Penjual: <span id="sellerNameForVerification"></span></p>
          <p>Total: <span id="orderTotalForVerification"></span></p>
          <div class="text-center">
            <img id="proofImageForVerification" src="" alt="Bukti Pembayaran" class="img-fluid rounded" style="max-height: 400px; object-fit: contain;">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-success" id="confirmVerificationBtn">Verifikasi</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal untuk penolakan pembayaran -->
  <div class="modal fade" id="rejectionModal" tabindex="-1" aria-labelledby="rejectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="rejectionModalLabel">Tolak Pembayaran</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Apakah Anda yakin ingin menolak pembayaran ini?</p>
          <p class="fw-bold">Nomor Pesanan: <span id="orderNumberForRejection"></span></p>
          <p>Pelanggan: <span id="customerNameForRejection"></span></p>
          <p>Penjual: <span id="sellerNameForRejection"></span></p>
          <p>Total: <span id="orderTotalForRejection"></span></p>
          <div class="text-center">
            <img id="proofImageForRejection" src="" alt="Bukti Pembayaran" class="img-fluid rounded" style="max-height: 400px; object-fit: contain;">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-danger" id="confirmRejectionBtn">Tolak</button>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    let currentOrderId = null;
    let currentAction = null; // 'verify' or 'reject'

    document.addEventListener('DOMContentLoaded', function() {
      // Event listener for search
      document.getElementById('searchBtn').addEventListener('click', function() {
        performSearch();
      });

      document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
          performSearch();
        }
      });

      // Event listener for verification buttons
      document.querySelectorAll('.verify-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
          const orderId = this.getAttribute('data-order-id');
          openVerificationModal(orderId);
        });
      });

      // Event listener for rejection buttons
      document.querySelectorAll('.reject-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
          const orderId = this.getAttribute('data-order-id');
          openRejectionModal(orderId);
        });
      });

      // Confirm verification
      document.getElementById('confirmVerificationBtn').addEventListener('click', function() {
        if (currentOrderId) {
          updatePaymentStatus(currentOrderId, 'paid');
        }
      });

      // Confirm rejection
      document.getElementById('confirmRejectionBtn').addEventListener('click', function() {
        if (currentOrderId) {
          updatePaymentStatus(currentOrderId, 'rejected');
        }
      });

      // Close modals when needed
      document.querySelectorAll('.btn-close').forEach(function(btn) {
        btn.addEventListener('click', function() {
          resetCurrentOrder();
        });
      });
    });

    function performSearch() {
      const searchTerm = document.getElementById('searchInput').value;
      let url = '{{ route("admin.payment.verification") }}';

      if (searchTerm) {
        url += '?search=' + encodeURIComponent(searchTerm);
      }

      window.location.href = url;
    }

    function openVerificationModal(orderId) {
      // Find the order data from the table
      const row = document.querySelector(`.verify-btn[data-order-id="${orderId}"]`).closest('tr');
      const orderNumber = row.cells[0].textContent;
      const customerName = row.cells[1].textContent;
      const sellerName = row.cells[2].textContent;
      const total = row.cells[3].textContent;
      const imageSrc = row.querySelector('img') ? row.querySelector('img').src : '';

      document.getElementById('orderNumberForVerification').textContent = orderNumber;
      document.getElementById('customerNameForVerification').textContent = customerName;
      document.getElementById('sellerNameForVerification').textContent = sellerName;
      document.getElementById('orderTotalForVerification').textContent = total;
      document.getElementById('proofImageForVerification').src = imageSrc;

      currentOrderId = orderId;
      currentAction = 'verify';

      const modal = new bootstrap.Modal(document.getElementById('verificationModal'));
      modal.show();
    }

    function openRejectionModal(orderId) {
      // Find the order data from the table
      const row = document.querySelector(`.reject-btn[data-order-id="${orderId}"]`).closest('tr');
      const orderNumber = row.cells[0].textContent;
      const customerName = row.cells[1].textContent;
      const sellerName = row.cells[2].textContent;
      const total = row.cells[3].textContent;
      const imageSrc = row.querySelector('img') ? row.querySelector('img').src : '';

      document.getElementById('orderNumberForRejection').textContent = orderNumber;
      document.getElementById('customerNameForRejection').textContent = customerName;
      document.getElementById('sellerNameForRejection').textContent = sellerName;
      document.getElementById('orderTotalForRejection').textContent = total;
      document.getElementById('proofImageForRejection').src = imageSrc;

      currentOrderId = orderId;
      currentAction = 'reject';

      const modal = new bootstrap.Modal(document.getElementById('rejectionModal'));
      modal.show();
    }

    function updatePaymentStatus(orderId, newStatus) {
      // Disable buttons while processing
      const verifyBtn = document.getElementById('confirmVerificationBtn');
      const rejectBtn = document.getElementById('confirmRejectionBtn');

      if (currentAction === 'verify') {
        verifyBtn.disabled = true;
        verifyBtn.textContent = 'Memproses...';
      } else {
        rejectBtn.disabled = true;
        rejectBtn.textContent = 'Memproses...';
      }

      fetch('{{ route("payment.update-status") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          order_id: orderId,
          status: newStatus
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Status pembayaran berhasil diperbarui');
          // Close modal and refresh page
          bootstrap.Modal.getInstance(document.getElementById(currentAction === 'verify' ? 'verificationModal' : 'rejectionModal')).hide();
          location.reload();
        } else {
          alert('Gagal memperbarui status pembayaran: ' + (data.message || 'Kesalahan tidak diketahui'));
          resetButtons();
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memperbarui status pembayaran');
        resetButtons();
      });
    }

    function resetButtons() {
      const verifyBtn = document.getElementById('confirmVerificationBtn');
      const rejectBtn = document.getElementById('confirmRejectionBtn');

      if (verifyBtn) {
        verifyBtn.disabled = false;
        verifyBtn.textContent = 'Verifikasi';
      }
      if (rejectBtn) {
        rejectBtn.disabled = false;
        rejectBtn.textContent = 'Tolak';
      }
    }

    function resetCurrentOrder() {
      currentOrderId = null;
      currentAction = null;
    }
  </script>
@endpush