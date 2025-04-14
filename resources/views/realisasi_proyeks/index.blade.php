@extends('layouts.template')

@section('title', 'Data Realisasi Proyek') {{-- Menambahkan title di tab browser --}}

@section('content')

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row justify-content-center"> 
            <div class="col-lg-12 grid-margin stretch-card"> {{-- Pastikan kolom cukup lebar --}}
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center">Data Realisasi Proyek</h4>
                     
                        

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
                            <th>Tgl Mulai</th>
                            <th>Tgl Selesai</th>
                            <th>Status</th>
                            <th>Catatan</th>
                            <th>Dokementasi&LPJ</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rps as $rp)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $rp->proyek->Judul }}</td>
                            <td>{{ $rp->perusahaan->NamaPerusahaan ?? 'Perusahaan Tidak Ditemukan' }}</td>
                            <td>{{ $rp->TglMulai }}</td>
                            <td>{{ $rp->TglSelesai }}</td>
                            <td>{{ $rp->Status }}</td>
                            <td>{{ $rp->Catatan }}</td>
                            <td>
    @if($rp->Dokumentasi)
        <a href="{{ asset('storage/' . $rp->Dokumentasi) }}" target="_blank">Lihat File</a>
    @endif
</td>


                            <td>
                                <a href="{{ route('realisasi_proyeks.edit', $rp->RPID) }}" class="btn btn-inverse-info btn-fw">Edit</a>
                                <form method="POST" action="{{ route('realisasi_proyeks.destroy', $rp->RPID) }}" style="display: inline;">
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


