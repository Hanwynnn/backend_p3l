<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class TransaksiDepoKelas extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $table = 'transaksi_deposit_kelas';
    protected $primaryKey = 'ID_DEPOSIT_KELAS';
    protected $keyType = 'string';
    public $timestamps = false;
    public $incrementing = false;

    public static function generateIdTransaksiDepoKelas()
    {
        $lastId = self::select('ID_DEPOSIT_KELAS')->orderBy('ID_DEPOSIT_KELAS', 'desc')->first();
        if($lastId == null){
            $newIdTransaksiDepoKelas = date('y.m') . '.' . '001';
            return $newIdTransaksiDepoKelas;
        }
        $lastIdNumber = substr($lastId->ID_DEPOSIT_KELAS, 6);
        $newIdNumber = str_pad($lastIdNumber + 1, 3, '0', STR_PAD_LEFT);
        $newIdTransaksiDepoKelas = date('y.m') . '.' . $newIdNumber;
        return $newIdTransaksiDepoKelas;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->ID_DEPOSIT_KELAS = self::generateIdTransaksiDepoKelas();
        });
    }

    protected $fillable = [
        'ID_MEMBER',
        'ID_PEGAWAI',
        'ID_KELAS',
        'ID_PROMO',
        'TANGGAL_DEPOSIT',
        'MASA_BERLAKU',
        'BONUS',
        'TOTAL_DEPOSIT',
        'TOTAL_HARGA'
    ];    

    public function member(){
        return $this->belongsTo(Member::class,'ID_MEMBER');
    }

    public function pegawai(){
        return $this->belongsTo(Pegawai::class,'ID_PEGAWAI');
    }

    public function kelas(){
        return $this->belongsTo(Kelas::class,'ID_KELAS');
    }

    public function depokelas(){
        return $this->hasMany(DepoKelas::class,'ID_MEMBER');
    }
}
