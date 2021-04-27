<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdminResource;
use App\Http\Resources\UserResource;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use JWTAuth;
use Illuminate\Support\Facades\Hash;


class AdminController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|unique:admins',
            'password' => 'required|confirmed',
            'phone' => 'required|unique:admins',
        ]);

        $data['password'] = bcrypt($request->password);

        $admin = Admin::create($data);

        $token = JWTAuth::fromUser($admin);

        return response()->json([
            'message' => "Registered Successfully",
            'user' => AdminResource::collection(Admin::query()->where('id',$admin->id)->get()),
            'token' => $token,
        ]);
    }

    public function login(Request $request)
    {

        $data = $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);
        $admin = Admin::where('email', $request->email)->first();
        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'msg' => "Cardetalsd doesn't match",
            ],401);
        }
        $token = JWTAuth::fromUser($admin);
        return response()->json([
            'msg' => "Admin login Successfully",
            'admin' => AdminResource::collection(Admin::query()->where('id',$admin->id)->get()),
            'token' => $token
        ]);

    }
    public function logout(){
        auth()->logout();
        return response()->json([
            'msg'=>'Admin Sign out successfully'
        ],200);
    }
    function updateProfile(Request $request){
        if(count($request->all()) >0) {
            $user = auth()->user();
            $user->update($request->all());
            return response()->json([
                'msg' => "Admin's Profile Updated Successfully",
                'data' => AdminResource::collection(Admin::query()->where('id', $user->id)->get())
            ], 200);
        }
        return response()->json([
            'msg' => 'No Item to Update',
        ], 422);
    }

    public function getAllUser(){
        return response()->json([
           UserResource::collection(User::all())
        ],200);
    }

    public function editPassword()
    {
        $data = \request()->validate([
            'password' => 'required|confirmed',
        ]);
        $user = Admin::find(\auth()->user()->id);

        $requestData = \request()->all();
        if($user && \request()->password == \request()->password_confirmation){
            $requestData['password'] = bcrypt($requestData['password']);
        }
        else{
            unset($requestData['password']);
        }
        $user->update($requestData);
        return response()->json([
            'message' => 'Admin Updated Password Successfully',
            'data' => AdminResource::collection(User::query()->where('id',$user->id)->get())
        ], 200);
    }
}
