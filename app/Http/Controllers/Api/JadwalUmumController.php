<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\JadwalUmumResource;
use Illuminate\Support\Facades\Validator;
use App\Models\JadwalUmum;
use App\Models\Kelas;
use App\Models\Instruktur;


class JadwalUmumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $jadwalUmum = JadwalUmum::with(['instruktur', 'kelas'])->latest('ID_JADWAL_UMUM')->orderBy('ID_JADWAL_UMUM', 'asc')->get();
        if(count($jadwalUmum)>0){
            return new JadwalUmumResource(true, 'List Data JadwalUmum', $jadwalUmum); 
        }// return data semua product dalam bentuk json
        else{
            return new JadwalUmumResource(false, 'Data JadwalUmum Kosong', null);
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
            'ID_KELAS' => 'required',
            'ID_INSTRUKTUR' => 'required',
            'SESI_KELAS' => 'required|date_format:H:i:s',
            'HARI_KELAS' => 'required',
        ]);
        if($validate->fails()){
            return response()->json($validate->errors(), 422);
        }

        $cekKelas = Kelas::where('ID_KELAS', $request->ID_KELAS)->first();
        if(!$cekKelas){
            return response([
                'message' => 'Kelas Tidak ada',
            ], 400);
        }

        $cekInstruktur = Instruktur::where('ID_INSTRUKTUR', $request->ID_INSTRUKTUR)->first();
        if(!$cekInstruktur){
            return response([
                'message' => 'Instruktur Tidak ada',
            ], 400);
        }

        
        $jadwalUmum = JadwalUmum::create($storeData);
        return new JadwalUmumResource(true, 'Add JadwalUmum Success', $jadwalUmum);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $jadwalUmum = JadwalUmum::with(['instruktur', 'kelas'])->where('ID_JADWAL_UMUM', $id)->first();

        if(!is_null($jadwalUmum)){
            return response([
                'message' => 'Berhasil Menerima data',
                'data' => $jadwalUmum
            ], 200);
        }

        return response([
            'message' => 'Data Tidak ada',
            'data' => null,
        ], 400);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $jadwalUmum = JadwalUmum::find($id);
        if(is_null($jadwalUmum)){
            return response([
                'message' => 'Data Tidak ada',
                'data' => null,
            ], 400);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'ID_KELAS' => 'required',
            'ID_INSTRUKTUR' => 'required',
            'SESI_KELAS' => 'required|date_format:H:i:s',
            'HARI_KELAS' => 'required',
        ]);

        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }

        $cekKelas = Kelas::where('ID_KELAS', $request->ID_KELAS)->first();
        if (!$cekKelas) {
            return response([
                'message' => 'Kelas Tidak ada',
            ], 400);
        }

        $cekInstruktur = Instruktur::where('ID_INSTRUKTUR', $request->ID_INSTRUKTUR)->first();
        if (!$cekInstruktur) {
            return response([
                'message' => 'Instruktur Tidak ada',
            ], 400);
        }

        $jadwalUmum->ID_KELAS = $updateData['ID_KELAS'];
        $jadwalUmum->ID_INSTRUKTUR = $updateData['ID_INSTRUKTUR'];
        $jadwalUmum->SESI_KELAS = $updateData['SESI_KELAS'];
        $jadwalUmum->HARI_KELAS = $updateData['HARI_KELAS'];
        
        if($jadwalUmum->save()){
            return response([
                'message' => 'Berhasil Mengubah Jadwal',
                'data' => $jadwalUmum,
            ], 200);
        }

        return response([
            'message' => 'Gagal Mengubah Jadwal',
            'data' => null,
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $jadwalUmum = JadwalUmum::find($id);

        if(is_null($jadwalUmum)){
            return response([
                'message' => 'Data Tidak ada',
                'data' => null,
            ], 400);
        }

        if($jadwalUmum->delete()){
            return response([
                'message' => 'Berhasil Menghapus Jadwal',
                'data' => $jadwalUmum,
            ], 200);
        }

        return response([
            'message' => 'Gagal Menghapus Jadwal',
            'data' => null,
        ], 400);
    }
}
