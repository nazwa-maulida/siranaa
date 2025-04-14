@extends('layouts.template')

@section('title', 'Dashboard Perusahaan')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
         <!-- Welcome Header -->
         <div class="card mb-4">
            <div class="d-flex align-items-center row">
                <div class="col-sm-7">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Selamat datang, {{ Auth::check() && Auth::user()->role == 'perusahaan' && isset($perusahaan) ? $perusahaan->PIC : 'Pengguna' }}!🎉</h5>
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
        

        <!-- Project Section -->
        <div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header bg-light py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0 font-weight-medium">Daftar Proyek</h4>
                </div>
            </div>
            <div class="card-body py-4">
                <div class="row g-4">
                    @foreach ($proyeks as $proyek)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow border-0 project-card hover-scale">
                            <div class="ribbon-wrapper">
                                @if ($proyek->Status == 'Diajukan')
                                    <div class="ribbon ribbon-warning">Diajukan</div>
                                @elseif ($proyek->Status == 'Diambil')
                                    <div class="ribbon ribbon-primary">Diambil</div>
                                @elseif ($proyek->Status == 'Disurvey')
                                    <div class="ribbon ribbon-info">Disurvey</div>
                                @elseif ($proyek->Status == 'Disetujui')
                                    <div class="ribbon ribbon-success">Disetujui</div>
                                @elseif ($proyek->Status == 'Ditolak')
                                    <div class="ribbon ribbon-danger">Ditolak</div>
                                @elseif ($proyek->Status == 'Dikerjakan')
                                    <div class="ribbon ribbon-warning">Dikerjakan</div>
                                @elseif ($proyek->Status == 'Selesai')
                                    <div class="ribbon ribbon-success">Selesai</div>
                                @endif
                            </div>
                            
                            <div class="card-header bg-white border-bottom p-3">
                                <h5 class="card-title text-primary mb-0 text-truncate" title="{{ $proyek->Judul }}">{{ $proyek->Judul }}</h5>
                            </div>
                            
                            <div class="card-body p-3">
                                <div class="project-details mb-3">
                                    <div class="detail-item d-flex justify-content-between mb-2">
                                        <span class="detail-label"><i class="mdi mdi-account mr-1 text-primary"></i> Mitra:</span>
                                        <span class="detail-value font-weight-medium text-right">{{ $proyek->mitra->NamaMitra ?? 'Mitra Tidak Ditemukan' }}</span>
                                    </div>
                                    
                                    <div class="detail-item d-flex justify-content-between mb-2">
                                        <span class="detail-label"><i class="mdi mdi-text mr-1 text-primary"></i> Deskripsi:</span>
                                        <span class="detail-value text-right project-description" title="{{ $proyek->Deskripsi }}">{{ Str::limit($proyek->Deskripsi, 50) }}</span>
                                    </div>
                                    
                                    <div class="detail-item d-flex justify-content-between mb-2">
                                        <span class="detail-label"><i class="mdi mdi-map-marker mr-1 text-primary"></i> Lokasi:</span>
                                        <span class="detail-value text-right">{{ $proyek->Lokasi }}</span>
                                    </div>
                                    
                                    <div class="detail-item d-flex justify-content-between">
                                        <span class="detail-label"><i class="mdi mdi-calendar mr-1 text-primary"></i> Tanggal:</span>
                                        <span class="detail-value">{{ \Carbon\Carbon::parse($proyek->TglPengajuan)->format('d M Y') }}</span>
                                    </div>
                                </div>
                                
                                <div class="mt-3 text-center">
                                    @if ($proyek->Status == 'Diajukan')
                                        <span class="badge badge-pill badge-warning px-3 py-2">Status: Diajukan</span>
                                    @elseif ($proyek->Status == 'Diambil')
                                        <span class="badge badge-pill badge-primary px-3 py-2">Status: Diambil</span>
                                    @elseif ($proyek->Status == 'Disurvey')
                                        <span class="badge badge-pill badge-info px-3 py-2">Status: Disurvey</span>
                                    @elseif ($proyek->Status == 'Disetujui')
                                        <span class="badge badge-pill badge-success px-3 py-2">Status: Disetujui</span>
                                    @elseif ($proyek->Status == 'Ditolak')
                                        <span class="badge badge-pill badge-danger px-3 py-2">Status: Ditolak</span>
                                    @elseif ($proyek->Status == 'Dikerjakan')
                                        <span class="badge badge-pill badge-warning px-3 py-2">Status: Dikerjakan</span>
                                    @elseif ($proyek->Status == 'Selesai')
                                        <span class="badge badge-pill badge-success px-3 py-2">Status: Selesai</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="card-footer bg-white p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    @if ($proyek->Status == 'Diajukan')
                                        <a href="{{ route('ambilProyek', ['id' => $proyek->ProyekID]) }}" class="btn btn-warning btn-sm btn-block action-btn">
                                            <i class="mdi mdi-hand-right mr-1"></i> Penawaran
                                        </a>
                                    @elseif ($proyek->Status == 'Diambil')
                                        <a href="{{ route('proyeks.show', ['id' => $proyek->ProyekID]) }}" class="btn btn-primary btn-sm btn-block action-btn">
                                            <i class="mdi mdi-eye mr-1"></i> Lihat Detail
                                        </a>
                                    @elseif ($proyek->Status == 'Disurvey')
                                        <a href="{{ route('proyeks.show', ['id' => $proyek->ProyekID]) }}" class="btn btn-info btn-sm btn-block action-btn">
                                            <i class="mdi mdi-eye mr-1"></i> Lihat Detail
                                        </a>
                                    @elseif ($proyek->Status == 'Disetujui')
                                        <div class="w-100">
                                            <div class="row">
                                                <div class="col-12 mb-2">
                                                    <a href="{{ route('proyeks.show', ['id' => $proyek->ProyekID]) }}" class="btn btn-success btn-sm btn-block action-btn">
                                                        <i class="mdi mdi-eye mr-1"></i> Lihat Detail
                                                    </a>
                                                </div>
                                                
                                                @php
                                                // Cek apakah ada rekomendasi untuk proyek ini
                                                $rekomendasi = App\Models\Rekomendasi::where('ProyekID', $proyek->ProyekID)->first();
                                                @endphp
                                                
                                                <div class="col-12">
                                                    @if ($rekomendasi)
                                                        @if ($rekomendasi->FileSPK)
                                                            <a href="{{ route('realisasi_proyeks.create', ['id' => $proyek->ProyekID]) }}" class="btn btn-warning btn-sm btn-block action-btn">
                                                                <i class="mdi mdi-file-document mr-1"></i> Buat Realisasi
                                                            </a>
                                                        @else
                                                            <span class="badge badge-info btn-block p-2">Menunggu SPK</span>
                                                        @endif
                                                    @else
                                                        <a href="{{ route('rekomendasis.create', ['proyekId' => $proyek->ProyekID]) }}" class="btn btn-primary btn-sm btn-block action-btn">
                                                            <i class="mdi mdi-file-plus mr-1"></i> Buat Rekomendasi
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @elseif ($proyek->Status == 'Ditolak')
                                        <span class="badge badge-danger p-2 w-100">Proyek Ditolak</span>
                                    @elseif ($proyek->Status == 'Dikerjakan')
                                        <a href="{{ route('proyeks.show', ['id' => $proyek->ProyekID]) }}" class="btn btn-warning btn-sm btn-block action-btn">
                                            <i class="mdi mdi-hard-hat mr-1"></i> Sedang Dikerjakan
                                        </a>
                                    @elseif ($proyek->Status == 'Selesai')
                                        <a href="{{ route('proyeks.show', ['id' => $proyek->ProyekID]) }}" class="btn btn-light btn-sm btn-block border action-btn">
                                            <i class="mdi mdi-check-circle mr-1 text-success"></i> Proyek Selesai
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    
                    @if(count($proyeks) == 0)
                    <div class="col-12 text-center py-5">
                        <div class="text-muted">
                            <i class="mdi mdi-information-outline mr-1 mdi-48px d-block mb-3"></i>
                            <h5>Belum ada data proyek</h5>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
            
        <!-- Rekomendasi Section -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Data Rekomendasi</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center" width="5%">No</th>
                                        <th width="15%">Judul Proyek</th>
                                        <th width="15%">Nama Perusahaan</th>
                                        <th width="15%">Nama Mitra</th>
                                        <th width="15%">Catatan</th>
                                        <th width="10%">File Anggaran</th>
                                        <th width="10%">File SPK</th>
                                        <th width="5%">Status</th>
                                        <th width="10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($rekomens as $rkm)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ $rkm->proyek->Judul ?? 'Judul Tidak Ditemukan' }}</td>
                                        <td>{{ $rkm->perusahaan->NamaPerusahaan ?? 'Perusahaan Tidak Ditemukan' }}</td>
                                        <td>{{ $rkm->mitra->NamaMitra ?? 'Mitra Tidak Ditemukan' }}</td>
                                        <td>{{ $rkm->Catatan }}</td>
                                        <td class="text-center">
                                            @if($rkm->FileAnggaran)
                                            <a href="{{ Storage::url($rkm->FileAnggaran) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                                <i class="mdi mdi-file-pdf mr-1"></i> Lihat
                                            </a>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($rkm->FileSPK)
                                            <a href="{{ Storage::url($rkm->FileSPK) }}" target="_blank" class="btn btn-outline-success btn-sm">
                                                <i class="mdi mdi-file-document mr-1"></i> Lihat
                                            </a>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($rkm->Status == 'anggaran ditolak')
                                                <span class="badge badge-danger">Ditolak</span>
                                            @elseif($rkm->Status == 'disetujui')
                                                <span class="badge badge-success">Disetujui</span>
                                            @elseif($rkm->Status == 'diajukan')
                                                <span class="badge badge-warning">Diajukan</span>
                                            @else
                                                <span class="badge badge-secondary">{{ $rkm->Status }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($rkm->Status == 'anggaran ditolak')
                                            <form action="{{ route('rekomendasis.update', $rkm->RekomendasiID) }}" method="POST" enctype="multipart/form-data" class="d-flex flex-column">
                                                @csrf
                                                @method('PUT')
                                                <div class="custom-file mb-2" style="height: auto;">
                                                    <input type="file" class="custom-file-input" id="fileAnggaran{{ $rkm->RekomendasiID }}" name="FileAnggaran" accept=".doc,.docx,.pdf" required>
                                                    <label class="custom-file-label" for="fileAnggaran{{ $rkm->RekomendasiID }}">Pilih file</label>
                                                </div>
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <i class="mdi mdi-upload mr-1"></i> Upload
                                                </button>
                                            </form>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                    
                                    @if(count($rekomens) == 0)
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="text-muted">Belum ada data rekomendasi</div>
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

         <!-- Notifications Card -->
         @php
        $notifications = App\Models\Notification::where('to_id', Auth::id())
            ->where('type', 'mitra_to_perusahaan')
            ->where('dibaca', false)
            ->orderBy('created_at', 'desc')
            ->get();
        @endphp
        
        @if(count($notifications) > 0)
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow border-left-warning">
                    <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 font-weight-medium">
                            <i class="mdi mdi-bell-ring text-warning mr-2"></i>Notifikasi Terbaru
                        </h5>
                        <a href="#" id="markAllRead" class="btn btn-sm btn-outline-primary">
                            <i class="mdi mdi-check-all mr-1"></i>Tandai Semua Dibaca
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="notification-list">
                            @foreach($notifications as $notification)
                            <div class="notification-item p-3 mb-2 border-bottom" id="notification-{{ $notification->id }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="font-weight-medium mb-1">{{ $notification->judul }}</h6>
                                        <p class="text-dark mb-1">{{ $notification->pesan }}</p>
                                        <small class="text-muted">
                                            <i class="mdi mdi-clock-outline mr-1"></i>
                                            {{ $notification->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <div>
                                        <button class="btn btn-sm btn-light mark-read" data-id="{{ $notification->id }}">
                                            <i class="mdi mdi-check text-success"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                @if($notification->proyek_id)
                                <div class="mt-2">
                                    <a href="{{ route('proyeks.show', ['id' => $notification->proyek_id]) }}" class="btn btn-sm btn-outline-info">
                                        <i class="mdi mdi-eye mr-1"></i>Lihat Proyek
                                    </a>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Project Section -->
        <div class="row mb-4">
            <!-- Rest of your existing code -->
        </div>
        
        <!-- Rest of your existing code -->
    </div>
</div>

<style>
/* Your existing styles */

/* Additional styles for notification card */
.border-left-warning {
    border-left: 4px solid #f6c23e;
}

.notification-item {
    transition: all 0.2s ease;
    border-radius: 6px;
}

.notification-item:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

.notification-item:last-child {
    border-bottom: none !important;
}

.mark-read:hover {
    background-color: #e9ecef;
}
</style>



    </div>
</div>

<style>
/* Custom styling untuk card proyek */
.project-card {
    transition: all 0.3s ease;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1) !important;
}

.hover-scale:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15) !important;
}

.card-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.08);
}

.detail-label {
    color: #6c757d;
    font-weight: 600;
    font-size: 0.85rem;
    min-width: 80px;
}

.detail-value {
    font-size: 0.85rem;
    color: #212529;
    max-width: 60%;
}

.project-description {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.ribbon-wrapper {
    position: absolute;
    top: -3px;
    right: -3px;
    z-index: 1;
    overflow: hidden;
    width: 100px;
    height: 100px;
}

.ribbon {
    position: absolute;
    top: 15px;
    right: -25px;
    transform: rotate(45deg);
    width: 120px;
    padding: 5px 0;
    background-color: #f8f9fa;
    color: white;
    text-align: center;
    font-size: 0.75rem;
    font-weight: 600;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
}

.ribbon-primary {
    background-color: #4e73df;
}

.ribbon-success {
    background-color: #1cc88a;
}

.ribbon-info {
    background-color: #36b9cc;
}

.ribbon-warning {
    background-color: #f6c23e;
}

.ribbon-danger {
    background-color: #e74a3b;
}

.action-btn {
    font-weight: 500;
    transition: all 0.2s;
    padding: 0.5rem 0;
}

.action-btn:hover {
    transform: translateY(-2px);
}

.badge-pill {
    font-weight: 500;
    letter-spacing: 0.3px;
}

/* Improved spacing on mobile */
@media (max-width: 767px) {
    .col-md-4 {
        padding-left: 12px;
        padding-right: 12px;
    }
    
    .project-card {
        margin-bottom: 15px;
    }
}

/* Styling untuk kontainer utama */
.g-4 > * {
    padding: 12px;
}

.py-4 {
    padding-top: 1.5rem !important;
    padding-bottom: 1.5rem !important;
}
</style>

@endsection