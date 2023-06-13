<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Instruktur extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'instruktur';
    protected $primaryKey = 'ID_INSTRUKTUR';
    protected $keyType = 'string';
    public $timestamps = false;
    public $incrementing = false;

    // public function getAuthPassword()
    // {
    //     return $this->PASSWORD;
    // }

    public static function generateIdInstuktur()
    {
        $lastId = self::select('ID_INSTRUKTUR')->orderBy('ID_INSTRUKTUR', 'desc')->first();
        $lastIdNumber = substr($lastId->ID_INSTRUKTUR, 4);
        $newIdNumber = str_pad($lastIdNumber + 1, 2, '0', STR_PAD_LEFT);
        $newIdMember = '210-'. $newIdNumber;
        return $newIdMember;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->ID_INSTRUKTUR = self::generateIdInstuktur();
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'NAMA_INSTRUKTUR',
        'TANGGAL_LAHIR_INSTRUKTUR',
        'ALAMAT_INSTRUKTUR',
        'EMAIL_INSTRUKTUR',
        'password',
        'TELEPON_INSTRUKTUR',
        'JUMLAH_TELAT',
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

    public function jadwal_umums()
    {
        return $this->hasMany(JadwalUmum::class, 'ID_INSTRUKTUR');
    }

    public function jadwal_harians()
    {
        return $this->hasMany(JadwalHarian::class, 'ID_INSTRUKTUR');
    }

    public function perizinan_instrukturs()
    {
        return $this->hasMany(PerizinanInstruktur::class, 'ID_INSTRUKTUR');
    }

    public function perizinan_instrukturs_pengganti()
    {
        return $this->hasMany(PerizinanInstruktur::class, 'PENGGANTI');
    }

    public function presensi_instrukturs(){
        return $this->hasMany(PresensiInstruktur::class, 'ID_INSTRUKTUR');
    }
}
