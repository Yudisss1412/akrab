<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Saldo & Penarikan Dana — AKRAB</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
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
          <p class="saldo-amount" id="saldoAmount">Rp 0</p>
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
              <tbody id="transactionTableBody">
                <!-- Data akan diisi secara dinamis -->
              </tbody>
            </table>
            
            <!-- Pagination for transactions -->
            <div class="pagination" id="transactionPagination">
              <!-- Pagination will be filled dynamically -->
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
              <tbody id="withdrawalTableBody">
                <!-- Data will be filled dynamically -->
              </tbody>
            </table>
            
            <!-- Pagination for withdrawals -->
            <div class="pagination" id="withdrawalPagination">
              <!-- Pagination will be filled dynamically -->
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
    // Ambil data saldo dan riwayat transaksi saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
      loadBalanceData();
      loadTransactionHistory();
      loadWithdrawalHistory();
    });

    // Fungsi untuk memformat angka menjadi format Rupiah
    function formatRupiah(angka) {
      var reverse = angka.toString().split('').reverse().join('');
      var ribuan = reverse.match(/\d{1,3}/g);
      var hasil = ribuan.join('.').split('').reverse().join('');
      return 'Rp ' + hasil;
    }

    // Fungsi untuk memuat data saldo
    async function loadBalanceData() {
      try {
        const response = await fetch('/api/withdrawal/balance', {
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        });
        const data = await response.json();

        if (data.balance !== undefined) {
          document.getElementById('saldoAmount').textContent = formatRupiah(data.balance);
        }
      } catch (error) {
        console.error('Error loading balance:', error);
        document.getElementById('saldoAmount').textContent = 'Rp 0';
      }
    }

    // Fungsi untuk memuat riwayat transaksi
    async function loadTransactionHistory() {
      try {
        const response = await fetch('/api/withdrawal/transactions', {
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        });
        const data = await response.json();

        if (data.transactions) {
          populateTransactionTable(data.transactions);
        }
      } catch (error) {
        console.error('Error loading transaction history:', error);
      }
    }

    // Fungsi untuk memuat riwayat penarikan
    async function loadWithdrawalHistory() {
      try {
        const response = await fetch('/api/withdrawal/history', {
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        });
        const data = await response.json();

        if (data.withdrawals) {
          populateWithdrawalTable(data.withdrawals.data); // Karena menggunakan paginate
        }
      } catch (error) {
        console.error('Error loading withdrawal history:', error);
      }
    }

    // Fungsi untuk mengisi tabel riwayat transaksi
    function populateTransactionTable(transactions) {
      const tbody = document.getElementById('transactionTableBody');
      tbody.innerHTML = ''; // Kosongkan tabel sebelumnya

      if (transactions.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align: center;">Tidak ada riwayat transaksi</td></tr>';
        return;
      }

      transactions.forEach(transaction => {
        const row = document.createElement('tr');

        // Format tanggal
        const date = new Date(transaction.date);
        const formattedDate = date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });

        // Tentukan jenis transaksi
        const isIncome = transaction.type === 'Pemasukan';
        const amountSign = isIncome ? '+' : '-';
        const amountClass = isIncome ? 'text-green' : 'text-red';
        const typeText = isIncome ? 'Pemasukan' : 'Pengeluaran';

        // Hitung saldo berdasarkan transaksi sebelumnya (dalam implementasi nyata, saldo akhir akan dihitung dengan lebih kompleks)
        const formattedAmount = `${amountSign}${formatRupiah(transaction.amount)}`;

        row.innerHTML = `
          <td>${formattedDate}</td>
          <td>${transaction.description || '-'}</td>
          <td><span class="${isIncome ? 'text-green' : 'text-red'}">${typeText}</span></td>
          <td class="${amountClass}">${formattedAmount}</td>
          <td>${formatRupiah(transaction.amount || 0)}</td>
        `;

        tbody.appendChild(row);
      });
    }

    // Fungsi untuk mengisi tabel riwayat penarikan
    function populateWithdrawalTable(withdrawals) {
      const tbody = document.getElementById('withdrawalTableBody');
      tbody.innerHTML = ''; // Kosongkan tabel sebelumnya

      if (withdrawals.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align: center;">Tidak ada riwayat penarikan</td></tr>';
        return;
      }

      withdrawals.forEach(withdrawal => {
        const row = document.createElement('tr');

        // Format tanggal
        const date = new Date(withdrawal.request_date || withdrawal.created_at);
        const formattedDate = date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });

        // Status badge
        let statusClass = '';
        let statusText = '';
        switch(withdrawal.status) {
          case 'approved':
          case 'completed':
            statusClass = 'badge-success';
            statusText = 'Berhasil';
            break;
          case 'pending':
            statusClass = 'badge-pending';
            statusText = 'Diproses';
            break;
          case 'rejected':
            statusClass = 'badge-failed';
            statusText = 'Gagal';
            break;
          default:
            statusClass = 'badge-pending';
            statusText = withdrawal.status || 'Diproses';
        }

        row.innerHTML = `
          <td>${formattedDate}</td>
          <td>${withdrawal.id || '-'}</td>
          <td>${formatRupiah(withdrawal.amount || 0)}</td>
          <td>${withdrawal.bank_account || withdrawal.account_number || '-'}</td>
          <td><span class="badge ${statusClass}">${statusText}</span></td>
        `;

        tbody.appendChild(row);
      });
    }

    document.getElementById('confirmWithdraw').addEventListener('click', (e) => {
      e.preventDefault();
      // Simple validation
      const amount = document.getElementById('amount').value;
      const bank = document.getElementById('bank').value;
      const pin = document.getElementById('pin').value;

      if(amount && bank && pin) {
        // Dalam implementasi nyata, kirim ke server
        fetch('/withdrawal', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({
            amount: parseFloat(amount),
            bank_account: bank
          })
        })
        .then(response => response.json())
        .then(data => {
          if (data.message) {
            alert(data.message);
            if(data.withdrawal_request) {
              withdrawModal.classList.remove('active');
              document.getElementById('withdrawForm').reset();
              // Refresh data
              loadBalanceData();
              loadWithdrawalHistory();
            }
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Terjadi kesalahan saat membuat permintaan penarikan');
        });
      } else {
        alert('Mohon lengkapi semua field!');
      }
    });
  </script>
</body>
</html>