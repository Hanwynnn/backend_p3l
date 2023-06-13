<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
    Route::post('login', 'Api\AuthController@login');
    Route::post('loginInstruktur', 'Api\AuthController@loginInstruktur');    
    Route::post('loginMember', 'Api\AuthMemberController@loginMember');

    Route::group(['middleware' => 'auth:pegawaiM'], function(){
        Route::post('logout', 'Api\AuthController@logout');
        Route::apiResource('/member', App\Http\Controllers\Api\MemberController::class);     
        Route::apiResource('/jadwalumum', App\Http\Controllers\Api\JadwalUmumController::class);   
        Route::apiResource('/jadwalharian', App\Http\Controllers\Api\JadwalHarianController::class);   
        Route::apiResource('/instruktur', App\Http\Controllers\Api\InstrukturController::class);  
        Route::apiResource('/kelas', App\Http\Controllers\Api\KelasController::class);   
        Route::apiResource('/transaksiaktivasi', App\Http\Controllers\Api\TransaksiAktivasiController::class);   
        Route::apiResource('/transaksidepouang', App\Http\Controllers\Api\TransaksiDepoUangController::class);
        Route::apiResource('/transaksidepokelas', App\Http\Controllers\Api\TransaksiDepoKelasController::class);
        Route::apiResource('/depokelas', App\Http\Controllers\Api\DepoKelasController::class);
        Route::apiResource('/perizinaninstruktur', App\Http\Controllers\Api\PerizinanInstrukturController::class); 
        Route::apiResource('/presensigym', App\Http\Controllers\Api\BookingGymController::class); 
        Route::apiResource('/presensikelas', App\Http\Controllers\Api\BookingKelasController::class);    
        Route::get('laporanAktivasi/{bulan}', 'Api\TransaksiAktivasiController@laporanAktivasi');
        Route::get('laporanDeposit/{bulan}', 'Api\TransaksiDepoUangController@laporanDeposit');
        Route::get('laporanDepositKelas/{bulan}', 'Api\TransaksiDepoKelasController@laporanDepositKelas');
        Route::get('laporanGym/{bulan}', 'Api\BookingGymController@laporanGym');        
        Route::get('laporanLibur/{id}', 'Api\PerizinanInstrukturController@laporanLibur');
        Route::get('laporanKelas/{bulan}', 'Api\KelasController@laporanKelas');
    });

    Route::group(['middleware' => 'auth:instrukturM'], function(){
        Route::post('logoutInstruktur', 'Api\AuthController@logoutInstruktur'); 
        Route::apiResource('/profileinstruktur', App\Http\Controllers\Api\InstrukturController::class);
    });

    Route::group(['middleware' => 'auth:memberM'], function(){
        Route::apiResource('/bookinggym', App\Http\Controllers\Api\BookingGymController::class);   
        Route::post('logoutMember', 'Api\AuthMemberController@logoutMember');     
        Route::get('bookinggymM/{id}', 'Api\BookingGymController@showbyidmember');
        Route::apiResource('/profilemember', App\Http\Controllers\Api\MemberController::class);
        Route::get('depokelasM/{id}', 'Api\DepoKelasController@showByMember');
        Route::get('historyKelas/{id}', 'Api\BookingKelasController@historyKelas');
        Route::get('historyGym/{id}', 'Api\BookingGymController@historyGym');
    });