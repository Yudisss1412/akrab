<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Saldo & Penarikan Dana — AKRAB</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin_penjual/style.css') }}">
  <style>
    :root {
      --ak-primary: #006E5C;
      --ak-primary-light: #a8d5c9;
      --ak-white: #FFFFFF;
      --ak-background: #f0fdfa;
      --ak-text: #1D232A;
      --ak-muted: #6b7280;
      --ak-border: #E5E7EB;
      --ak-success: #10B981;
      --ak-danger: #EF4444;
      --ak-warning: #F59E0B;
      --ak-radius: 12px;
      --ak-space: 16px;
    }
    
    * {
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      padding: 0;
      color: var(--ak-text);
      background: var(--ak-background);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    
    .main-layout {
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    
    .content-wrapper {
      flex: 1;
      display: flex;
      flex-direction: column;
      padding: 0 1.5rem;
    }
    
    /* Page header */
    .page-header {
      background: var(--ak-white);
      border-radius: var(--ak-radius);
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      border: 1px solid var(--ak-border);
    }
    
    .page-header h1 {
      margin: 0;
      font-size: 1.5rem;
      font-weight: 600;
      color: var(--ak-primary);
    }
    
    /* Saldo card */
    .saldo-card {
      background: var(--ak-white);
      border-radius: var(--ak-radius);
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      border: 1px solid var(--ak-border);
    }
    
    .saldo-title {
      font-size: 0.875rem;
      color: var(--ak-muted);
      margin: 0 0 0.5rem 0;
    }
    
    .saldo-amount {
      font-size: 2.5rem;
      font-weight: 700;
      color: var(--ak-text);
      margin: 0 0 1.5rem 0;
    }
    
    .btn-primary {
      background: var(--ak-primary);
      color: white;
      border: none;
      border-radius: var(--ak-radius);
      padding: 0.75rem 1.25rem;
      font-weight: 500;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      text-decoration: none;
    }
    
    .btn-primary:hover {
      background: #005a4a;
    }
    
    /* Tab navigation */
    .tab-nav {
      display: flex;
      border-bottom: 1px solid var(--ak-border);
      margin-bottom: 1.5rem;
    }
    
    .tab-item {
      padding: 0.75rem 1.25rem;
      cursor: pointer;
      border-bottom: 3px solid transparent;
      font-weight: 500;
      color: var(--ak-muted);
    }
    
    .tab-item.active {
      border-bottom: 3px solid var(--ak-primary);
      color: var(--ak-primary);
    }
    
    /* Tab content */
    .tab-content {
      display: none;
    }
    
    .tab-content.active {
      display: block;
    }
    
    /* Card */
    .card {
      background: var(--ak-white);
      border-radius: var(--ak-radius);
      padding: 1.5rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      border: 1px solid var(--ak-border);
    }
    
    .card h2 {
      margin-top: 0;
      margin-bottom: 1rem;
      font-size: 1.25rem;
      font-weight: 600;
      color: var(--ak-primary);
    }
    
    /* Table styles */
    .data-table {
      width: 100%;
      border-collapse: collapse;
    }
    
    .data-table th {
      text-align: left;
      padding: 0.75rem 1rem;
      font-weight: 600;
      color: var(--ak-muted);
      border-bottom: 1px solid var(--ak-border);
    }
    
    .data-table td {
      padding: 0.75rem 1rem;
      border-bottom: 1px solid var(--ak-border);
    }
    
    .data-table tr:last-child td {
      border-bottom: none;
    }
    
    .text-green {
      color: var(--ak-success);
      font-weight: 500;
    }
    
    .text-red {
      color: var(--ak-danger);
      font-weight: 500;
    }
    
    .badge {
      display: inline-block;
      padding: 0.25rem 0.5rem;
      border-radius: 50px;
      font-size: 0.75rem;
      font-weight: 500;
    }
    
    .badge-success {
      background: rgba(16, 185, 129, 0.1);
      color: var(--ak-success);
    }
    
    .badge-pending {
      background: rgba(245, 158, 11, 0.1);
      color: var(--ak-warning);
    }
    
    .badge-failed {
      background: rgba(239, 68, 68, 0.1);
      color: var(--ak-danger);
    }
    
    /* Pagination */
    .pagination {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 0.5rem;
      margin-top: 1.5rem;
    }
    
    .pagination a, .pagination span {
      display: inline-block;
      padding: 0.5rem 0.75rem;
      border-radius: var(--ak-radius);
      text-decoration: none;
      font-size: 0.875rem;
    }
    
    .pagination .active {
      background: var(--ak-primary);
      color: white;
    }
    
    /* Modal */
    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      z-index: 1000;
      align-items: center;
      justify-content: center;
    }
    
    .modal.active {
      display: flex;
    }
    
    .modal-content {
      background: var(--ak-white);
      border-radius: var(--ak-radius);
      width: 90%;
      max-width: 500px;
      padding: 1.5rem;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
      position: relative;
    }
    
    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
    }
    
    .modal-header h2 {
      margin: 0;
      font-size: 1.25rem;
      color: var(--ak-primary);
    }
    
    .close-modal {
      background: none;
      border: none;
      font-size: 1.5rem;
      cursor: pointer;
      color: var(--ak-muted);
    }
    
    .form-group {
      margin-bottom: 1rem;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
      color: var(--ak-text);
    }
    
    .form-control {
      width: 100%;
      padding: 0.75rem;
      border: 1px solid var(--ak-border);
      border-radius: var(--ak-radius);
      font-size: 1rem;
    }
    
    .form-control:focus {
      outline: none;
      border-color: var(--ak-primary);
    }
    
    .modal-footer {
      display: flex;
      justify-content: flex-end;
      gap: 0.75rem;
      margin-top: 1.5rem;
    }
  </style>
</head>
<body>
  @include('components.admin_penjual.header')

  <div class="main-layout">
    <div class="content-wrapper">
      <main class="content admin-page-content" role="main">
        <!-- Page Header -->
        <section class="page-header">
          <h1>Saldo & Penarikan Dana</h1>
        </section>

        <!-- Saldo Summary Card -->
        <section class="saldo-card">
          <p class="saldo-title">Saldo Saat Ini (Dapat Ditarik)</p>
          <p class="saldo-amount">Rp 5.450.000</p>
          <button class="btn-primary" id="btnWithdraw">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            + Tarik Dana
          </button>
        </section>

        <!-- Tab Navigation -->
        <div class="tab-nav">
          <div class="tab-item active" data-tab="transaksi">Riwayat Transaksi</div>
          <div class="tab-item" data-tab="penarikan">Riwayat Penarikan</div>
        </div>

        <!-- Tab Content: Transaction History -->
        <div class="tab-content active" id="transaksi-content">
          <section class="card">
            <h2>Riwayat Transaksi</h2>
            <table class="data-table">
              <thead>
                <tr>
                  <th>Tanggal</th>
                  <th>Deskripsi</th>
                  <th>Jenis</th>
                  <th>Jumlah</th>
                  <th>Saldo Akhir</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>10 Okt 2025</td>
                  <td>Penjualan Produk "Kaos Polos"</td>
                  <td><span class="text-green">Pemasukan</span></td>
                  <td class="text-green">+Rp 150.000</td>
                  <td>Rp 5.450.000</td>
                </tr>
                <tr>
                  <td>8 Okt 2025</td>
                  <td>Pemrosesan Biaya Admin</td>
                  <td><span class="text-red">Pengeluaran</span></td>
                  <td class="text-red">-Rp 2.500</td>
                  <td>Rp 5.300.000</td>
                </tr>
                <tr>
                  <td>5 Okt 2025</td>
                  <td>Penjualan Produk Setelan Celana</td>
                  <td><span class="text-green">Pemasukan</span></td>
                  <td class="text-green">+Rp 125.000</td>
                  <td>Rp 5.302.500</td>
                </tr>
                <tr>
                  <td>3 Okt 2025</td>
                  <td>Penjualan Produk Jaket Hoodie</td>
                  <td><span class="text-green">Pemasukan</span></td>
                  <td class="text-green">+Rp 275.000</td>
                  <td>Rp 5.177.500</td>
                </tr>
                <tr>
                  <td>1 Okt 2025</td>
                  <td>Penjualan Produk Celana Panjang</td>
                  <td><span class="text-green">Pemasukan</span></td>
                  <td class="text-green">+Rp 180.000</td>
                  <td>Rp 4.902.500</td>
                </tr>
              </tbody>
            </table>
            
            <!-- Pagination -->
            <div class="pagination">
              <a href="#" class="prev">‹ Sebelumnya</a>
              <a href="#">1</a>
              <a href="#">2</a>
              <a href="#">3</a>
              <span class="active">4</span>
              <a href="#">5</a>
              <a href="#" class="next">Berikutnya ›</a>
            </div>
          </section>
        </div>

        <!-- Tab Content: Withdrawal History -->
        <div class="tab-content" id="penarikan-content">
          <section class="card">
            <h2>Riwayat Penarikan</h2>
            <table class="data-table">
              <thead>
                <tr>
                  <th>Tanggal Permintaan</th>
                  <th>ID Penarikan</th>
                  <th>Jumlah</th>
                  <th>Bank Tujuan</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>5 Okt 2025</td>
                  <td>WD-20251005001</td>
                  <td>Rp 1.200.000</td>
                  <td>BCA - 1234567890</td>
                  <td><span class="badge badge-success">Berhasil</span></td>
                </tr>
                <tr>
                  <td>2 Okt 2025</td>
                  <td>WD-20251002001</td>
                  <td>Rp 850.000</td>
                  <td>BRI - 0987654321</td>
                  <td><span class="badge badge-success">Berhasil</span></td>
                </tr>
                <tr>
                  <td>28 Sep 2025</td>
                  <td>WD-20250928001</td>
                  <td>Rp 500.000</td>
                  <td>Mandiri - 1122334455</td>
                  <td><span class="badge badge-pending">Diproses</span></td>
                </tr>
                <tr>
                  <td>20 Sep 2025</td>
                  <td>WD-20250920001</td>
                  <td>Rp 1.000.000</td>
                  <td>BNI - 5566778899</td>
                  <td><span class="badge badge-success">Berhasil</span></td>
                </tr>
                <tr>
                  <td>15 Sep 2025</td>
                  <td>WD-20250915001</td>
                  <td>Rp 750.000</td>
                  <td>BCA - 1234567890</td>
                  <td><span class="badge badge-failed">Gagal</span></td>
                </tr>
              </tbody>
            </table>
            
            <!-- Pagination -->
            <div class="pagination">
              <a href="#" class="prev">‹ Sebelumnya</a>
              <a href="#">1</a>
              <a href="#">2</a>
              <span class="active">3</span>
              <a href="#">4</a>
              <a href="#" class="next">Berikutnya ›</a>
            </div>
          </section>
        </div>
        
        <!-- Withdrawal Modal -->
        <div class="modal" id="withdrawModal">
          <div class="modal-content">
            <div class="modal-header">
              <h2>Tarik Dana</h2>
              <button class="close-modal" id="closeModal">&times;</button>
            </div>
            
            <form id="withdrawForm">
              <div class="form-group">
                <label for="amount">Jumlah Dana yang Ditarik</label>
                <input type="number" id="amount" class="form-control" placeholder="Masukkan jumlah dana" min="50000" max="5450000" required>
              </div>
              
              <div class="form-group">
                <label for="bank">Pilih Rekening Bank Tujuan</label>
                <select id="bank" class="form-control" required>
                  <option value="">Pilih bank</option>
                  <option value="bca">BCA - 1234567890</option>
                  <option value="bri">BRI - 0987654321</option>
                  <option value="mandiri">Mandiri - 1122334455</option>
                  <option value="bni">BNI - 5566778899</option>
                </select>
              </div>
              
              <div class="form-group">
                <label for="pin">Konfirmasi PIN/Password</label>
                <input type="password" id="pin" class="form-control" placeholder="Masukkan PIN Anda" required>
              </div>
              
              <div class="modal-footer">
                <button type="button" class="btn-primary" id="confirmWithdraw">Konfirmasi</button>
                <button type="button" class="close-modal">Batal</button>
              </div>
            </form>
          </div>
        </div>
      </main>
    </div>
  </div>

  @include('components.admin_penjual.footer')
  
  <script>
    // Tab functionality
    document.querySelectorAll('.tab-item').forEach(tab => {
      tab.addEventListener('click', () => {
        // Remove active class from all tabs and contents
        document.querySelectorAll('.tab-item').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        
        // Add active class to clicked tab
        tab.classList.add('active');
        
        // Show corresponding content
        const tabId = tab.getAttribute('data-tab');
        document.getElementById(`${tabId}-content`).classList.add('active');
      });
    });
    
    // Modal functionality
    const withdrawModal = document.getElementById('withdrawModal');
    const btnWithdraw = document.getElementById('btnWithdraw');
    const closeModal = document.getElementById('closeModal');
    const closeButtons = document.querySelectorAll('.close-modal');
    
    btnWithdraw.addEventListener('click', () => {
      withdrawModal.classList.add('active');
    });
    
    closeButtons.forEach(button => {
      button.addEventListener('click', () => {
        withdrawModal.classList.remove('active');
      });
    });
    
    // Close modal if clicked outside
    window.addEventListener('click', (event) => {
      if (event.target === withdrawModal) {
        withdrawModal.classList.remove('active');
      }
    });
    
    // Form submission
    document.getElementById('confirmWithdraw').addEventListener('click', (e) => {
      e.preventDefault();
      // Simple validation
      const amount = document.getElementById('amount').value;
      const bank = document.getElementById('bank').value;
      const pin = document.getElementById('pin').value;
      
      if(amount && bank && pin) {
        // In a real application, you would submit the form to the server
        alert('Permintaan penarikan dana berhasil dikirim!');
        withdrawModal.classList.remove('active');
        // Reset form
        document.getElementById('withdrawForm').reset();
      } else {
        alert('Mohon lengkapi semua field!');
      }
    });
  </script>
</body>
</html>