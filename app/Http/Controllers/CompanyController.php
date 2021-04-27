<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdminResource;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\UserResource;
use App\Models\Admin;
use App\Models\Company;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use Illuminate\Http\Request;
use JWTAuth;
use Illuminate\Support\Facades\Hash;

class CompanyController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|unique:companies',
            'password' => 'required|confirmed',
            'phone' => 'required|unique:companies',
        ]);

        $data['password'] = bcrypt($request->password);

        $admin = Company::create($data);

        $token = JWTAuth::fromUser($admin);

        return response()->json([
            'message' => "Company Registered Successfully",
            'user' => CompanyResource::collection(Company::query()->where('id',$admin->id)->get()),
            'token' => $token,
        ]);
    }

    public function login(Request $request)
    {

        $data = $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);
        $admin = Company::where('email', $request->email)->first();
        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'msg' => "Cardinaltiess doesn't match",
            ],401);
        }
        $token = JWTAuth::fromUser($admin);
        return response()->json([
            'msg' => "Company login Successfully",
            'admin' => CompanyResource::collection(Company::query()->where('id',$admin->id)->get()),
            'token' => $token
        ]);

    }

    public function logout(){
        auth()->logout();
        return response()->json([
            'msg'=>'Company Sign out successfully'
        ],200);
    }

    public function getAllUser(){
        return response()->json([
            UserResource::collection(User::all())
        ],200);
    }

    public function getAllAdmin(){
        return response()->json([
            AdminResource::collection(Admin::all())
        ],200);
    }

    public function forgot(Request $request)
    {
        $credentials = request()->validate(['email' => 'required|email']);
        if(Company::query()->where('email',$credentials)->doesntExist()){
            return response()->json([
                "msg" => "Email Not found"
            ],404);
        }
        Password::sendResetLink($credentials);

        return response()->json(["msg" => 'Reset password link sent on your email id.']);
    }


}
