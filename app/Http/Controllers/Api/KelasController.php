<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\KelasResource;
use Illuminate\Support\Facades\Validator;
use App\Models\Kelas;
use App\Models\BookingKelas;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $kelas = Kelas::latest('ID_KELAS')->orderBy('ID_KELAS', 'asc')->get();
        if(count($kelas)>0){
            return new KelasResource(true, 'List Data Kelas', $kelas); 
        }// return data semua product dalam bentuk json
        else{
            return new KelasResource(false, 'Data Kelas Kosong', null);
        }
    }

    public function show($id)
    {
        $kelas = Kelas::find($id);

        if(!is_null($kelas)){
            return response([
                'message' => 'Retrieve Kelas Success',
                'data' => $kelas
            ],200);
        }

        return response([
            'message' => 'Kelas Not Found',
            'data' => null
        ],404);
    }

    public function laporanKelas($bulan)
    {
        $kelas = Kelas::with(['jadwal_umums.instruktur', 'booking_kelas'])
        ->whereHas('booking_kelas', function ($query) use ($bulan) {
            $query->where('TANGGAL_BOOKING', 'like', $bulan.'%');
        })
        ->orderBy('NAMA_KELAS', 'asc')
        ->get();        

        if(!is_null($kelas)){
            return response([
                'message' => 'Retrieve Kelas Success',
                'data' => $kelas                
            ],200);
        }

        return response([
            'message' => 'Kelas Not Found',
            'data' => null
        ],404);
    }
}
