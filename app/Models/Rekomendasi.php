<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rekomendasi extends Model
{
    use HasFactory;

    protected $table = 'rekomendasis';
    protected $primaryKey = 'RekomendasiID'; 
    protected $fillable = [
        'ProyekID',
        'PerusahaanID',
        'MitraID',
        'Catatan',
        'FileAnggaran',
        'FileSPK',
        'Status',
        ];

        public function proyek()
        {
            return $this->belongsTo(Proyek::class, 'ProyekID', 'ProyekID');
        }

        public function perusahaan()
        {
            return $this->belongsTo(Perusahaan::class, 'PerusahaanID', 'PerusahaanID');
        }

        public function mitra()
        {
            return $this->belongsTo(Mitra::class, 'MitraID', 'MitraID');
        }
}

