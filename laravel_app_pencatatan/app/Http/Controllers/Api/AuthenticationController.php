<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required', Password::defaults()],
            'device_name' => 'required',
        ]);

        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        if(!$user || !Hash::check($request->password, $user->password)){
            return response()->json(
                [
                'status' => false,
                'token' => null,
                'message' => 'User tidak ditemukan!',
                ]
            );
        }

        $token = $user->createToken($request->device_name)->plainTextToken;
        return response()->json([
            'status' => true,
            'token' => $token,
            'message' => 'Register Sukses!',
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
            // 'device_name' => 'required', //untuk menentukan nama token
        ]);
        //proses cek user untuk login
        $user = User::where("username", $request->username)->first();
        if(!$user || !Hash::check($request->password, $user->password)){
            return response()->json(
                [
                'status' => false,
                'token' => null,
                'message' => 'User tidak ditemukan!',
                ]
            );
        }

        //generate user acces token
        $token = $user->createToken($request->password)->plainTextToken; //all ability
        // $token = $user->createToken($request->device_name, ['prodi:create', 'prodi:delete'])->plainTextToken; //khusus create n delete
        return response()->json(
            [
            'status' => true,
            'token' => $token,
            'message' => 'Login Sukses!',
            ]
        );
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logout Sukses!',
        ]);
    }
}
