@extends('layouts.app')

@section('title', 'Permintaan Penarikan Dana')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Permintaan Penarikan Dana</li>
                    </ol>
                </div>
                <h4 class="page-title">Permintaan Penarikan Dana</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Penjual</th>
                                    <th>Jumlah</th>
                                    <th>Metode</th>
                                    <th>Status</th>
                                    <th>Tanggal Diajukan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>#001</td>
                                    <td>Toko Serba Ada</td>
                                    <td>Rp 1.500.000</td>
                                    <td>Bank BCA</td>
                                    <td><span class="badge bg-warning">Menunggu</span></td>
                                    <td>15 Okt 2025</td>
                                    <td>
                                        <div class="dropdown">
                                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown">
                                                <i class="mdi mdi-dots-horizontal"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="#" class="dropdown-item">Lihat Detail</a>
                                                <a href="#" class="dropdown-item">Setujui</a>
                                                <a href="#" class="dropdown-item">Tolak</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>#002</td>
                                    <td>Toko Fashion Murah</td>
                                    <td>Rp 2.300.000</td>
                                    <td>Bank Mandiri</td>
                                    <td><span class="badge bg-success">Disetujui</span></td>
                                    <td>14 Okt 2025</td>
                                    <td>
                                        <div class="dropdown">
                                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown">
                                                <i class="mdi mdi-dots-horizontal"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="#" class="dropdown-item">Lihat Detail</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection