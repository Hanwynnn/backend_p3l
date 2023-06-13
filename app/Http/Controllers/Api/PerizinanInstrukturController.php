<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\PerizinanInstrukturResource;
use Illuminate\Support\Facades\Validator;
use App\Models\PerizinanInstruktur;
use App\Models\Instruktur;
use App\Models\JadwalHarian;

class PerizinanInstrukturController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $perizinan = PerizinanInstruktur::with(['instruktur', 'jadwal_harian', 'jadwal_harian.jadwal_umum.kelas', 'pengganti'])->latest('ID_PERIZINAN_INSTRUKTUR')->orderBy('ID_PERIZINAN_INSTRUKTUR', 'asc')->get();
        if(count($perizinan)>0){
            return new PerizinanInstrukturResource(true, 'List Data Perizinan Instruktur', $perizinan); 
        }// return data semua product dalam bentuk json
        else{
            return new PerizinanInstrukturResource(false, 'Data Perizinan Instruktur Kosong', null);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
        $perizinan = PerizinanInstruktur::find($id);

        $updateData = $request->all();

        $cekJadwalHarian = JadwalHarian::where('id', $request->ID_JADWAL_HARIAN)->first();
        // if (!$cekJadwalHarian) {
        //     return response([
        //         'data' => $request->ID_JADWAL_HARIAN,
        //         'message' => 'Jadwal Harian Tidak ada',
        //     ], 400);
        // }
        $perizinan->STATUS = $updateData['STATUS'];

        if($perizinan->STATUS == "Diterima"){
            $cekJadwalHarian->STATUS = "Menggantikan";
            $cekJadwalHarian->ID_INSTRUKTUR = $perizinan->ID_PENGGANTI;
            $cekJadwalHarian->save();
        }else{
            
        }

        if($perizinan->save()){
            return response([
                'message' => 'update Perizinan success',
                'data' => $perizinan
            ], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function laporanLibur($id)
    {
        $pendapatan = PerizinanInstruktur::where('ID_INSTRUKTUR', $id)->get();

        $jumlah = count($pendapatan);
            
        return response([
            'message' => 'update Perizinan success',
            'data' => $jumlah
        ], 200);
    }
}
