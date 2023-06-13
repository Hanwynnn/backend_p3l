<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class PresensiInstruktur extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $table = 'presensi_instruktur';
    protected $primaryKey = 'ID_PRESENSI_INSTRUKTUR';
    protected $keyType = 'string';
    public $timestamps = false;
    public $incrementing = false;

    public static function generateIdPerizinanInstruktur()
    {
        $lastId = self::select('ID_PRESENSI_INSTRUKTUR')->orderBy('ID_PRESENSI_INSTRUKTUR', 'desc')->first();
        if(empty($lastId)){
            return "PRI-". '01';
        }else{
            $lastIdNumber = substr($lastId->ID_PRESENSI_INSTRUKTUR, 3);
            $newIdNumber = str_pad($lastIdNumber + 1, 2, '0', STR_PAD_LEFT);
        }        
        $newIdPerizinanInstruktur = 'PI-'. $newIdNumber;
        return $newIdPerizinanInstruktur;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->ID_PRESENSI_INSTRUKTUR = self::generateIdPerizinanInstruktur();
        });
    }

    protected $fillable =[
        'ID_INSTRUKTUR',
        'DURASI_TERLAMBAT',
        'JAM_MULAI',
        'JAM_SELESAI',
        'TANGGAL'
    ];

    public function instruktur(){
        return $this->belongsTo(Instruktur::class,'ID_INSTRUKTUR');
    }
}
