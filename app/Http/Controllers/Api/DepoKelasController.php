<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\DepoKelasResource;
use Illuminate\Support\Facades\Validator;
use App\Models\DepoKelas;
use App\Models\Member;
use App\Models\Kelas;

class DepoKelasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $depokelas = DepoKelas::with(['kelas', 'member'])->latest('id_depo')->orderBy('id_depo','asc')->get();
        if(count($depokelas)>0){
            return new DepoKelasResource(true, 'List Data Depo Kelas', $depokelas); 
        }// return data semua product dalam bentuk json
        else{
            return new DepoKelasResource(false, 'Data Depo Kelas Kosong', null);
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
        $kelas = DepoKelas::find($id);
        $updateData = $request->all();

        $kelas->SISA_DEPOSIT = $updateData['SISA_DEPOSIT'];
        $kelas->STATUS = $updateData['STATUS'];

        if($kelas->save()){
            return response([
                'message' => 'update Instruktur success',
                'data' => $kelas
            ], 200);
        }

        return response([
            'message' => 'Update Instruktur Failed',
            'data' => null
        ],400);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showByMember($id)
    {
        $cekMember = DepoKelas::where('ID_MEMBER', $id)->with(['kelas'])->get();

        if ($cekMember->isEmpty()) {
            return response([
                'message' => 'History Depo Kelas Member Masih Kosong',
                'data' => null
            ], 404);
        } else {
            return response([
                'message' => 'Retrieve History Depo Kelas Member Success',
                'data' => $cekMember
            ], 200);
        }
    }
}
