<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Mitra extends Model
{
    use HasFactory;

    protected $table = 'mitras';
    protected $primaryKey = 'MitraID'; 
    protected $fillable = [
        'user_id',
        'NamaMitra',
        'PIC',
        'NoTelp',
        'Alamat',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
