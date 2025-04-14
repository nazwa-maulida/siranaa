<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'from_id',
        'to_id',
        'proyek_id',
        'rekomendasi_id',
        'judul',
        'pesan',
        'dibaca'
    ];

    public function proyek()
    {
        return $this->belongsTo(Proyek::class, 'proyek_id', 'ProyekID');
    }

    public function from()
    {
        if ($this->type == 'mitra_to_perusahaan') {
            return $this->belongsTo(Mitra::class, 'from_id', 'MitraID');
        } else {
            return $this->belongsTo(Perusahaan::class, 'from_id', 'PerusahaanID');
        }
    }

    public function to()
    {
        if ($this->type == 'mitra_to_perusahaan') {
            return $this->belongsTo(Perusahaan::class, 'to_id', 'PerusahaanID');
        } else {
            return $this->belongsTo(Mitra::class, 'to_id', 'MitraID');
        }
    }
}
