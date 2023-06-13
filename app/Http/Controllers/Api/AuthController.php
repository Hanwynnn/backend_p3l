<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\PegawaiResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Laravel\Passport\PersonalAccessToken;

class AuthController extends Controller
{
    // public function username(){
    //     return 'nama';
    // }

    public function login(Request $request)
    {
        //Menampung semua data dari form login
        // $loginData = $request->only(['email','password']);
        $loginData = $request->all();   

        //melakukan validasi data
        $validate = Validator::make($loginData,[
            'NAMA_PEGAWAI' => 'required',
            'password' => 'required'
        ]);

        //jika validasi gagal
        if($validate->fails())
            return response(['message' => $validate->errors()],400);
        //jika attempt tidak dilakukan
        // @dd($loginData);
        // @dd(!Auth::attempt($loginData));
        if(!Auth::guard('pegawai')->attempt($loginData))
            return response(['message' => "invalid credentials"],401);
        
        // if($user->email_verified_at == null){
        //     return response(['message' => "Your Accout Email must be verified before you can continue"],403);
        // }
        $guard = Auth::guard('pegawai')->user();
        $token = $guard->createToken('Authentication Token')->accessToken;
        
        return response([
            'message' => 'Authenticated',
            'user' => $guard,
            'token_type' => 'Bearer',
            'access_token' => $token,
            
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response([
            'message' => 'Successfully logged out'
        ]);
    }

    public function loginInstruktur(Request $request)
    {
        //Menampung semua data dari form login
        // $loginData = $request->only(['email','password']);
        $loginInstruktur = $request->all();   

        //melakukan validasi data
        $validate = Validator::make($loginInstruktur,[
            'NAMA_INSTRUKTUR' => 'required',
            'password' => 'required'
        ]);

        //jika validasi gagal
        if($validate->fails())
            return response(['message' => $validate->errors()],400);
        //jika attempt tidak dilakukan
        // @dd($loginData);
        // @dd(!Auth::attempt($loginData));
        if(!Auth::guard('instruktur')->attempt($loginInstruktur))
            return response(['message' => "invalid credentials"],400);
        
        // if($user->email_verified_at == null){
        //     return response(['message' => "Your Accout Email must be verified before you can continue"],403);
        // }
        $guard = Auth::guard('instruktur')->user();
        $token = $guard->createToken('Authentication Token')->accessToken;
        
        return response([
            'message' => 'Authenticated',
            'user' => $guard,
            'token_type' => 'Bearer',
            'access_token' => $token,
            
        ]);
    }

    public function logoutInstruktur(Request $request)
    {
        $request->user()->token()->revoke();
        return response([
            'message' => 'Successfully logged out'
        ]);
    }
}
