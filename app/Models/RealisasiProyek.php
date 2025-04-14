<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RealisasiProyek extends Model
{
    use HasFactory;

    protected $table = 'realisasi_proyeks';
    protected $primaryKey = 'RPID'; 
    protected $fillable = [
        'ProyekID',
        'PerusahaanID',
        'TglMulai',
        'TglSelesai', 
        'Status',
        'Catatan',
        'Dokumentasi'
        ];

        public function proyek()
        {
            return $this->belongsTo(Proyek::class, 'ProyekID', 'ProyekID');
        }

        public function perusahaan()
        {
            return $this->belongsTo(Perusahaan::class, 'PerusahaanID', 'PerusahaanID');
        }
}
