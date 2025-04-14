@extends('layouts.template')

@section('title', 'Edit Data Rekomendasi') {{-- Menambahkan title di tab browser --}}

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
                            <h4 class="card-title">Edit data rekomendasi</h4>
                          

                            <form class="forms-sample" method="POST" action="{{ route('rekomendasis.update', $rekomendasi->RekomendasiID) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <input type="hidden" name="MitraID" value="{{ auth()->user()->mitra->id }}">

    <div class="form-group">
        <label for="Status">Status</label>
        <select class="form-control" name="Status" id="status">
            <option value="anggaran ditolak">Anggaran Ditolak</option>
            <option value="spk terbit">SPK Terbit</option>
        </select>
    </div>

    <div class="form-group" id="spkUploadSection" style="display: none;">
        <label for="FileSPK">Upload File SPK (PDF/DOC/DOCX, max: 10MB)</label>
        <input type="file" class="form-control" name="FileSPK" id="FileSPK" accept=".pdf,.doc,.docx">
    </div>

    <button type="submit" class="btn btn-primary mr-2">Submit</button>
    <a href="{{ route('rekomendasis.index') }}" class="btn btn-light">Cancel</a>
</form>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var statusSelect = document.getElementById("status");
        var spkUploadSection = document.getElementById("spkUploadSection");

        function toggleSPKUpload() {
            if (statusSelect.value === "spk terbit") {
                spkUploadSection.style.display = "block";
            } else {
                spkUploadSection.style.display = "none";
            }
        }

        statusSelect.addEventListener("change", toggleSPKUpload);
        toggleSPKUpload(); // Jalankan saat halaman dimuat
    });
</script>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>       
</div>
@endsection
