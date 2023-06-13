<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\MemberResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Laravel\Passport\PersonalAccessToken;

class AuthMemberController extends Controller
{
    public function username(){
        return 'ID_MEMBER';
    }

    public function loginMember(Request $request)
    {
        //Menampung semua data dari form login
        // $loginData = $request->only(['email','password']);
        $loginData = $request->all();   

        //melakukan validasi data
        $validate = Validator::make($loginData,[
            'ID_MEMBER' => 'required',
            'password' => 'required'
        ]);

        //jika validasi gagal
        if($validate->fails())
            return response(['message' => $validate->errors()],400);
        //jika attempt tidak dilakukan
        // @dd($loginData);
        // @dd(!Auth::attempt($loginData));
        if(!Auth::guard('member')->attempt($loginData))
            return response([
                'message' => "invalid credentials",
                'data' => $loginData,
            ],401);
        
        // if($user->email_verified_at == null){
        //     return response(['message' => "Your Accout Email must be verified before you can continue"],403);
        // }
        $guard = Auth::guard('member')->user();
        $token = $guard->createToken('Authentication Token')->accessToken;
        
        return response([
            'message' => 'Authenticated',
            'user' => $guard,
            'token_type' => 'Bearer',
            'access_token' => $token,
            
        ]);
    }

    public function logoutMember(Request $request)
    {
        $request->user()->token()->revoke();
        return response([
            'message' => 'Successfully logged out'
        ]);
    }
}
