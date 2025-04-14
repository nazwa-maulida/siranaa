@extends('layouts.template')

@section('title', 'Detail Proyek') {{-- Menambahkan title di tab browser --}}

@section('content')

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row justify-content-center"> 
            <div class="col-lg-12 grid-margin stretch-card"> {{-- Pastikan kolom cukup lebar --}}
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-center">Detail Proyek</h4>
                    <a href="{{ route('perusahaan.dashboard') }}" class="btn btn-secondary btn-sm">Kembali</a>
                </div>

               

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-8">
                            <h4>{{ $proyek->Judul }}</h4>
                            <p class="text-muted">Status: <span class="badge bg-{{ 
                                $proyek->Status == 'Diajukan' ? 'primary' :
                                ($proyek->Status == 'Diambil' ? 'info' :
                                ($proyek->Status == 'Disurvey' ? 'warning' :
                                ($proyek->Status == 'Disetujui' ? 'success' :
                                ($proyek->Status == 'Ditolak' ? 'danger' :
                                ($proyek->Status == 'Dikerjakan' ? 'dark' :
                                ($proyek->Status == 'Selesai' ? 'secondary' : 'secondary'))))))
                            }}">{{ $proyek->Status }}</span></p>
                            <p class="text-muted">Tanggal Pengajuan: {{ \Carbon\Carbon::parse($proyek->TglPengajuan)->format('d M Y') }}</p>
                        </div>
                        <div class="col-md-4">
                        @if($role == 'perusahaan' && $proyek->Status == 'Diambil' && !$survey)
    <a href="{{ route('surveys.create', ['ProyekID' => $proyek->ProyekID]) }}" class="btn btn-primary">Jadwalkan Survey</a>
@endif
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5>Deskripsi</h5>
                            <p>{{ $proyek->Deskripsi }}</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Informasi Mitra</h5>
                            <p><strong>Nama Mitra:</strong> {{ $proyek->mitra->NamaMitra }}</p>
                            <p><strong>PIC:</strong> {{ $proyek->mitra->PIC }}</p>
                            <p><strong>Alamat:</strong> {{ $proyek->mitra->Alamat }}</p>
                        </div>
                        @if($proyek->PerusahaanID)
                        <div class="col-md-6">
                            <h5>Informasi Perusahaan</h5>
                            <p><strong>Nama Perusahaan:</strong> {{ $proyek->perusahaan->NamaPerusahaan }}</p>
                            <p><strong>PIC:</strong> {{ $proyek->perusahaan->PIC }}</p>
                            <p><strong>Alamat:</strong> {{ $proyek->perusahaan->Alamat }}</p>
                        </div>
                        @endif
                    </div>

                    @if($survey)
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Informasi Survey</h5>
                </div>
                <div class="card-body">
                    <p><strong>Tanggal Survey:</strong> {{ \Carbon\Carbon::parse($survey->TglSurvey)->format('d M Y') }}</p>
                    <p><strong>Catatan:</strong> {{ $survey->Catatan }}</p>
                    <p><strong>Keputusan:</strong> {{ $survey->Keputusan }}</p>
                    
                    @if($role == 'perusahaan' && $survey->Keputusan == 'Pending')
                        <div class="mt-3">
                            <a href="{{ route('surveys.edit', $survey->SurveyID) }}" class="btn btn-warning">Konfirmasi Hasil Survey</a>
                        </div>
                    @elseif($role == 'mitra' && $survey->Keputusan == 'Tidak Lanjut')
                        <div class="mt-3">
                            <button class="btn btn-primary" onclick="confirmProjectContinuation()">Apakah Anda ingin melanjutkan proyek ini?</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @php
        $proyekID = $survey->ProyekID ?? $proyek->ProyekID;
    @endphp

    <script>
        function confirmProjectContinuation() {
            if (confirm("Apakah Anda yakin ingin melanjutkan proyek ini?")) {
                // Jika mitra memilih untuk melanjutkan, ubah status menjadi "Diajukan"
                window.location.href = "{{ route('proyek.lanjutkan', $proyekID) }}";
            } else {
                // Jika mitra memilih untuk tidak melanjutkan, selesaikan proyek
                window.location.href = "{{ route('proyek.tdkselesai', $proyekID) }}";
            }
        }
    </script>
@endif
                    @if($rekomendasi)
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Rekomendasi & Anggaran</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Status:</strong> {{ $rekomendasi->Status }}</p>
                                    <p><strong>Catatan:</strong> {{ $rekomendasi->Catatan }}</p>
                                    
                                    @if($rekomendasi->FileAnggaran)
                                        <p><strong>File Anggaran:</strong> <a href="{{ asset('storage/' . $rekomendasi->FileAnggaran) }}" target="_blank">Lihat File</a></p>
                                    @endif
                                    
                                    @if($rekomendasi->FileSPK)
                                        <p><strong>SPK:</strong> <a href="{{ asset('storage/' . $rekomendasi->FileSPK) }}" target="_blank">Lihat SPK</a></p>
                                    @endif
                                    
                                    @if($role == 'mitra' && $rekomendasi->Status == 'menunggu_persetujuan')
                                        <div class="mt-3">
                                            <a href="{{ route('rekomendasi.approve', $rekomendasi->id) }}" class="btn btn-success">Setujui Anggaran</a>
                                            <a href="{{ route('rekomendasi.reject', $rekomendasi->id) }}" class="btn btn-danger">Tolak Anggaran</a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($realisasi)
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Realisasi Proyek</h5>
                </div>
                <div class="card-body">
                    <p><strong>Tanggal Mulai:</strong> {{ \Carbon\Carbon::parse($realisasi->TglMulai)->format('d M Y') }}</p>
                    @if($realisasi->TglSelesai)
                        <p><strong>Tanggal Selesai:</strong> {{ \Carbon\Carbon::parse($realisasi->TglSelesai)->format('d M Y') }}</p>
                    @endif
                    <p><strong>Status:</strong> {{ $realisasi->Status }}</p>
                    
                    <div class="mt-3">
                        @if($role == 'perusahaan' && $realisasi->Status == 'dalam proses')
                            <a href="{{ route('realisasi.complete', $realisasi->RPID) }}" class="btn btn-success mr-2">Tandai Selesai</a>
                        @endif
                        
                        @if($role == 'perusahaan')
                            <a href="{{ route('realisasi_proyeks.edit', $realisasi->RPID) }}" class="btn btn-primary">Update Realisasi</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection