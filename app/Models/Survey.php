<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{

    use HasFactory;
    protected $table = 'surveys';
    protected $primaryKey = 'SurveyID'; 
    protected $fillable = [
        'ProyekID',
        'PerusahaanID',
        'TglSurvey', 
        'Catatan',
        'Keputusan',
        ];

        public function proyek()
        {
            return $this->belongsTo(Proyek::class, 'ProyekID', 'ProyekID');
        }

        public function perusahaan()
        {
            return $this->belongsTo(Perusahaan::class, 'PerusahaanID', 'PerusahaanID');
        }

        protected static function booted()
        {
            static::created(function ($survey) {
            $proyek = Proyek::find($survey->ProyekID);

            if ($proyek && $proyek->Status == 'Diajukan') {
                $proyek->Status = 'Diambil';
                $proyek->save();
            }
        });
    }


}
