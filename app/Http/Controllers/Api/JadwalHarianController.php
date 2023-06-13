<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\JadwalHarianResource;
use Illuminate\Support\Facades\Validator;
use App\Models\JadwalHarian;
use App\Models\JadwalUmum;
use App\Models\Instruktur;

class JadwalHarianController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $jadwalHarian = jadwalHarian::with(['instruktur', 'jadwal_umum', 'jadwal_umum.kelas'])->latest('id')->orderBy('id','asc')->get();
        if(count($jadwalHarian)>0){
            return new JadwalHarianResource(true, 'List Data Jadwal Harian', $jadwalHarian); 
        }// return data semua product dalam bentuk json
        else{
            return new JadwalHarianResource(false, 'Data Jadwal Harian Kosong', null);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $storeData = $request->all();

        $validate = Validator::make($storeData, [
            'ID_JADWAL_UMUM' => 'required',
            'ID_INSTRUKTUR' => 'required',
            'TANGGAL_JADWAL_HARIAN' => 'required',
        ]);
        if($validate->fails()){
            return response()->json($validate->errors(), 422);
        }

        $cekJadwal = JadwalUmum::where('ID_JADWAL_UMUM', $request->ID_JADWAL_UMUM)->first();
        if(!$cekJadwal){
            return response([
                'message' => 'Jadwal Umum Tidak ada',
            ], 400);
        }

        $cekInstruktur = Instruktur::where('ID_INSTRUKTUR', $request->ID_INSTRUKTUR)->first();
        if(!$cekInstruktur){
            return response([
                'message' => 'Instruktur Tidak ada',
            ], 400);
        }

        
        $jadwalHarian = JadwalHarian::create($storeData);
        return new JadwalHarianResource(true, 'Add jadwal Harian Success', $jadwalHarian);
    }

    public function update(Request $request, $id)
    {
        $jadwalHarian = JadwalHarian::find($id);
        if(is_null($jadwalHarian)){
            return response([
                'message' => 'Data Tidak ada',
                'data' => null,
            ], 400);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'ID_INSTRUKTUR' => 'required',            
            'STATUS' => 'required',
        ]);

        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }

        $cekInstruktur = Instruktur::where('ID_INSTRUKTUR', $request->ID_INSTRUKTUR)->first();
        if (!$cekInstruktur) {
            return response([
                'message' => 'Instruktur Tidak ada',
            ], 400);
        }

        $jadwalHarian->ID_INSTRUKTUR = $updateData['ID_INSTRUKTUR'];        
        $jadwalHarian->STATUS = $updateData['STATUS'];
        
        if($jadwalHarian->save()){
            return response([
                'message' => 'Berhasil Mengubah Jadwal',
                'data' => $jadwalHarian,
            ], 200);
        }

        return response([
            'message' => 'Gagal Mengubah Jadwal',
            'data' => null,
        ], 400);
    }

    public function show($id)
    {
        $jadwalHariam = JadwalHarian::with(['instruktur', 'jadwal_umum'])->where('id', $id)->first();

        if(!is_null($jadwalHariam)){
            return response([
                'message' => 'Berhasil Menerima data',
                'data' => $jadwalHariam
            ], 200);
        }

        return response([
            'message' => 'Data Tidak ada',
            'data' => null,
        ], 400);
    }
}
