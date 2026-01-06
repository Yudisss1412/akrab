@extends('layouts.app')

@section('title', 'Buat Tiket Bantuan Baru')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/customer/tickets/create.css') }}">
@endpush

@section('content')
<div class="ticket-form-container">
    <div class="ticket-form-card">
        <div class="ticket-form-header">
            <h4 class="ticket-form-title">Kirim Tiket Bantuan</h4>
            <p>Tim kami akan membantu Anda secepat mungkin</p>
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
                        <i class="bi bi-arrow-left"></i>
                        Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send"></i>
                        Kirim Tiket
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
