<?php

namespace App\Http\Controllers;
use function PHPUnit\Framework\isEmpty;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $list = User::all();
        if(count($list) > 0) {
            return response()->json(
                [
                    'kode' => 1,
                    'pesan' => "Data Tersedia",
                    'data' => $list,
                ]
            );
        }else{
            return response()->json(
                [
                    'kode' => 0,
                    'pesan' => "Data Tidak Tersedia",
                ]
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'username' => 'string|required',
        'password' => 'required|min:8',
    ]);

    // Enkripsi kata sandi sebelum menyimpannya
    $validated['password'] = bcrypt($validated['password']);

    $insert = User::create($validated);
    if($insert) {
        return response()->json(
            [
                'kode' => 1,
                'pesan' => "Sukses Menyimpan Data",
                'token' => $insert->createToken('password')->plainTextToken,
            ]
        );
    } else {
        return response()->json(
            [
                'kode' => 0,
                'pesan' => "Gagal Menyimpan Data",
            ]
        );
    }
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $find = User::find($id);
        if($find) {
            return response()->json(
                [
                    'kode' => 1,
                    'pesan' => "Sukses",
                    'data' => $find,
                ]
            );
        }else{
            return response()->json(
                [
                    'kode' => 0,
                    'pesan' => "Data Tidak Ditemukan",
                ]
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $update = User::find($id)
                    ->update($request->all());
        if($update) {
            return response()->json(
                [
                    'kode' => 1,
                    'pesan' => "Sukses Mengupdate Data",
                ]
            );
        }else{
            return response()->json(
                [
                    'kode' => 0,
                    'pesan' => "Gagal Mengupdate Data",
                ]
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $delete = User::findOrFail($id);
        if($delete) {
            $delete->delete();
            return response()->json(
                [
                    'kode' => 1,
                    'pesan' => "Sukses Menghapus Data",
                ]
            );
        }else{
            return response()->json(
                [
                    'kode' => 0,
                    'pesan' => "Gagal Menghapus Data",
                ]
            );
        }
    }
}
