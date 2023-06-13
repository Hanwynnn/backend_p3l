<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class PerizinanInstruktur extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $table = 'perizinan_instruktur';
    protected $primaryKey = 'ID_PERIZINAN_INSTRUKTUR';
    protected $keyType = 'string';
    public $timestamps = false;
    public $incrementing = false;

    public static function generateIdPerizinanInstruktur()
    {
        $lastId = self::select('ID_PERIZINAN_INSTRUKTUR')->orderBy('ID_PERIZINAN_INSTRUKTUR', 'desc')->first();
        if(empty($lastId)){
            return "PI-". '01';
        }else{
            $lastIdNumber = substr($lastId->ID_PERIZINAN_INSTRUKTUR, 3);
            $newIdNumber = str_pad($lastIdNumber + 1, 2, '0', STR_PAD_LEFT);
        }        
        $newIdPerizinanInstruktur = 'PI-'. $newIdNumber;
        return $newIdPerizinanInstruktur;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->ID_PERIZINAN_INSTRUKTUR = self::generateIdPerizinanInstruktur();
        });
    }

    protected $fillable =[
        'ID_INSTRUKTUR',
        'ID_JADWAL_HARIAN',
        'ALASAN',
        'STATUS',
        'ID_PENGGANTI',
    ];

    public function instruktur(){
        return $this->belongsTo(Instruktur::class,'ID_INSTRUKTUR');
    }

    public function pengganti(){
        return $this->belongsTo(Instruktur::class,'ID_PENGGANTI');
    }

    public function jadwal_harian(){
        return $this->belongsTo(JadwalHarian::class,'ID_JADWAL_HARIAN');
    }
}
