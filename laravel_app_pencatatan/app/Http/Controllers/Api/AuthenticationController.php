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
                'message' => 'User Tidak Ditemukan/Password Salah!',
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
            'user_id' => $user->id,
            'message' => 'Login Sukses!',
            ]
        );
    }

    public function updateUser(Request $request, string $id)
{

    // Validate the incoming request data
    $validate = $request->validate([
        'username' => ['required', 'string', 'max:50'],
        'password' => ['required', 'string', 'min:5'], // adjust rules as needed
    ]);

    // Get the user by ID
    $user = User::find($id);

    // Check if user exists
    if (!$user) {
        return response()->json([
            'kode' => 0,
            'pesan' => "User not found",
        ]);
    }

    // Prepare data for update
    $data = [];

    // Check if username is present in the request and update it
    if ($request->has('username')) {
        $data['username'] = $request->username;
    }

    // If password is present, hash it and update
    if ($request->has('password')) {
        $data['password'] = Hash::make($request->password);
    }

    // Attempt to update the user
    $updated = User::where('id', $id)->first();
    $updated->username = $validate['username'];
    $updated->password = Hash::make($request->password);
    $updated -> save();

    // Check if update was successful
    if ($updated) {
        return response()->json([
            'kode' => 1,
            'pesan' => "Sukses Mengupdate Data",
            'data' => $updated // return updated user data
        ]);
    } else {
        return response()->json([
            'kode' => 0,
            'pesan' => "Gagal Mengupdate Data",
        ]);
    }
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
