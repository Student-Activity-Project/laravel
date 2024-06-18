<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class AuthenticationController extends Controller
{
    use HasApiTokens, Notifiable, SoftDeletes;
    public function register(Request $request)
    {
        $rules = [
            'username' => ['required', 'max:50', 'regex:/^[a-zA-Z0-9_]*$/', 'min:4'],
            'password' => ['required', 'max:50', 'regex:/^[a-zA-Z0-9_]*$/', 'min:4'],
        ];

        // Validate the incoming request data
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        try {
            $user = User::create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'status' => true,
                'user' => $user,
                'message' => 'Register Sukses!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Register Gagal! ' . $e->getMessage(),
            ]);
        }
    }

    public function login(Request $request)
    {
        $rules = [
            'username' => ['required', 'max:50', 'regex:/^[a-zA-Z0-9_]*$/', 'min:4'],
            'password' => ['required', 'max:50', 'regex:/^[a-zA-Z0-9_]*$/', 'min:4'],
        ];

        // Validate the incoming request data
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        try {
            $user = User::where("username", $request->username)->first();
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => false,
                    'token' => null,
                    'message' => 'User Tidak Ditemukan/Password Salah!',
                ]);
            }

            $token = $user->createToken($request->password)->plainTextToken;

            return response()->json([
                'status' => true,
                'token' => $token,
                'username' => $user->username,
                'user_id' => $user->id,
                'message' => 'Login Sukses!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Login Gagal! ' . $e->getMessage(),
            ]);
        }
    }

    public function updateUser(Request $request, string $id)
    {
        $rules = [
            'username' => ['string', 'max:50', 'regex:/^[a-zA-Z0-9_]*$/', 'min:4'],
            'password' => ['string', 'max:50', 'regex:/^[a-zA-Z0-9_]*$/', 'min:4'],
        ];

        // Validate the incoming request data
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        // Get the user by ID
        $user = User::find($id);

        // Check if user exists
        if (!$user) {
            return response()->json([
                'kode' => 0,
                'pesan' => "User tidak ditemukan",
            ]);
        }

        // Prepare data for update
        $data = [];

        // Check if username is present in the request and update it
        if ($request->has('username')) {
            // Check if the new username is already taken by another user
            if ($request->username !== $user->username) {
                $existingUser = User::where('username', $request->username)->first();
                if ($existingUser) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Username Sudah Ada!',
                    ]);
                }
            }
            $data['username'] = $request->username;
        }

        // If password is present, hash it and update
        if ($request->has('password')) {
            $data['password'] = Hash::make($request->password);
        }

        try {
            // Attempt to update the user
            $user->update($data);

            return response()->json([
                'status' => true,
                'message' => "User Berhasil Diupdate!",
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Gagal Mengupdate User! " . $e->getMessage(),
            ]);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status' => true,
                'message' => 'Logout Sukses!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Logout Gagal! ' . $e->getMessage(),
            ]);
        }
    }

    public function deleteUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User tidak ditemukan!',
            ]);
        }

        try {
            $user->delete();

            return response()->json([
                'status' => true,
                'message' => 'User berhasil dihapus!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus user! ' . $e->getMessage(),
            ]);
        }
    }

    public function restoreUser($id)
    {
        $user = User::onlyTrashed()->find($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User tidak ditemukan!',
            ]);
        }

        try {
            $user->restore();

            return response()->json([
                'status' => true,
                'message' => 'User berhasil dikembalikan!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengembalikan user! ' . $e->getMessage(),
            ]);
        }
    }

}
