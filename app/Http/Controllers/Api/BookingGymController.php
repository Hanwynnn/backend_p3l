<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\BookingGymResource;
use Illuminate\Support\Facades\Validator;
use App\Models\BookingGym;
use App\Models\Member;

class BookingGymController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bookingGym = BookingGym::with(['member'])->latest('ID_BOOKING_GYM')->orderBy('ID_BOOKING_GYM','asc')->get();
        if(count($bookingGym)>0){
            return new BookingGymResource(true, 'List Data Booking Gym', $bookingGym); 
        }// return data semua product dalam bentuk json
        else{
            return new BookingGymResource(false, 'Data Booking Gym Kosong', null);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $storeData = $request->all();

        $validate = Validator::make($storeData, [
            'ID_MEMBER' => 'required',
            'TANGGAL_BOOKING_GYM' => 'required',
            'WAKTU_GYM' => 'required',
        ]);

        if($validate->fails()){
            return response()->json($validate->errors(), 422);
        }

        $cekMember = Member::where('ID_MEMBER', $request->ID_MEMBER)->first();

        if($cekMember->STATUS == "Tidak Aktif"){
            return response([
                'message' => 'Member Tidak Aktif',
            ], 400);
        }

        $cekBooking = BookingGym::where('TANGGAL_BOOKING_GYM', $request->TANGGAL_BOOKING_GYM)->where('WAKTU_GYM', $request->WAKTU_GYM)->get();

        if(count($cekBooking)>9){
            return response([
                'message' => 'Booking Gym Sudah Penuh untuk sesi ini',
            ], 400);
        }
        
        $bookingGym = BookingGym::create($storeData);
        return new BookingGymResource(true, 'Booking Gym Berhasil Ditambahkan', $bookingGym);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $bookingGym = BookingGym::find($id);

        if(is_null($bookingGym)){
            return response([
                'message' => 'Booking Gym Not Found',
                'data' => null
            ],404);
        }

        $tanggalBookingGym = $bookingGym->TANGGAL_BOOKING_GYM;
        $hMinusOne = date('Y-m-d');

        if($bookingGym && $tanggalBookingGym > $hMinusOne){
            $bookingGym->delete();
            return response([
                'message' => 'Delete Booking Gym Success',
                'data' => $bookingGym,$tanggalBookingGym, $hMinusOne
            ],200);
        }else{
            return response([
                'message' => 'Delete Booking Gym Failed',
                'data' => $tanggalBookingGym, $hMinusOne
            ],400);
        }

        return response([
            'message' => 'Delete Booking Gym Failed',
            'data' => null
        ],400);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showbyidmember($id)
    {
        $cekMember = BookingGym::where('ID_MEMBER', $id)->get();

        if(!is_null($cekMember)){
            return response([
                'message' => 'Retrieve Instruktur Success',
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
    public function show($id)
    {
        $cekMember = BookingGym::where('ID_BOOKING_GYM', $id)->with(['member'])->get();

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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
        $bookingGym = BookingGym::find($id);
        $updateData = $request->all();

        $bookingGym->WAKTU_PRESENSI = date('Y-m-d H:i');

        if($bookingGym->save()){
            return response([
                'message' => 'update Booking Gym success',
                'data' => $bookingGym
            ], 200);
        }

        return response([
            'message' => 'Update Booking Gym Failed',
            'data' => null
        ],400);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function laporanGym($bulan)
    {
        $pendapatan = BookingGym::where('TANGGAL_BOOKING_GYM', 'like', $bulan)->get();

        $jumlahMember = count($pendapatan);
        return response([
            'message' => 'Retrieve Laporan Gym Success',
            'data' => $jumlahMember
        ],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function historyGym($id)
    {
        $cekMember = BookingGym::where('ID_MEMBER', $id)->get();

        if ($cekMember->isEmpty()) {
            return response([
                'message' => 'History Gym Member Masih Kosong',
                'data' => null
            ], 404);
        } else {
            return response([
                'message' => 'Retrieve History Gym Member Success',
                'data' => $cekMember
            ], 200);
        }
    }
}
