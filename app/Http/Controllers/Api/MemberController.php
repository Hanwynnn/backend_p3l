<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\MemberResource;
use Illuminate\Support\Facades\Validator;
use App\Models\Member;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $member = Member::latest('ID_MEMBER')->get(); //mengambil semua data product

        if(count($member)>0){
            return new MemberResource(true, 'List Data Member', $member); 
        }// return data semua product dalam bentuk json
        else{
            return new MemberResource(false, 'Data Member Kosong', null);
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
            'NAMA_MEMBER' => 'required',
            'TANGGAL_LAHIR_MEMBER' => 'required',
            'ALAMAT_MEMBER' => 'required',
            'JENIS_KELAMIN' => 'required',
            'TELEPON_MEMBER' => 'required',                         
            'EMAIL_MEMBER' => 'required|email:rfc,dns|unique:member',
            'password' => 'required',
        ]);        

        if($validate->fails())
            return response()->json($validate->errors(), 422);

        $storeData['password'] = bcrypt($request->password);
        $storeData['STATUS'] = 'Tidak Aktif';
        $storeData['DEPOSIT_UANG'] = 0;
        $storeData['TANGGAL_KADALUARSA'] = date('Y/m/d', strtotime('-1 years'));

        $member = Member::create($storeData);
        // return response([
        //     'message' => 'Add member Success',
        //     'data' => $member
        // ], 200);
        return new MemberResource(true, 'Add member Success', $member);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $member = Member::find($id);

        if(!is_null($member)){
            return response([
                'message' => 'Retrieve Member Success',
                'data' => $member
            ],200);
        }

        return response([
            'message' => 'Member Not Found',
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
        $member = Member::find($id);
        if(is_null($member)){
            return response([
                'message' => 'Member Not Found',
                'data' => null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'NAMA_MEMBER' => 'required',
            'TANGGAL_LAHIR_MEMBER' => 'required',
            'ALAMAT_MEMBER' => 'required',
            'JENIS_KELAMIN' => 'required',
            'TELEPON_MEMBER' => 'required',                         
            'EMAIL_MEMBER' => 'required|email',            
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()],400);

        $member->NAMA_MEMBER = $updateData['NAMA_MEMBER'];
        $member->TANGGAL_LAHIR_MEMBER = $updateData['TANGGAL_LAHIR_MEMBER'];
        $member->ALAMAT_MEMBER = $updateData['ALAMAT_MEMBER'];
        $member->JENIS_KELAMIN = $updateData['JENIS_KELAMIN'];
        $member->TELEPON_MEMBER = $updateData['TELEPON_MEMBER'];        
        $member->EMAIL_MEMBER = $updateData['EMAIL_MEMBER'];
        $member->STATUS = $updateData['STATUS'];

        if($member->save()){
            return response([
                'message' => 'update Member success',
                'data' => $member
            ], 200);
        }

        return response([
            'message' => 'Update Member Failed',
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
        $member = Member::find($id);

        if(is_null($member)){
            return response([
                'message' => 'Member Not Found',
                'data' => null
            ],404);
        }

        if($member){
            $member->delete();
            return response([
                'message' => 'Delete Member Success',
                'data' => $member
            ],200);
        }

        return response([
            'message' => 'Delete Member Failed',
            'data' => null
        ],400);
    }
}
