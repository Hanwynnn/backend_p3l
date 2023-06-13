<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class JadwalHarian extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $table = 'jadwal_harian';        
    public $timestamps = false;

    protected $fillable =[
        'TANGGAL_JADWAL_HARIAN',
        'ID_INSTRUKTUR',
        'ID_JADWAL_UMUM',
        'STATUS'
    ];

    //make relation with instruktur and jadwal umum
    public function instruktur(){
        return $this->belongsTo(Instruktur::class,'ID_INSTRUKTUR');
    }

    public function jadwal_umum(){
        return $this->belongsTo(JadwalUmum::class,'ID_JADWAL_UMUM');
    }

    public function perizinan_instruktur(){
        return $this->hasMany(PerizinanInstruktur::class,'id');
    }

    public function booking_kelas(){
        return $this->hasMany(BookingKelas::class,'ID_JADWAL_HARIAN');
    }
}
