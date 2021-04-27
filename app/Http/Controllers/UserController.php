<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use JWTAuth;
use Validator;
use App\Http\Resources\UserResource;


class UserController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'phone' => 'required|unique:users',
        ]);

        $data['password'] = bcrypt($request->password);

        $user = User::create($data);
        $token = JWTAuth::fromUser($user);
        return response()->json([
            'msg' => 'Registered Successfully',
            'user' => UserResource::collection(User::query()->where('id', $user->id)->get()),
            'token' => $token,
        ], 200);

    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'error' => 'Invalid email or password',
            ], 400);
        }

        return response()->json([
            'msg' => 'Login Successfully',
            'user' => UserResource::collection(User::query()->where('id', \auth()->user()->id)->get()),
            'token' => $token,
        ], 200);
    }

    public function logout(){
        auth()->logout();

        return response()->json(['message' => 'User successfully signed out']);
    }

    function updateProfile(Request $request){
        if(count($request->all()) >0) {
            $user = auth()->user();
            $user->update($request->all());
            return response()->json([
                'msg' => 'Profile Updated Successfully',
                'data' => UserResource::collection(User::query()->where('id', $user->id)->get())
            ], 200);
        }
        return response()->json([
            'msg' => 'No Item to Update',
        ], 422);
    }

    public function editPassword()
    {
        $data = \request()->validate([
            'password' => 'required|confirmed',
        ]);
        $user = User::find(\auth()->user()->id);
        if ($user->password == \request()->old){
            dd("1");
        }
        $requestData = \request()->all();
        if(\request()->password == \request()->password_confirmation){
            $requestData['password'] = bcrypt($requestData['password']);
        }
        else{
            unset($requestData['password']);
        }
        $user->update($requestData);
        return response()->json([
            'message' => 'User Updated Password Successfully',
            'data' => UserResource::collection(User::query()->where('id',$user->id)->get())
        ], 200);
    }

}
