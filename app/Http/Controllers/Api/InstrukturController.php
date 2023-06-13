<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\InstrukturResource;
use Illuminate\Support\Facades\Validator;
use App\Models\Instruktur;

class InstrukturController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $instruktur = Instruktur::with(['perizinan_instrukturs' => function ($query) {
            $query->where('STATUS', 'Diterima');}, 'presensi_instrukturs'])->orderBy('JUMLAH_TELAT', 'asc')->get(); //mengambil semua data product

        if(count($instruktur)>0){
            return new InstrukturResource(true, 'List Data Instruktur', $instruktur); // return data semua product dalam bentuk json
        }else{
            return new InstrukturResource(false, 'Data Instruktur Kosong', null);
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
        $validate = Validator::make($storeData,[
            'NAMA_INSTRUKTUR' => 'required',
            'TANGGAL_LAHIR_INSTRUKTUR' => 'required',
            'ALAMAT_INSTRUKTUR' => 'required',
            'EMAIL_INSTRUKTUR' => 'required|email:rfc,dns|unique:instruktur',
            'password' => 'required',
            'TELEPON_INSTRUKTUR' => 'required'
        ]);        

        if($validate->fails())
            return response()->json($validate->errors(), 422);

        $storeData['password'] = bcrypt($request->password);
        $storeData['JUMLAH_TELAT'] = 0;

        $instruktur = Instruktur::create($storeData);
        return new InstrukturResource(true, 'Instruktur Berhasil Ditambahkan', $instruktur); //return data product baru dalam bentuk json
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $instruktur = Instruktur::find($id);

        if(!is_null($instruktur)){
            return response([
                'message' => 'Retrieve Instruktur Success',
                'data' => $instruktur
            ],200);
        }

        return response([
            'message' => 'Instruktur Not Found',
            'data' => null
        ],404);
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
        $instruktur = Instruktur::find($id);
        if(is_null($instruktur)){
            return response([
                'message' => 'Instruktur Not Found',
                'data' => null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'NAMA_INSTRUKTUR' => 'required',
            'TANGGAL_LAHIR_INSTRUKTUR' => 'required',
            'ALAMAT_INSTRUKTUR' => 'required',
            'EMAIL_INSTRUKTUR' => 'required|email:rfc,dns',            
            'TELEPON_INSTRUKTUR' => 'required'
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()],400);

        $instruktur->NAMA_INSTRUKTUR = $updateData['NAMA_INSTRUKTUR'];
        $instruktur->TANGGAL_LAHIR_INSTRUKTUR = $updateData['TANGGAL_LAHIR_INSTRUKTUR'];
        $instruktur->ALAMAT_INSTRUKTUR = $updateData['ALAMAT_INSTRUKTUR'];
        $instruktur->EMAIL_INSTRUKTUR = $updateData['EMAIL_INSTRUKTUR'];        
        $instruktur->TELEPON_INSTRUKTUR = $updateData['TELEPON_INSTRUKTUR'];
        $instruktur->JUMLAH_TELAT = $updateData['JUMLAH_TELAT'];

        if($instruktur->save()){
            return response([
                'message' => 'update Instruktur success',
                'data' => $instruktur
            ], 200);
        }

        return response([
            'message' => 'Update Instruktur Failed',
            'data' => null
        ],400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $instruktur = Instruktur::find($id);

        if(is_null($instruktur)){
            return response([
                'message' => 'Instruktur Not Found',
                'data' => null
            ],404);
        }

        if($instruktur){
            $instruktur->delete();
            return response([
                'message' => 'Delete Instruktur Success',
                'data' => $instruktur
            ],200);
        }

        return response([
            'message' => 'Delete Instruktur Failed',
            'data' => null
        ],400);
    }
}
