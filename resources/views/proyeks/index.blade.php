@extends('layouts.template')

@section('title', 'Data Proyek') {{-- Menambahkan title di tab browser --}}

@section('content')

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row justify-content-center"> 
            <div class="col-lg-12 grid-margin stretch-card"> {{-- Pastikan kolom cukup lebar --}}
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center">Data Proyek</h4>
                        <a href="{{ route('proyeks.create') }}" class="btn btn-primary mb-3">Tambah proyek</a>

                        @if(session('success'))
                        <div class="alert alert-success" role="alert" id="success-alert">
                            {{ session('success') }}
                        </div>
                        @endif
                        
                        <form action="{{ route('proyeks.index') }}" method="GET" class="mb-3">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Cari data proyek" value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="submit">Cari</button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive"> {{-- Tambahkan div ini untuk membuat tabel responsif --}}
                            <table class="table table-striped table-borderless">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Mitra</th>
                                        <th>Judul</th>
                                        <th>Deskripsi</th>
                                        <th>Lokasi</th>
                                        <th>Tgl Pengajuan</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($proyeks as $proyek)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $proyek->mitra->NamaMitra ?? 'Mitra Tidak Ditemukan' }}</td>
                                        <td>{{ $proyek->Judul }}</td>
                                        <td>{{ $proyek->Deskripsi }}</td>
                                        <td>{{ $proyek->Lokasi }}</td>
                                        <td>{{ $proyek->TglPengajuan }}</td>
                                        <td class="font-weight-medium">
                                            @if ($proyek->Status == 'Diajukan')
                                            <div class="badge badge-warning">Penawaran</div>
                                            @elseif ($proyek->Status == 'Diambil')
                                            <div class="badge badge-primary">Diambil</div>
                                            @elseif ($proyek->Status == 'Disurvey')
                                            <div class="badge badge-info">Disurvey</div>
                                            @elseif ($proyek->Status == 'Disetujui')
                                            <div class="badge badge-success">Disetujui</div>
                                            @if ($proyek->rekomendasi && $proyek->rekomendasi->FileAnggaran)
                                            <a href="{{ asset('storage/' . $proyek->rekomendasi->FileAnggaran) }}" target="_blank">Review Anggaran</a>
                                            @else
                                            <span class="text-danger">File anggaran tidak tersedia</span>
                                            @endif
                                            @elseif ($proyek->Status == 'Ditolak')
                                            <div class="badge badge-danger">Ditolak</div>
                                            @elseif ($proyek->Status == 'Dikerjakan')
                                            <div class="badge badge-warning">Dikerjakan</div>
                                            @elseif ($proyek->Status == 'Tidak Selesai')
                                            <div class="badge badge-danger">Tidak Selesai</div>
                                            @elseif ($proyek->Status == 'Selesai')
                                            <div class="badge badge-success">Selesai</div>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('proyeks.edit', $proyek->ProyekID) }}" class="btn btn-inverse-info btn-fw">Edit</a>
                                            <form method="POST" action="{{ route('proyeks.destroy', $proyek->ProyekID) }}" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-inverse-danger btn-fw" onclick="return confirm('Apakah anda yakin ingin menghapusnya?')" title="Hapus">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div> {{-- Akhir dari div.table-responsive --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
               

@endsection

