@extends('layouts.template')

@section('title', 'Atur jadwal survey') {{-- Menambahkan title di tab browser --}}

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
                            <h4 class="card-title">Atur jadwal survey</h4>

                            <form class="forms-sample" method="POST" action="{{ route('surveys.store') }}">
                                @csrf

                                @if(auth()->user()->perusahaan)
                                <input type="hidden" name="PerusahaanID" value="{{ auth()->user()->perusahaan->PerusahaanID }}">
                                @else
                                <div class="alert alert-danger">Akun Anda belum memiliki data perusahaan.</div>
                                @endif

                                <input type="hidden" name="ProyekID" value="{{ $proyeks->ProyekID }}">

                                <div class="form-group">
                                    <label for="TglSurvey">Tgl Survey</label>
                                    <input type="date" class="form-control" name="TglSurvey" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="Catatan">Catatan</label>
                                    <textarea class="form-control" name="Catatan" rows="4" required></textarea>
                                </div>
                                
                               
                                <!-- <div class="form-group">
                                    <label for="Keputusan">Keputusan</label>
                                    <select class="form-control" name="Keputusan">
                                        <option>Pending</option>
                                        <option>Lanjut</option>
                                        <option>Tidak Lanjut</option>
                                    </select>
                                </div> -->
                                
                                
                                
                                <button type="submit" class="btn btn-primary mr-2">Submit</button>
                                <a href="{{ route('surveys.index') }}" class="btn btn-light">Cancel</a>
                            
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>       
</div>
@endsection
