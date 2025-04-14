@extends('layouts.template')

@section('title', 'Edit Data Proyek') {{-- Menambahkan title di tab browser --}}

@section('content')

@if ($errors->any())
    <div style="color: red;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="content-wrapper">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth px-0">
            <div class="row w-100 mx-0">
                <div class="col-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Edit proyek anda</h4>
                            <p class="card-description">Ajukan proyek Anda</p>

                            <form class="forms-sample" method="POST" action="{{ route('proyeks.update', $proyek->ProyekID) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="_method" value="put" />
                            

                                {{-- Input Hidden untuk MitraID (otomatis dari user yang login) --}}
                                <input type="hidden" name="MitraID" value="{{ auth()->user()->mitra->id }}">

                                <div class="form-group">
                                    <label for="Judul">Judul</label>
                                    <input type="text" class="form-control" name="Judul" value="{{ old('Judul', $proyek->Judul) }}" placeholder="Judul proyek Anda" >
                                </div>
                                
                                <div class="form-group">
                                    <label for="Deskripsi">Deskripsi</label>
                                    <textarea class="form-control" name="Deskripsi" rows="4" required></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label for="Lokasi">Lokasi</label>
                                    <input type="text" class="form-control" name="Lokasi" value="{{ old('Lokasi', $proyek->Lokasi) }}" placeholder="Lokasi proyek Anda" >
                                </div>
                               
                                <div class="form-group">
                                    <label for="Status">Status</label>
                                    <select class="form-control" name="Status">
                                        <option>Diajukan</option>
                                        <option>Diambil</option>
                                        <option>Disurvey</option>
                                        <option>Disetujui</option>
                                        <option>Ditolak</option>
                                        <option>Dikerjakan</option>
                                        <option>Selesai</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="TglPengajuan">Tgl Pengajuan</label>
                                    <input type="date" class="form-control" name="TglPengajuan" value="{{ old('TglPengajuan', $proyek->TglPengajuan) }}">
                                </div>
                                
                                <button type="submit" class="btn btn-primary mr-2">Submit</button>
                                <a href="{{ route('proyeks.index') }}" class="btn btn-light">Cancel</a>
                            
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>       
</div>
@endsection
