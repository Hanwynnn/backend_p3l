<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\TransaksiDepoKelasResource;
use Illuminate\Support\Facades\Validator;
use App\Models\TransaksiDepoKelas;
use App\Models\Member;
use App\Models\Pegawai;
use App\Models\Kelas;
use App\Models\DepoKelas;

class TransaksiDepoKelasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $deposit = TransaksiDepoKelas::with(['member','kelas'])->latest('ID_DEPOSIT_KELAS')->orderBy('ID_DEPOSIT_KELAS', 'asc')->get();
        if(count($deposit)>0){
            return new TransaksiDepoKelasResource(true, 'List Data Transaksi Deposit Kelas', $deposit); 
        }// return data semua product dalam bentuk json
        else{
            return new TransaksiDepoKelasResource(false, 'Data Transaksi Deposit kelas Kosong', null);
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
            'ID_MEMBER' => 'required',
            'ID_PEGAWAI' => 'required',
            'ID_KELAS' => 'required',    
            'TOTAL_DEPOSIT' => 'required|in:5,10',        
        ]);
        if($validate->fails()){
            return response()->json($validate->errors(), 422);
        }

        $cekMember = Member::where('ID_MEMBER', $request->ID_MEMBER)->first();
        if(!$cekMember){
            return response([
                'message' => 'Member Tidak ada',
            ], 400);
        }

        $cekPegawai = Pegawai::where('ID_PEGAWAI', $request->ID_PEGAWAI)->first();
        if(!$cekPegawai){
            return response([
                'message' => 'Pegawai Tidak ada',
            ], 400);
        }

        $cekKelas = Kelas::where('ID_KELAS', $request->ID_KELAS)->first();
        if(!$cekKelas){
            return response([
                'message' => 'Kelas Tidak ada',
            ], 400);
        }         
        
        if($cekMember->STATUS == "Tidak Aktif"){
            return response([
                'message' => 'Member Tidak Aktif',
            ], 400);
        }

        if($request->TOTAL_DEPOSIT < 10 && $storeData['ID_PROMO'] == "PR-3"){
            return response([
                'message' => 'Promo Tidak Berlaku',
            ], 400);
        }else if($request->TOTAL_DEPOSIT < 5 && $storeData['ID_PROMO'] == "PR-2"){
            return response([
                'message' => 'Promo Tidak Berlaku',
            ], 400);
        }
        
        if($request->TOTAL_DEPOSIT >= 10 && $storeData['ID_PROMO'] == "PR-3"){
            $storeData['BONUS'] = 2;
            $storeData['MASA_BERLAKU'] = date('Y-m-d', strtotime('+2 month') );
        }else if($request->TOTAL_DEPOSIT >= 5 && $storeData['ID_PROMO'] == "PR-2"){
            $storeData['BONUS'] = 1;
            $storeData['MASA_BERLAKU'] = date('Y-m-d', strtotime('+1 month') );
        }else {
            $storeData['BONUS'] = 0;
            $storeData['MASA_BERLAKU'] = date('Y-m-d', strtotime('+1 month') );
        }

        $storeData['TANGGAL_DEPOSIT'] = date('Y-m-d H:i:s');
        $storeData['TOTAL_HARGA'] = $request->TOTAL_DEPOSIT * $cekKelas->HARGA_KELAS;

        $cekDepoMember = DepoKelas::where('ID_MEMBER', $request->ID_MEMBER)->where('ID_KELAS', $request->ID_KELAS)->first();  
        if(!$cekDepoMember){
            $storeDepo['ID_KELAS'] = $request->ID_KELAS;
            $storeDepo['ID_MEMBER'] = $request->ID_MEMBER;
            $storeDepo['MASA_BERLAKU_DEPO'] = $storeData['MASA_BERLAKU'];
            $storeDepo['SISA_DEPOSIT'] = $request->TOTAL_DEPOSIT + $storeData['BONUS'];
            $storeDepo['STATUS'] = "Aktif";
            DepoKelas::create($storeDepo);
        }else{  
            if($cekDepoMember->SISA_DEPOSIT > 0){
                return response([
                    'message_error' => 'Member Sudah Mempunyai Deposit Kelas',
                ],400);
            }else{
                $cekDepoMember->SISA_DEPOSIT = $cekDepoMember->SISA_DEPOSIT + $request->TOTAL_DEPOSIT + $storeData['BONUS'];
                $cekDepoMember->MASA_BERLAKU_DEPO = $storeData['MASA_BERLAKU'];
                $cekDepoMember->STATUS = "Aktif";
                $cekDepoMember->save();
            }
            
        }        
        $deposit = TransaksiDepoKelas::create($storeData);
        return new TransaksiDepoKelasResource(true, 'Add Transaksi Deposit Kelas Success', $deposit);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        $cekMember = TransaksiDepoKelas::where('ID_DEPOSIT_KELAS', $id)->with(['member', 'kelas'])->get();

        if(!is_null($cekMember)){
            return response([
                'message' => 'Retrieve Transaksi Aktivasi Success',
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
    public function laporanDepositKelas($bulan)
    {
        $pendapatan = TransaksiDepoKelas::where('TANGGAL_DEPOSIT', 'like', $bulan.'%')->sum('TOTAL_HARGA');

        if(!is_null($pendapatan)){
            return response([
                'message' => 'Retrieve pendapatan Success',
                'data' => $pendapatan
            ],200);
        }

        return response([
            'message' => 'pendapatan Not Found',
            'data' => null
        ],404);
    }
}
