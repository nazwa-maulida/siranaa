<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perusahaan extends Model
{
    use HasFactory;

    protected $table = 'perusahaans';
    protected $primaryKey = 'PerusahaanID'; 
    protected $fillable = [
        'user_id',
        'NamaPerusahaan',
        'PIC',
        'NoTelp',
        'Alamat',
    ];

   public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function proyek()
    {
        return $this->hasMany(Proyek::class, 'PerusahaanID');
    }
    
}
