<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\TransaksiAktivasiResource;
use Illuminate\Support\Facades\Validator;
use App\Models\TransaksiAktivasi;
use App\Models\Member;
use App\Models\Pegawai;

class TransaksiAktivasiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $aktivasi = TransaksiAktivasi::with(['pegawai', 'member'])->latest('ID_TRANSAKSI_AKTIVASI')->orderBy('ID_TRANSAKSI_AKTIVASI', 'asc')->get();
        if(count($aktivasi)>0){
            return new TransaksiAktivasiResource(true, 'List Data Transaksi Aktivais', $aktivasi); 
        }// return data semua product dalam bentuk json
        else{
            return new TransaksiAktivasiResource(false, 'Data Transaksi Aktivasi Kosong', null);
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
            // 'ID_TRANSAKSI_AKTIVASI' => 'required',            
            'JENIS_PEMBAYARAN' => 'required',
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

        $storeData['TANGGAL_AKTIVASI'] = date('Y-m-d H:i:s');
        $storeData['JUMLAH_HARGA_AKTIVASI'] = 3000000;

        if($cekMember->STATUS == "Aktif"){            
            $cekMember->update([
                'TANGGAL_KADALUARSA' => date('Y-m-d', strtotime($cekMember->TANGGAL_KADALUARSA. ' + 1 year')),
            ]);
        }else{            
            $cekMember->STATUS = "Aktif";
            $cekMember->TANGGAL_KADALUARSA = date('Y-m-d', strtotime('+1 year'));
            $cekMember->save();
        }
        
        $aktivasi = TransaksiAktivasi::create($storeData);
        return new TransaksiAktivasiResource(true, 'Add Transaksi Aktivasi Success', $aktivasi);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function laporanAktivasi($bulan)
    {
        $pendapatan = TransaksiAktivasi::where('TANGGAL_AKTIVASI', 'like', $bulan.'%')->sum('JUMLAH_HARGA_AKTIVASI');

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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        $cekMember = TransaksiAktivasi::where('ID_TRANSAKSI_AKTIVASI', $id)->with(['member'])->get();

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
}
