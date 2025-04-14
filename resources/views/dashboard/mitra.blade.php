@extends('layouts.template')

@section('title', 'Dashboard Mitra')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <!-- Welcome Header - Dengan margin bottom yang diperbaiki -->
        <div class="card mb-4">
            <div class="d-flex align-items-center row">
                <div class="col-sm-7">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Selamat datang, {{ Auth::check() && Auth::user()->role == 'mitra' && isset($mitra) ? $mitra->PIC : 'Pengguna' }}!🎉</h5>
                        <p class="mb-4">
                            Dashboard Mitra - Manajemen Proyek
                        </p>
                    </div>
                </div>
                <div class="col-sm-5 text-center text-sm-right">
                    <div class="card-body pb-0 px-0 px-md-4">
                        <img
                            src="/images/illustrations/girls-removebg-preview.png"
                            height="140"
                            alt="View Badge User"
                            style="max-width: 100%; object-fit: contain;" 
                            data-app-dark-img="illustrations/girls.png"
                            data-app-light-img="illustrations/girls.png"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Stats - Dengan gap/spacing yang diperbaiki -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3 mb-md-0">
                <div class="card bg-gradient-info text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-1">{{ $proyeks->where('Status', 'Diajukan')->count() }}</h4>
                                <p class="mb-0">Proyek Diajukan</p>
                            </div>
                            <div>
                                <i class="mdi mdi-file-document-outline mdi-36px"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3 mb-md-0">
                <div class="card bg-gradient-primary text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-1">{{ $proyeks->whereIn('Status', ['Diambil', 'Disurvey', 'Disetujui', 'Dikerjakan'])->count() }}</h4>
                                <p class="mb-0">Proyek Aktif</p>
                            </div>
                            <div>
                                <i class="mdi mdi-account-clock mdi-36px"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3 mb-md-0">
                <div class="card bg-gradient-success text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-1">{{ $proyeks->where('Status', 'Selesai')->count() }}</h4>
                                <p class="mb-0">Proyek Selesai</p>
                            </div>
                            <div>
                                <i class="mdi mdi-check-circle-outline mdi-36px"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3 mb-md-0">
                <div class="card bg-gradient-danger text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-1">{{ $proyeks->whereIn('Status', ['Ditolak', 'Tidak Selesai'])->count() }}</h4>
                                <p class="mb-0">Proyek Ditolak/Gagal</p>
                            </div>
                            <div>
                                <i class="mdi mdi-close-circle-outline mdi-36px"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Project Section - Dengan shadow dan padding yang ditingkatkan -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-light py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0 font-weight-medium">Data Proyek Anda</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped" style="border-spacing: 0 10px; ">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="5%" class="text-center">No</th>
                                        <th width="15%">Nama Mitra</th>
                                        <th width="15%">Judul</th>
                                        <th width="20%">Deskripsi</th>
                                        <th width="10%">Lokasi</th>
                                        <th width="10%">Tgl Pengajuan</th>
                                        <th width="10%">Tgl Survey</th>
                                        <th width="15%">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($proyeks as $proyek)
                                    <tr class="shadow-sm rounded" style="height: 60px;">
                                        <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                        <td class="align-middle">{{ $proyek->mitra->NamaMitra ?? 'Mitra Tidak Ditemukan' }}</td>
                                        <td class="align-middle">{{ $proyek->Judul }}</td>
                                        <td class="align-middle">{{ Str::limit($proyek->Deskripsi, 80) }}</td>
                                        <td class="align-middle">{{ $proyek->Lokasi }}</td>
                                        <td class="align-middle">{{ \Carbon\Carbon::parse($proyek->TglPengajuan)->format('d M Y') }}</td>
                                        <td class="align-middle">
                                            @if ($proyek->surveys->isNotEmpty())
                                                {{ \Carbon\Carbon::parse($proyek->surveys->last()->TglSurvey)->format('d M Y') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="align-middle py-3">
                                            <div class="d-flex flex-column">
                                                @if ($proyek->Status == 'Diajukan')
                                                    <div class="badge badge-warning p-2 mb-2">Penawaran</div>
                                                @elseif ($proyek->Status == 'Diambil')
                                                    <div class="badge badge-primary p-2 mb-2">Diambil</div>
                                                @elseif ($proyek->Status == 'Disurvey')
                                                    <a href="{{ route('proyeks.show', ['id' => $proyek->ProyekID]) }}" class="text-decoration-none">
                                                        <div class="badge badge-info p-2 mb-2">Disurvey</div>
                                                    </a>
                                                @elseif ($proyek->Status == 'Disetujui')
                                                    <div class="badge badge-success p-2 mb-2">Disetujui</div>
                                                    @if ($proyek->rekomendasi && $proyek->rekomendasi->FileAnggaran)
                                                        <div class="mt-2">
                                                            <a href="{{ asset('storage/' . $proyek->rekomendasi->FileAnggaran) }}" target="_blank" class="btn btn-outline-primary btn-sm mb-2 px-3">
                                                                <i class="mdi mdi-file-document mr-1"></i> Review Anggaran
                                                            </a>
                                                            <a href="{{ route('rekomendasis.edit', $proyek->rekomendasi->RekomendasiID) }}" class="btn btn-info btn-sm px-3">
                                                                <i class="mdi mdi-account-check mr-1"></i> Buat Keputusan
                                                            </a>
                                                        </div>
                                                    @else
                                                        <span class="text-danger mt-2"><i class="mdi mdi-alert-circle mr-1"></i> File anggaran tidak tersedia</span>
                                                    @endif
                                                @elseif ($proyek->Status == 'Ditolak')
                                                    <a href="{{ route('proyeks.show', ['id' => $proyek->ProyekID]) }}" class="text-decoration-none">
                                                        <div class="badge badge-danger p-2 mb-2">Ditolak</div>
                                                    </a>
                                                @elseif ($proyek->Status == 'Dikerjakan')
                                                    <div class="badge badge-warning p-2 mb-2">Dikerjakan</div>
                                                @elseif ($proyek->Status == 'Tidak Selesai')
                                                    <div class="badge badge-danger p-2 mb-2">Tidak Selesai</div>
                                                @elseif ($proyek->Status == 'Selesai')
                                                    <div class="badge badge-success p-2 mb-2">Selesai</div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    
                                    @if(count($proyeks) == 0)
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="mdi mdi-information-outline mr-1 mdi-36px d-block mb-2"></i>
                                                Belum ada data proyek
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom styling untuk memperbaiki jarak dan tampilan */
.content-wrapper {
    padding: 1.5rem 1.7rem;
}

.card {
    border: 0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 6px 14px rgba(0, 0, 0, 0.12);
}

/* Memperbaiki margin untuk layout mobile */
@media (max-width: 767px) {
    .mb-md-0 {
        margin-bottom: 1rem !important;
    }
}

/* Memperbaiki tampilan tabel */
.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

.badge {
    font-weight: 500;
    letter-spacing: 0.3px;
}

/* Memperbaiki tampilan card header */
.card-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.08);
}

/* Memperbaiki tampilan kartu statistik */
.bg-gradient-info {
    background: linear-gradient(135deg, #4099ff, #73b4ff);
}

.bg-gradient-primary { 
    background: linear-gradient(135deg, #2c69d1, #4a89dc);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #2ed8b6, #59e0c5);
}

.bg-gradient-danger {
    background: linear-gradient(135deg, #ff5370, #ff869a);
}
</style>
@endsection