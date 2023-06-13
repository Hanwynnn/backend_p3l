<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class BookingGym extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $table = 'booking_gym';
    protected $primaryKey = 'ID_BOOKING_GYM';
    protected $keyType = 'string';
    public $timestamps = false;
    public $incrementing = false;

    // public function getAuthPassword()
    // {
    //     return $this->PASSWORD;
    // }

    public static function generateIdBookingGym()
    {
        $lastId = self::select('ID_BOOKING_GYM')->orderBy('ID_BOOKING_GYM', 'desc')->first();
        if($lastId == null){
            $newIdMember = date('y.m') . '.' . '001';
            return $newIdMember;
        }
        $lastIdNumber = substr($lastId->ID_BOOKING_GYM, 6);
        $newIdNumber = str_pad($lastIdNumber + 1, 3, '0', STR_PAD_LEFT);
        $newIdMember = date('y.m') . '.' . $newIdNumber;
        return $newIdMember;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->ID_BOOKING_GYM = self::generateIdBookingGym();
        });
    }

    protected $fillable =[
        'ID_MEMBER',        
        'TANGGAL_BOOKING_GYM',
        'WAKTU_GYM',
        'WAKTU_PRESENSI',
    ];

    public function member(){
        return $this->belongsTo(Member::class,'ID_MEMBER');
    }
}
