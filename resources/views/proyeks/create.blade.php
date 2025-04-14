@extends('layouts.template')

@section('title', 'Buat Data Proyek') {{-- Menambahkan title di tab browser --}}

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
                            <h4 class="card-title">Buat proyek baru</h4>
                            <p class="card-description">Ajukan proyek Anda</p>

                            <form class="forms-sample" method="POST" action="{{ route('proyeks.store') }}">
                                @csrf

                                @if(auth()->user()->mitra)
                                <input type="hidden" name="MitraID" value="{{ auth()->user()->mitra->MitraID }}">
                                @else
                                <div class="alert alert-danger">Akun Anda belum memiliki data mitra.</div>
                                @endif


                                <div class="form-group">
                                    <label for="Judul">Judul</label>
                                    <input type="text" class="form-control" name="Judul" placeholder="Judul proyek Anda" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="Deskripsi">Deskripsi</label>
                                    <textarea class="form-control" name="Deskripsi" rows="4" required></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label for="Lokasi">Lokasi</label>
                                    <input type="text" class="form-control" name="Lokasi" placeholder="Lokasi" required>
                                </div>
                               
                                <div class="form-group">
    <label for="Status">Status</label>
    <input type="text" class="form-control" name="Status" value="Diajukan" readonly>
</div>


                                
<div class="form-group">
                                    <label for="TglPengajuan">Tanggal Pengajuan</label>
                                    <input type="date" class="form-control" id="TglPengajuan" name="TglPengajuan" value="{{ date('Y-m-d') }}" readonly>
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
