<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Member extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'member';
    protected $primaryKey = 'ID_MEMBER';
    protected $keyType = 'string';
    public $timestamps = false;
    public $incrementing = false;

    public static function generateIdMember()
    {
        $lastId = self::select('ID_MEMBER')->orderBy('ID_MEMBER', 'desc')->first();
        $lastIdNumber = substr($lastId->ID_MEMBER, 6);
        $newIdNumber = str_pad($lastIdNumber + 1, 2, '0', STR_PAD_LEFT);
        $newIdMember = date('y.m') . '.' . $newIdNumber;
        return $newIdMember;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->ID_MEMBER = self::generateIdMember();
        });
    }


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [        
        'NAMA_MEMBER',
        'TANGGAL_LAHIR_MEMBER',
        'ALAMAT_MEMBER',
        'JENIS_KELAMIN',
        'TELEPON_MEMBER',
        'STATUS',
        'TANGGAL_KADALUARSA',
        'DEPOSIT_UANG',
        'EMAIL_MEMBER',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function transaksi_aktivasi()
    {
        return $this->hasMany(TransaksiAktivasi::class, 'ID_MEMBER');
    }

    public function transaksi_depo_uang()
    {
        return $this->hasMany(TransaksiDepoUang::class, 'ID_MEMBER');
    }

    public function transaksi_depo_kelas()
    {
        return $this->hasMany(TransaksiDepoKelas::class, 'ID_MEMBER');
    }

    public function depo_kelas()
    {
        return $this->hasMany(DepoKelas::class, 'ID_MEMBER');
    }

    public function booking_gym()
    {
        return $this->hasMany(BookingGym::class, 'ID_MEMBER');
    }
}
