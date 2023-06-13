<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class BookingKelas extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $table = 'booking_kelas';
    protected $primaryKey = 'ID_BOOKING_Kelas';
    protected $keyType = 'string';
    public $timestamps = false;
    public $incrementing = false;

    // public function getAuthPassword()
    // {
    //     return $this->PASSWORD;
    // }

    public static function generateIdBookingGym()
    {
        $lastId = self::select('ID_BOOKING_KELAS')->orderBy('ID_BOOKING_KELAS', 'desc')->first();
        if($lastId == null){
            $newIdMember = date('y.m') . '.' . '001';
            return $newIdMember;
        }
        $lastIdNumber = substr($lastId->ID_BOOKING_KELAS, 6);
        $newIdNumber = str_pad($lastIdNumber + 1, 3, '0', STR_PAD_LEFT);
        $newIdMember = date('y.m') . '.' . $newIdNumber;
        return $newIdMember;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->ID_BOOKING_KELAS = self::generateIdBookingGym();
        });
    }

    protected $fillable =[
        'ID_MEMBER',        
        'ID_JADWAL_HARIAN',
        'ID_KELAS',
        'WAKTU_PRESENSI',
        'PEMBAYARAN'
    ];

    public function member(){
        return $this->belongsTo(Member::class,'ID_MEMBER');
    }

    public function jadwal(){
        return $this->belongsTo(JadwalHarian::class,'ID_JADWAL_HARIAN');
    }

    public function kelas(){
        return $this->belongsTo(Kelas::class,'ID_KELAS');
    }
}
