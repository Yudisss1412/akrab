<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <title>Invoice — AKRAB</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/customer/transaksi/invoice.css') }}">
</head>
<body>
  <header class="ak-header">
    <div class="brand">
      <img src="{{ $store['logo'] ?? '/src/Logo_UMKM.png' }}" alt="AKRAB" class="logo">
      <span>Invoice</span>
    </div>
    <div class="actions">
      <button id="btnPrint" class="btn btn-primary" type="button" aria-label="Cetak invoice">
        Cetak
      </button>
      <a href="{{ route('profil.penjual') }}" class="btn" aria-label="Kembali ke Profil Penjual">Kembali</a>
    </div>
  </header>

  <main class="container-only-preview">
    <section class="preview">
      <div id="printCard" class="print-card">
        @php
          // Normalisasi data agar aman ketika key/variabel kosong
          $store     = $store     ?? [];
          $invoice   = $invoice   ?? [];
          $buyer     = $buyer     ?? [];
          $shipping  = $shipping  ?? [];
          $payment   = $payment   ?? [];
          $summary   = $summary   ?? [];
          $itemsList = $items     ?? [[
            'img'    => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?q=80&w=300&auto=format&fit=crop',
            'name'   => 'Mini Projector',
            'variant'=> 'Default',
            'qty'    => 1,
            'price'  => 1250000,
          ]];

          $fmt = function($n){ return 'Rp '.number_format((float)$n, 0, ',', '.'); };

          $payStatus   = strtoupper($invoice['pay_status'] ?? 'PAID');
          $orderStatus = $invoice['order_status'] ?? 'Selesai';
          $issuedAt    = $invoice['issued_at']   ?? now();
        @endphp

        {{-- ================= HEADER ================= --}}
        <div class="inv-head">
          <div class="inv-store">
            <img src="{{ $store['logo'] ?? '/src/Logo_UMKM.png' }}" alt="logo toko">
            <div>
              <div class="store-name">{{ $store['name'] ?? 'Shoppy.gg' }}</div>
              <div class="muted">{{ $store['addr'] ?? 'Jl. Melati No. 12, Banyuwangi' }}</div>
              <div class="muted">{{ $store['contact'] ?? '+62 812-3456-7890 • support@shoppy.gg' }}</div>
            </div>
          </div>

          <div class="inv-meta">
            <div class="muted small">Invoice</div>
            <div class="strong">{{ $invoice['number'] ?? 'INV-2025-0001' }}</div>
            <div class="badge {{ $payStatus === 'PAID' ? 'paid' : 'unpaid' }}">Bayar: {{ $payStatus }}</div>
            <div class="badge">{{ $orderStatus }}</div>
            <div class="muted small">
              {{ \Carbon\Carbon::parse($issuedAt)->format('d M Y H:i') }}
            </div>
          </div>
        </div>

        {{-- ================= BLOK INFO ================= --}}
        <div class="inv-cols">
          <div class="box">
            <h4>Pembeli</h4>
            <div class="strong">{{ $buyer['name'] ?? 'Budi Santoso' }}</div>
            <div class="muted">{{ $buyer['contact'] ?? 'budi@mail.com • 0812-0000-0000' }}</div>
          </div>
          <div class="box">
            <h4>Pengiriman</h4>
            <div>{{ $shipping['addr'] ?? 'Perum Mawar Blok A-12, Banyuwangi' }}</div>
            <div class="muted">Kurir: {{ $shipping['courier'] ?? 'JNE • Reguler' }}</div>
            <div class="muted">
              Resi: {{ $shipping['awb'] ?? 'JNE1234567890' }}
              @if(!empty($shipping['track']))
                • <a href="{{ $shipping['track'] }}" target="_blank" rel="noopener">Tracking</a>
              @endif
            </div>
          </div>
        </div>

        <div class="inv-cols">
          <div class="box">
            <h4>Pembayaran</h4>
            <div>Metode: <strong>{{ $payment['method'] ?? 'Virtual Account' }}</strong></div>
            <div class="muted">ID Transaksi: {{ $payment['txid'] ?? '-' }}</div>
            <div class="muted">
              Waktu Bayar:
              {{ !empty($payment['time']) ? \Carbon\Carbon::parse($payment['time'])->format('d M Y H:i') : '-' }}
            </div>
            <div class="badge" style="margin-top:6px">{{ $payment['status'] ?? 'Berhasil' }}</div>
          </div>
          <div class="box">
            <h4>Catatan</h4>
            <div class="note">{{ $note ?? 'Terima kasih telah berbelanja.' }}</div>
          </div>
        </div>

        {{-- ================= ITEM TABLE ================= --}}
        @php $subtotal = 0; @endphp
        <table class="items-table" aria-label="Item">
          <thead>
            <tr>
              <th>Item</th>
              <th>Varian</th>
              <th>Qty</th>
              <th>Harga</th>
              <th>Subtotal</th>
            </tr>
          </thead>
          <tbody>
            @foreach($itemsList as $it)
              @php
                $qty    = (int)($it['qty']   ?? 0);
                $price  = (float)($it['price'] ?? 0);
                $rowSub = $qty * $price;
                $subtotal += $rowSub;
              @endphp
              <tr>
                <td>
                  <div style="display:flex;align-items:center;gap:8px">
                    <img class="thumb" src="{{ $it['img'] ?? '' }}" alt="">
                    <div>{{ $it['name'] ?? '-' }}</div>
                  </div>
                </td>
                <td>{{ $it['variant'] ?? '—' }}</td>
                <td>{{ $qty }}</td>
                <td>{{ $fmt($price) }}</td>
                <td>{{ $fmt($rowSub) }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>

        {{-- ================= TOTALS ================= --}}
        @php
          $disc   = (float)($summary['disc']        ?? 0);
          $shipC  = (float)($summary['ship']        ?? 0);
          $tax    = (float)($summary['tax']         ?? 0);
          $taxPct = isset($summary['tax_percent']) ? (float)$summary['tax_percent'] : null;

          if ($taxPct !== null) {
            $tax = max(0, ($subtotal - $disc) * ($taxPct / 100));
          }

          $grand = max(0, $subtotal - $disc + $shipC + $tax);
        @endphp
        <div class="summary">
          <div></div>
          <div class="totals">
            <div class="row"><span>Subtotal</span><strong>{{ $fmt($subtotal) }}</strong></div>
            <div class="row"><span>Diskon/Voucher</span><strong>- {{ $fmt($disc) }}</strong></div>
            <div class="row"><span>Ongkir</span><strong>{{ $fmt($shipC) }}</strong></div>
            <div class="row">
              <span>Pajak{!! $taxPct !== null ? ' ('.$taxPct.'%)' : '' !!}</span>
              <strong>{{ $fmt($tax) }}</strong>
            </div>
            <div class="row grand"><span>Grand Total</span><strong>{{ $fmt($grand) }}</strong></div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <script src="{{ asset('js/customer/transaksi/invoice.js') }}"></script>
</body>
</html>
