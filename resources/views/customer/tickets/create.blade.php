@extends('layouts.app')

@section('title', 'Buat Tiket Bantuan Baru')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Kirim Tiket Bantuan</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('tickets.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="subject" class="form-label">Subjek Tiket <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="subject" name="subject" value="{{ old('subject') }}" required placeholder="Tulis subjek tiket Anda...">
                                    @if ($errors->has('subject'))
                                        <div class="text-danger mt-1">{{ $errors->first('subject') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Kategori <span class="text-danger">*</span></label>
                                    <select class="form-select" id="category" name="category" required>
                                        <option value="">Pilih Kategori</option>
                                        <option value="technical" {{ old('category') === 'technical' ? 'selected' : '' }}>Masalah Teknis</option>
                                        <option value="billing" {{ old('category') === 'billing' ? 'selected' : '' }}>Masalah Penagihan</option>
                                        <option value="account" {{ old('category') === 'account' ? 'selected' : '' }}>Masalah Akun</option>
                                        <option value="product" {{ old('category') === 'product' ? 'selected' : '' }}>Masalah Produk</option>
                                        <option value="other" {{ old('category') === 'other' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                    @if ($errors->has('category'))
                                        <div class="text-danger mt-1">{{ $errors->first('category') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="priority" class="form-label">Prioritas <span class="text-danger">*</span></label>
                                    <select class="form-select" id="priority" name="priority" required>
                                        <option value="">Pilih Prioritas</option>
                                        <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Rendah</option>
                                        <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>Menengah</option>
                                        <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>Tinggi</option>
                                        <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Darurat</option>
                                    </select>
                                    @if ($errors->has('priority'))
                                        <div class="text-danger mt-1">{{ $errors->first('priority') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="message" class="form-label">Pesan <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="message" name="message" rows="5" required placeholder="Jelaskan masalah Anda secara rinci...">{{ old('message') }}</textarea>
                                    @if ($errors->has('message'))
                                        <div class="text-danger mt-1">{{ $errors->first('message') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('customer.tickets') }}" class="btn btn-secondary">Kembali</a>
                            <button type="submit" class="btn btn-primary">Kirim Tiket</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection