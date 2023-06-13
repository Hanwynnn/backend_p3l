<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class JadwalUmum extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $table = 'jadwal_umum';
    protected $primaryKey = 'ID_JADWAL_UMUM';
    protected $keyType = 'string';
    public $timestamps = false;
    public $incrementing = false;

    public static function generateIdJadwalUmum()
    {
        $lastId = self::select('ID_JADWAL_UMUM')->orderBy('ID_JADWAL_UMUM', 'desc')->first();
        $lastIdNumber = substr($lastId->ID_JADWAL_UMUM, 5);
        $newIdNumber = str_pad($lastIdNumber + 1, 2, '0', STR_PAD_LEFT);
        $newIdJadwalUmum = 'JU-1-'. $newIdNumber;
        return $newIdJadwalUmum;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->ID_JADWAL_UMUM = self::generateIdJadwalUmum();
        });
    }

    protected $fillable =[
        'ID_KELAS',
        'ID_INSTRUKTUR',
        'SESI_KELAS',
        'HARI_KELAS'
    ];

    //make relation with instruktur and kelas
    public function kelas(){
        return $this->belongsTo(Kelas::class,'ID_KELAS');
    }

    public function instruktur(){
        return $this->belongsTo(Instruktur::class,'ID_INSTRUKTUR');
    }

    public function jadwal_harian(){
        return $this->hasMany(JadwalHarian::class,'ID_JADWAL_UMUM');
    }
}
