<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Kelas extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'kelas';
    protected $primaryKey = 'ID_KELAS';
    protected $keyType = 'integer';
    public $timestamps = false;
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id_kelas = static::count() + 101;
        });
    }

    protected $fillable =[
        'NAMA_KELAS',
        'HARGA_KELAS',
    ];

    public function jadwal_umums()
    {
        return $this->hasMany(JadwalUmum::class, 'ID_KELAS');
    }

    public function transaksi_depo_kelas()
    {
        return $this->hasMany(TransaksiDepoKelas::class, 'ID_Kelas');
    }
    
    public function depo_kelas()
    {
        return $this->hasMany(DepoKelas::class, 'ID_Kelas');
    }

    public function booking_kelas()
    {
        return $this->hasMany(BookingKelas::class, 'ID_KELAS');
    }
}
