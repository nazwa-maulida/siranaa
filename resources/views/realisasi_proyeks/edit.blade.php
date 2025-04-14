@extends('layouts.template')

@section('title', 'Edit Realisasi Proyek')

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
                            <h4 class="card-title">Edit Realisasi Proyek</h4>

                            <form class="forms-sample" method="POST" action="{{ route('realisasi_proyeks.update', $rp->RPID) }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                @if(auth()->user()->perusahaan)
                                <input type="hidden" name="PerusahaanID" value="{{ auth()->user()->perusahaan->id }}">
                                @else
                                <div class="alert alert-danger">Akun Anda belum memiliki data perusahaan.</div>
                                @endif

                                <div class="form-group">
                                    <label for="ProyekID">Judul Proyek</label>
                                    <input type="text" class="form-control" value="{{ $rp->proyek->Judul ?? 'Tidak ada judul' }}" disabled>
                                    <input type="hidden" name="ProyekID" value="{{ $rp->ProyekID }}">
                                </div>

                                <div class="form-group">
                                    <label for="TglMulai">Tanggal Mulai</label>
                                    <input type="date" class="form-control" value="{{ $rp->TglMulai }}" disabled>
                                    <input type="hidden" name="TglMulai" value="{{ $rp->TglMulai }}">
                                </div>

                                <div class="form-group">
                                    <label for="TglSelesai">Tanggal Selesai</label>
                                    <input type="date" class="form-control" name="TglSelesai" value="{{ $rp->TglSelesai }}" required>
                                </div>
                                
                                <div class="form-group">
    <label for="Status">Status</label>
    <select name="Status" id="Status" class="form-control" required>
        <option value="dalam proses" {{ ($rp->Status == 'dalam proses' || $rp->Status == null) ? 'selected' : '' }}>Dalam Proses</option>
        <option value="selesai" {{ $rp->Status == 'selesai' ? 'selected' : '' }}>Selesai</option>
    </select>
</div>

                                <div class="form-group">
        <label for="Dokumentasi">Upload Dokumentasi</label>
        <div class="input-group">
            <input type="file" class="form-control file-upload-info @error('Dokumentasi') is-invalid @enderror" 
                name="Dokumentasi" id="Dokumentasi" 
                accept=".doc,.docx,.pdf,.xls,.xlsx" required>
        </div>
        <small class="text-muted">Format yang diterima: DOC, DOCX, PDF, XLS, XLSX (Max: 10MB)</small>
        @error('Dokumentasi')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
                               

                                <div class="form-group">
                                    <label for="Catatan">Catatan</label>
                                    <textarea class="form-control" name="Catatan" rows="4" required>{{ $rp->Catatan }}</textarea>
                                </div>
                                

                                <button type="submit" class="btn btn-primary mr-2">Update</button>
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