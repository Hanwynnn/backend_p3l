<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\BookingKelasResource;
use Illuminate\Support\Facades\Validator;
use App\Models\BookingKelas;
use App\Models\Member;
use App\Models\JadwalHarian;
use App\Models\DepoKelas;

class BookingKelasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bookingKelas = BookingKelas::with(['jadwal.jadwal_umum.kelas','member','jadwal', 'jadwal.instruktur'])->orderBy('ID_BOOKING_KELAS','asc')->get();
        if(count($bookingKelas)>0){
            return new BookingKelasResource(true, 'List Data Booking Kelas', $bookingKelas); 
        }// return data semua product dalam bentuk json
        else{
            return new BookingKelasResource(false, 'Data Booking Kelas Kosong', null);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cekMember = BookingKelas::where('ID_BOOKING_KELAS', $id)->with(['member', 'jadwal', 'member.depo_kelas', 'jadwal.jadwal_umum.kelas', 'jadwal.instruktur'])->get();

        if(!is_null($cekMember)){
            return response([
                'message' => 'Retrieve Booking gym Success',
                'data' => $cekMember
            ],200);
        }

        return response([
            'message' => 'Instruktur Not Found',
            'data' => null
        ],404);
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function historyKelas($id)
    {
        $cekMember = BookingKelas::where('ID_MEMBER', $id)->with(['jadwal', 'kelas', 'jadwal.instruktur', 'jadwal.jadwal_umum'])->get();        

        if ($cekMember->isEmpty()) {
            return response([
                'message' => 'History Kelas Member Masih Kosong',
                'data' => null
            ], 404);
        } else {
            return response([
                'message' => 'Retrieve History Kelas Member Success',
                'data' => $cekMember
            ], 200);
        }
    }
}

