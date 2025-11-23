@extends('layouts.app')

@section('title', 'Buat Tiket Bantuan Baru')

@section('content')
<style>
    .ticket-form-container {
        padding: 2rem 1rem;
        max-width: 800px;
        margin: 0 auto;
    }

    .ticket-form-card {
        background: var(--secondary-color, #fff);
        border-radius: 16px;
        box-shadow: 0 8px 30px rgba(0, 110, 92, 0.12);
        border: 1px solid var(--border-color, #e9ecef);
        overflow: hidden;
    }

    .ticket-form-header {
        background: var(--primary-color-dark, #006E5C);
        color: white;
        padding: 1.5rem 2rem;
        text-align: center;
    }

    .ticket-form-title {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 700;
    }

    .ticket-form-body {
        padding: 2rem;
    }

    .form-row {
        display: flex;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .form-group {
        flex: 1;
        margin-bottom: 0;
    }

    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: var(--text, #333);
        font-size: 0.95rem;
    }

    .form-control, .form-select {
        width: 100%;
        padding: 0.8rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.2s ease;
        background-color: #f8fafc;
    }

    .form-control:focus, .form-select:focus {
        outline: none;
        border-color: var(--primary-color-dark, #006E5C);
        box-shadow: 0 0 0 3px rgba(0, 110, 92, 0.1);
        background-color: #fff;
    }

    .form-control:required:valid {
        border-color: #cbd5e1;
    }

    textarea.form-control {
        min-height: 150px;
        resize: vertical;
    }

    .required {
        color: #dc3545;
    }

    .error-message {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: block;
    }

    .form-actions {
        display: flex;
        justify-content: space-between;
        margin-top: 2rem;
        gap: 1rem;
    }

    .btn {
        padding: 0.8rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-2px);
    }

    .btn-primary {
        background: var(--primary-color-dark, #006E5C);
        color: white;
    }

    .btn-primary:hover {
        background: #005a4a;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 110, 92, 0.25);
    }

    .form-info {
        background: #f0fdfa;
        border-left: 4px solid var(--primary-color-dark, #006E5C);
        padding: 1rem;
        margin-bottom: 1.5rem;
        border-radius: 0 8px 8px 0;
    }

    .form-info h5 {
        margin: 0 0 0.5rem 0;
        color: var(--primary-color-dark, #006E5C);
        font-weight: 600;
    }

    .form-info ul {
        margin: 0;
        padding-left: 1.2rem;
        color: #444;
    }

    .form-info li {
        margin-bottom: 0.3rem;
    }

    @media (max-width: 768px) {
        .form-row {
            flex-direction: column;
            gap: 1rem;
        }

        .ticket-form-container {
            padding: 1rem;
        }

        .ticket-form-body {
            padding: 1.5rem;
        }

        .form-actions {
            flex-direction: column;
        }
    }
</style>

<div class="ticket-form-container">
    <div class="ticket-form-card">
        <div class="ticket-form-header">
            <h4 class="ticket-form-title">Kirim Tiket Bantuan</h4>
            <p style="margin: 0.5rem 0 0 0; opacity: 0.8; font-size: 0.9rem;">Tim kami akan membantu Anda secepat mungkin</p>
        </div>

        <div class="ticket-form-body">
            <div class="form-info">
                <h5>Panduan Pengisian Formulir</h5>
                <ul>
                    <li>Jelaskan masalah Anda secara rinci dan spesifik</li>
                    <li>Pilih kategori yang paling sesuai dengan masalah Anda</li>
                    <li>Tentukan prioritas berdasarkan tingkat kepentingan</li>
                    <li>Tim kami akan merespon dalam waktu 24 jam</li>
                </ul>
            </div>

            <form action="{{ route('tickets.store') }}" method="POST">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label for="subject" class="form-label">Subjek Tiket <span class="required">*</span></label>
                        <input type="text" class="form-control" id="subject" name="subject"
                               value="{{ old('subject') }}" required placeholder="Contoh: Kesulitan saat checkout">
                        @if ($errors->has('subject'))
                            <div class="error-message">{{ $errors->first('subject') }}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="category" class="form-label">Kategori <span class="required">*</span></label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="">Pilih Kategori</option>
                            <option value="technical" {{ old('category') === 'technical' ? 'selected' : '' }}>Masalah Teknis</option>
                            <option value="billing" {{ old('category') === 'billing' ? 'selected' : '' }}>Masalah Penagihan</option>
                            <option value="account" {{ old('category') === 'account' ? 'selected' : '' }}>Masalah Akun</option>
                            <option value="product" {{ old('category') === 'product' ? 'selected' : '' }}>Masalah Produk</option>
                            <option value="other" {{ old('category') === 'other' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @if ($errors->has('category'))
                            <div class="error-message">{{ $errors->first('category') }}</div>
                        @endif
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="priority" class="form-label">Prioritas <span class="required">*</span></label>
                        <select class="form-select" id="priority" name="priority" required>
                            <option value="">Pilih Prioritas</option>
                            <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Rendah</option>
                            <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>Menengah</option>
                            <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>Tinggi</option>
                            <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Darurat</option>
                        </select>
                        @if ($errors->has('priority'))
                            <div class="error-message">{{ $errors->first('priority') }}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="message" class="form-label">Deskripsi Masalah <span class="required">*</span></label>
                        <textarea class="form-control" id="message" name="message" rows="5"
                                  required placeholder="Jelaskan masalah Anda secara rinci...">{{ old('message') }}</textarea>
                        @if ($errors->has('message'))
                            <div class="error-message">{{ $errors->first('message') }}</div>
                        @endif
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('customer.tickets') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left" style="margin-right: 0.5rem;"></i>
                        Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send" style="margin-right: 0.5rem;"></i>
                        Kirim Tiket
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection