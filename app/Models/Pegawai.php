<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Pegawai extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'pegawai';
    protected $primaryKey = 'ID_PEGAWAI';
    protected $keyType = 'integer';
    public $timestamps = false;
    public $incrementing = false;

    // public function getAuthPassword()
    // {
    //     return $this->PASSWORD;
    // }

    public static function generateIdPegawai()
    {
        $lastId = self::select('ID_PEGAWAI')->orderBy('ID_PEGAWAI', 'desc')->first();
        $lastIdNumber = substr($lastId->ID_PEGAWAI, 6);
        $newIdPegawai = $lastIdNumber + 1;        
        return $newIdPegawai;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->ID_PEGAWAI = self::generateIdMember();
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'NAMA_PEGAWAI',
        'TANGGAL_LAHIR_PEGAWAI',
        'ALAMAT_PEGAWAI',
        'NOMOR_TELEPON',
        'password',
        'ROLE',
        'TANGGAL_MASUK',
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
        return $this->hasMany(TransaksiAktivasi::class, 'ID_PEGAWAI');
    }

    public function transaksi_depo_uang()
    {
        return $this->hasMany(TransaksiAktivasi::class, 'ID_PEGAWAI');
    }

    public function transaksi_depo_kelas()
    {
        return $this->hasMany(TransaksiDepoKelas::class, 'ID_PEGAWAI');
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    // ];    
}
