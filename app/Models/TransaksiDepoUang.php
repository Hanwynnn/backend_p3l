<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class TransaksiDepoUang extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $table = 'transaksi_deposit_uang';
    protected $primaryKey = 'ID_DEPOSIT_UANG';
    protected $keyType = 'string';
    public $timestamps = false;
    public $incrementing = false;

    public static function generateIdTransaksiDepoUang()
    {
        $lastId = self::select('ID_DEPOSIT_UANG')->orderBy('ID_DEPOSIT_UANG', 'desc')->first();
        if(empty($lastId)){
            return 'DU-' . '01';
        }else{
            $lastIdNumber = substr($lastId->ID_DEPOSIT_UANG, 3);
            $newIdNumber = str_pad($lastIdNumber + 1, 2, '0', STR_PAD_LEFT);
        }        
        $newIdTransaksiDepoUang = 'DU-' . $newIdNumber;
        return $newIdTransaksiDepoUang;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->ID_DEPOSIT_UANG = self::generateIdTransaksiDepoUang();
        });
    }

    protected $fillable = [
        'ID_MEMBER',
        'ID_PEGAWAI',
        'ID_PROMO',
        'TANGGAL_DEPOSIT',
        'JUMLAH_DEPOSIT',
        'BONUS_DEPOSIT',
        'SISA_DEPOSIT',
        'TOTAL_DEPOSIT_UANG',
        'JUMLAH_HARGA_DEPOSIT',
    ];

    public function member(){
        return $this->belongsTo(Member::class,'ID_MEMBER');
    }

    public function pegawai(){
        return $this->belongsTo(Pegawai::class,'ID_PEGAWAI');
    }
}
