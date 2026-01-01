@extends('layouts.admin')

@section('title', 'Detail Laporan Pelanggaran - AKRAB')

@push('styles')
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin/dashboard_admin.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin_penjual/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin/report_detail.css') }}">
@endpush

@section('content')
  <div class="container-fluid">
    <div class="main-layout">
      <div class="content-wrapper">
        <main class="admin-page-content">
        <div class="page-header">
          <h1>Detail Laporan Pelanggaran</h1>
        </div>
        
        <div style="display: grid; grid-template-columns: 70% 30%; gap: 1.5rem; height: 100%;">
          <!-- Kolom Kiri: Detail Mendalam -->
          <div class="detail-container">
            <div class="detail-header">
              <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                  <h2 style="margin: 0; font-size: 1.5rem;">Laporan Pelanggaran #{{ $report->report_number }}</h2>
                  <p style="margin: 0.25rem 0 0; opacity: 0.9;">Dilaporkan pada {{ $report->created_at->format('d M Y') }}</p>
                </div>
                <span class="status-badge 
                  @if($report->status === 'pending') status-pending
                  @elseif($report->status === 'investigating') status-investigating
                  @elseif($report->status === 'resolved') status-resolved
                  @elseif($report->status === 'dismissed') status-dismissed
                  @else status-pending @endif">
                  {{ ucfirst(str_replace('_', ' ', $report->status)) }}
                </span>
              </div>
            </div>
            
            <div class="detail-content">
              <!-- Detail Utama Laporan -->
              <h3 class="section-title">Detail Utama Laporan</h3>
              <div class="info-grid">
                <div class="info-card">
                  <div class="info-label">ID Laporan</div>
                  <div class="info-value">{{ $report->report_number }}</div>
                </div>
                
                <div class="info-card">
                  <div class="info-label">Status</div>
                  <div class="info-value">{{ ucfirst(str_replace('_', ' ', $report->status)) }}</div>
                </div>
                
                <div class="info-card">
                  <div class="info-label">Tanggal Laporan</div>
                  <div class="info-value">{{ $report->created_at->format('d M Y H:i') }}</div>
                </div>
              </div>
              
              <!-- Informasi Pelapor dan Penjual yang Dilaporkan -->
              <h3 class="section-title">Informasi Pelapor & Penjual Terlapor</h3>
              <div class="info-grid">
                <div class="info-card">
                  <div class="info-label">Pelapor</div>
                  <div class="info-value">{{ $report->reporter->name ?? 'Tidak Ditemukan' }}</div>
                  <div class="info-label" style="margin-top: 0.5rem;">Email</div>
                  <div class="info-value">{{ $report->reporter->email ?? 'Tidak Tersedia' }}</div>
                  <div class="info-label" style="margin-top: 0.5rem;">ID Pengguna</div>
                  <div class="info-value">USR-{{ $report->reporter->id ?? '000000' }}</div>
                </div>

                <div class="info-card">
                  <div class="info-label">Penjual Terlapor</div>
                  <div class="info-value">{{ $report->violator->name ?? 'Tidak Ditemukan' }}</div>
                  <div class="info-label" style="margin-top: 0.5rem;">Email</div>
                  <div class="info-value">{{ $report->violator->email ?? 'Tidak Tersedia' }}</div>
                  <div class="info-label" style="margin-top: 0.5rem;">ID Penjual</div>
                  <div class="info-value">SLR-{{ $report->violator->id ?? '000000' }}</div>
                </div>

                <div class="info-card">
                  <div class="info-label">Produk Terkait</div>
                  <div class="info-value">{{ $report->product->name ?? 'Tidak Ada Produk Terkait' }}</div>
                  <div class="info-label" style="margin-top: 0.5rem;">ID Produk</div>
                  <div class="info-value">{{ $report->product ? 'PRD-'.$report->product->id : 'Tidak Tersedia' }}</div>
                  <div class="info-label" style="margin-top: 0.5rem;">Kategori</div>
                  <div class="info-value">{{ $report->product->category->name ?? 'Tidak Tersedia' }}</div>
                </div>
              </div>
              
              <!-- Detail Jenis dan Deskripsi Pelanggaran -->
              <h3 class="section-title">Jenis dan Deskripsi Pelanggaran</h3>
              <div class="info-grid">
                <div class="info-card">
                  <div class="info-label">Jenis Pelanggaran</div>
                  <div class="info-value">{{ ucfirst(str_replace('_', ' ', $report->violation_type)) }}</div>
                </div>
                
                <div class="info-card" style="grid-column: span 2;">
                  <div class="info-label">Deskripsi Pelanggaran</div>
                  <div class="info-value">{{ $report->description }}</div>
                </div>
              </div>
              
              <!-- Riwayat Pelanggaran Penjual -->
              <h3 class="section-title">Riwayat Pelanggaran Penjual</h3>
              @if($report->violator)
              @php
                $violations = \App\Models\ViolationReport::where('violator_user_id', $report->violator->id)
                  ->where('id', '!=', $report->id) // Exclude current report
                  ->orderBy('created_at', 'desc')
                  ->limit(5)
                  ->get();
              @endphp
              @if($violations->count() > 0)
              <div class="violation-history">
                @foreach($violations as $violation)
                <div class="history-item">
                  <div style="display: flex; justify-content: space-between;">
                    <div>
                      <strong>{{ ucfirst(str_replace('_', ' ', $violation->violation_type)) }} - {{ $violation->product->name ?? 'Produk Tidak Ditemukan' }}</strong>
                      <div style="font-size: 0.9rem; color: var(--muted);">Dilaporkan: {{ $violation->created_at->format('d M Y') }}</div>
                    </div>
                    <span class="status-badge
                      @if($violation->status === 'pending') status-pending
                      @elseif($violation->status === 'investigating') status-investigating
                      @elseif($violation->status === 'resolved') status-resolved
                      @elseif($violation->status === 'dismissed') status-dismissed
                      @else status-pending @endif">
                      {{ ucfirst(str_replace('_', ' ', $violation->status)) }}
                    </span>
                  </div>
                  <div style="margin-top: 0.5rem; font-size: 0.9rem;">{{ Str::limit($violation->description, 100) }}</div>
                </div>
                @endforeach
              </div>
              @else
              <div class="info-card">
                <div class="info-label">Tidak ada riwayat pelanggaran sebelumnya</div>
                <div class="info-value">Penjual ini belum pernah dilaporkan sebelumnya</div>
              </div>
              @endif
              @else
              <div class="info-card">
                <div class="info-label">Tidak dapat menampilkan riwayat pelanggaran</div>
                <div class="info-value">Tidak ada informasi penjual yang dilaporkan</div>
              </div>
              @endif
              
              <!-- Bukti Laporan -->
              <h3 class="section-title">Bukti Laporan</h3>
              @if($report->evidence)
              @php
                $evidence_array = is_array($report->evidence) ? $report->evidence : [$report->evidence];
                $evidence_array = array_filter($evidence_array); // Filter out null/empty values
              @endphp
              @if(count($evidence_array) > 0)
              <div class="evidence-grid">
                @foreach($evidence_array as $evidence)
                <div class="evidence-item">
                  <img src="{{ str_starts_with($evidence, 'http') ? $evidence : asset('storage/' . $evidence) }}" alt="Bukti Gambar" class="evidence-img" onerror="this.onerror=null; this.src='{{ asset('src/placeholder_produk.png') }}';">
                  <div class="evidence-desc">Bukti pelanggaran</div>
                </div>
                @endforeach
              </div>
              @else
              <div class="info-card">
                <div class="info-label">Tidak ada bukti dilampirkan</div>
                <div class="info-value">Pelapor tidak menyertakan bukti pelanggaran</div>
              </div>
              @endif
              @else
              <div class="info-card">
                <div class="info-label">Tidak ada bukti dilampirkan</div>
                <div class="info-value">Pelapor tidak menyertakan bukti pelanggaran</div>
              </div>
              @endif
            </div>
          </div>
          
          <!-- Kolom Kanan: Aksi Multi-langkah (Sticky) -->
          <div style="position: sticky; top: 1.5rem; height: fit-content;">
            <div class="action-steps">
              <h3 class="section-title">Ambil Tindakan</h3>
              
              <div class="step">
                <div class="step-title">
                  <span class="step-number">1</span>
                  Pilih Tindakan
                </div>
                <div class="form-group">
                  <label for="actionType">Tindakan</label>
                  <select id="actionType" class="form-control">
                    <option value="">Pilih tindakan yang akan diambil</option>
                    <option value="reject">Tolak Laporan</option>
                    <option value="warning">Beri Peringatan ke Penjual</option>
                    <option value="deactivate">Nonaktifkan Produk Terkait</option>
                    <option value="suspend_temp">Blokir Penjual Sementara</option>
                    <option value="suspend_perm">Blokir Penjual Permanen</option>
                  </select>
                </div>
              </div>
              
              <div class="step" id="messageStep" style="display: none;">
                <div class="step-title">
                  <span class="step-number">2</span>
                  Tulis Pesan untuk Penjual
                </div>
                <div class="form-group">
                  <label for="actionMessage">Pesan Peringatan</label>
                  <textarea id="actionMessage" class="form-control" placeholder="Tulis pesan peringatan untuk penjual..."></textarea>
                </div>
              </div>
              
              <div class="step">
                <div class="step-title">
                  <span class="step-number">3</span>
                  Konfirmasi
                </div>
                <button class="btn-submit" id="confirmAction">Kirim Tindakan</button>
              </div>
            </div>
          </div>
        
      </main>
    </div>
  </div>

  @include('components.admin_penjual.footer')

  <script>
    // Handle action selection and conditional message field display
    document.getElementById('actionType').addEventListener('change', function() {
      const messageStep = document.getElementById('messageStep');
      const actionMessage = document.getElementById('actionMessage');
      
      // Show message field only for specific actions
      if (this.value === 'warning') {
        messageStep.style.display = 'block';
        actionMessage.placeholder = 'Silakan tulis pesan peringatan untuk penjual...';
      } else {
        messageStep.style.display = 'none';
      }
    });
    
    // Handle form submission
    document.getElementById('confirmAction').addEventListener('click', async function() {
      const actionType = document.getElementById('actionType').value;
      const actionMessage = document.getElementById('actionMessage');
      
      if (!actionType) {
        alert('Silakan pilih tindakan terlebih dahulu.');
        return;
      }
      
      // Only require message if warning is selected
      if (actionType === 'warning' && !actionMessage.value.trim()) {
        alert('Silakan tulis pesan peringatan untuk penjual.');
        return;
      }
      
      // Determine status based on action
      let status = 'investigating';
      let resolution = null;
      
      switch(actionType) {
        case 'reject':
          status = 'dismissed';
          break;
        case 'warning':
        case 'deactivate':
          status = 'resolved';
          resolution = 'warning';
          break;
        case 'suspend_temp':
          status = 'resolved';
          resolution = 'suspension';
          break;
        case 'suspend_perm':
          status = 'resolved';
          resolution = 'permanent_ban';
          break;
      }
      
      try {
        const response = await fetch(`/reports/violations/{{ $report->id }}/status`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({
            status: status,
            admin_notes: actionMessage.value.trim() || `Tindakan diambil: ${actionType}`,
            resolution: resolution
          })
        });
        
        const data = await response.json();
        
        if (data.success) {
          alert(`Tindakan "${actionType}" berhasil dikirimkan. Laporan telah diperbarui.`);
          // Reload the page to show updated status
          location.reload();
        } else {
          alert('Gagal mengirim tindakan: ' + (data.message || 'Unknown error'));
        }
      } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat mengirim tindakan');
      }
    });
  </script>
@endsection
