<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\TransaksiDepoUangResource;
use Illuminate\Support\Facades\Validator;
use App\Models\TransaksiDepoUang;
use App\Models\Member;
use App\Models\Pegawai;

class TransaksiDepoUangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $deposit = TransaksiDepoUang::with(['pegawai', 'member'])->latest('ID_DEPOSIT_UANG')->orderBy('ID_DEPOSIT_UANG', 'asc')->get();
        if(count($deposit)>0){
            return new TransaksiDepoUangResource(true, 'List Data Transaksi Deposit Uang', $deposit); 
        }// return data semua product dalam bentuk json
        else{
            return new TransaksiDepoUangResource(false, 'Data Transaksi Deposit Uang Kosong', null);
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
            'JUMLAH_DEPOSIT' => 'required',                                    
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

        if($cekMember->STATUS == "Tidak Aktif"){
            return response([
                'message' => 'Member Tidak Aktif',
            ], 400);
        }

        if($cekMember->DEPOSIT_UANG < 500000 && !empty($storeData['ID_PROMO'])){
            return response([
                'message' => 'Tidak Bisa Ambil Promo Karena Deposit Uang Kurang dari 500.000',
            ], 400);            
        }

        if($storeData['JUMLAH_DEPOSIT'] < 3000000 && $storeData['ID_PROMO']=="PR-1"){
            return response ([
                'message' => 'Tidak Bisa Ambil Promo Karena Deposit Uang Kurang dari 3.000.000',
            ], 400);
        }
        
        if($storeData['JUMLAH_DEPOSIT'] >= 3000000 && $storeData['ID_PROMO']=="PR-1" && $cekMember->DEPOSIT_UANG > 500000){
            $storeData['BONUS_DEPOSIT'] = 300000;            
        }else{
            $storeData['BONUS_DEPOSIT'] = 0;
        }
        $storeData['TANGGAL_DEPOSIT'] = date('Y/m/d H:i:s');
        $storeData['SISA_DEPOSIT'] = $cekMember->DEPOSIT_UANG;
        $storeData['TOTAL_DEPOSIT_UANG'] = $storeData['SISA_DEPOSIT'] + $request->JUMLAH_DEPOSIT + $storeData['BONUS_DEPOSIT'];

        $cekMember->DEPOSIT_UANG = $storeData['TOTAL_DEPOSIT_UANG'];
        $cekMember->save();

        
        $deposit = TransaksiDepoUang::create($storeData);
        return new TransaksiDepoUangResource(true, 'Add Transaksi Deposit Uang Success', $deposit);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function laporanDeposit($bulan)
    {
        $pendapatan = TransaksiDepoUang::where('TANGGAL_DEPOSIT', 'like', $bulan.'%')->sum('JUMLAH_DEPOSIT');

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
