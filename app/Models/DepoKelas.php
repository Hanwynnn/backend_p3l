<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class DepoKelas extends Model
{
    use HasFactory, HasApiTokens;

    protected $table = 'deposit_kelas';
    protected $primaryKey = 'id_depo';
    public $timestamps = false;

    protected $fillable =[
        'ID_KELAS',
        'ID_MEMBER',
        'MASA_BERLAKU_DEPO',
        'SISA_DEPOSIT',
        'STATUS',
    ];

    public function member(){
        return $this->belongsTo(Member::class,'ID_MEMBER');
    }

    public function kelas(){
        return $this->belongsTo(Kelas::class,'ID_KELAS');
    }

    public function memberDepo(){
        return $this->hasMany(TransaksiDepoKelas::class,'ID_MEMBER');
    }
}
