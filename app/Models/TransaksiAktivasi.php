<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class TransaksiAktivasi extends aUTHENTICATABLE
{
    use HasFactory, HasApiTokens;

    protected $table = 'transaksi_aktivasi';
    protected $primaryKey = 'ID_TRANSAKSI_AKTIVASI';
    protected $keyType = 'string';
    public $timestamps = false;
    public $incrementing = false;

    public static function generateIdTransaksiAktivasi()
    {
        $lastId = self::select('ID_TRANSAKSI_AKTIVASI')->orderBy('ID_TRANSAKSI_AKTIVASI', 'desc')->first();
        if($lastId == null){
            $newIdTransaksiAktivasi = date('y.m') . '.' . '001';
            return $newIdTransaksiAktivasi;
        }
        $lastIdNumber = substr($lastId->ID_TRANSAKSI_AKTIVASI, 6);
        $newIdNumber = str_pad($lastIdNumber + 1, 3, '0', STR_PAD_LEFT);
        $newIdTransaksiAktivasi = date('y.m') . '.' . $newIdNumber;
        return $newIdTransaksiAktivasi;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->ID_TRANSAKSI_AKTIVASI = self::generateIdTransaksiAktivasi();
        });
    }

    protected $fillable =[
        'ID_MEMBER',
        'ID_PEGAWAI',        
        'TANGGAL_AKTIVASI',        
        'JUMLAH_HARGA_AKTIVASI',
        'JENIS_PEMBAYARAN'
    ];

    public function member(){
        return $this->belongsTo(Member::class,'ID_MEMBER');
    }

    public function pegawai(){
        return $this->belongsTo(Pegawai::class,'ID_PEGAWAI');
    }
}
