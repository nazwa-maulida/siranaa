@extends('layouts.template')

@section('title', 'Data Rekomendasi') {{-- Menambahkan title di tab browser --}}

@section('content')

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row justify-content-center"> 
            <div class="col-lg-12 grid-margin stretch-card"> {{-- Pastikan kolom cukup lebar --}}
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center">Data Rekom+</h4>
                        @if(session('success'))
            <div class="alert alert-success" role="alert" id="success-alert">
                {{ session('success') }}
            </div>
            @endif
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Judul Proyek</th> 
                            <th>Nama Perusahaan</th>
                            <th>Nama Mitra</th>
                            <th>Catatan</th>
                            <th>File Anggaran</th>
                            <th>File SPK</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rekomens as $rkm)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $rkm->proyek->Judul ?? 'Judul Tidak Ditemukan' }}</td>
                            <td>{{ $rkm->perusahaan->NamaPerusahaan ?? 'Perusahaan Tidak Ditemukan' }}</td>
                            <td>{{ $rkm->mitra->NamaMitra ?? 'Mitra Tidak Ditemukan' }}</td>
                            <td>{{ $rkm->Catatan }}</td>
                            <td>
                                @if($rkm->FileAnggaran)
                                <a href="{{ asset('storage/' . $rkm->FileAnggaran) }}" target="_blank">Lihat File</a>
                                @endif
                            </td>
                            <td>
                                @if($rkm->FileSPK)
                                <a href="{{ asset('storage/' . $rkm->FileSPK) }}" target="_blank">Lihat SPK</a>
                                @endif
                            </td>
                            <td>{{ $rkm->Status }}</td>
                            <td>
                                <form method="POST" action="{{ route('rekomendasis.destroy', $rkm->RekomendasiID) }}" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-inverse-danger btn-fw" onclick="return confirm('Apakah anda yakin ingin menghapusnya?')" title="Hapus">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection