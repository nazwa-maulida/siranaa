@extends('layouts.template')

@section('title', 'Realsiasi Proyek') {{-- Menambahkan title di tab browser --}}

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
                            <h4 class="card-title">Realsiasi Proyek</h4>

                            <form class="forms-sample" method="POST" action="{{ route('realisasi_proyeks.store') }}">
                                @csrf

                                @if(auth()->user()->perusahaan)
                                <input type="hidden" name="PerusahaanID" value="{{ auth()->user()->perusahaan->id }}">
                                @else
                                <div class="alert alert-danger">Akun Anda belum memiliki data perusahaan.</div>
                                @endif

                                <div class="form-group">
    <label>Judul Proyek</label>
    <input type="text" class="form-control" value="{{ $proyek->Judul }}" readonly>
    <input type="hidden" name="ProyekID" value="{{ $proyek->ProyekID }}">
</div>


                                <div class="form-group">
                                    <label for="TglMulai">Tgl Mulai</label>
                                    <input type="date" class="form-control" name="TglMulai" required>
                                </div>

                                <div class="form-group">
                                    <label for="TglSelesai">Tgl Selesai</label>
                                    <input type="date" class="form-control" name="TglSelesai" required>
                                </div>                    
                               
            
                                <div class="form-group">
                                    <label for="Catatan">Catatan</label>
                                    <textarea class="form-control" name="Catatan" rows="4" required></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-primary mr-2">Submit</button>
                                <a href="{{ route('realisasi_proyeks.index') }}" class="btn btn-light">Cancel</a>
                            
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>       
</div>
@endsection
