<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyek extends Model
{
    use HasFactory;
    protected $table = 'proyeks';
    protected $primaryKey = 'ProyekID'; 
    protected $fillable = [
        'MitraID',
        'PerusahaanID',
        'Judul', 
        'Deskripsi',
        'Lokasi',
        'Status',
        'TglPengajuan'];

        public function mitra()
        {
            return $this->belongsTo(Mitra::class, 'MitraID', 'MitraID');
        }

        public function perusahaan()
        {
            return $this->belongsTo(Perusahaan::class, 'PerusahaanID', 'PerusahaanID');
        }

        public function surveys()
        {
            return $this->hasMany(Survey::class, 'ProyekID', 'ProyekID');
        }

        public function rekomendasi()
        {
            return $this->hasOne(Rekomendasi::class, 'ProyekID'); // Ganti 'proyek_id' dengan nama kolom yang sesuai
        }

}
