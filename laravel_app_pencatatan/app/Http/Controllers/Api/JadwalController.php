<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $jadwal = Jadwal::all();
        return response()->json(
            ['status' => true, 'data' => $jadwal]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'nama_mk'=> 'required|min:5|max:20',
            'nama_dosen'=> 'required|min:5|max:20',
            'waktu'=> 'required|min:5|max:20',
            'jam'=> 'required|min:5|max:20',
            'jumlah_pertemuan'=> 'required',
        ]);

        $jadwal = new Jadwal();
        $jadwal->nama_mk = $validateData['nama_mk'];
        $jadwal->nama_dosen = $validateData['nama_dosen'];
        $jadwal->waktu = $validateData['waktu'];
        $jadwal->jam = $validateData['jam'];
        $jadwal->jumlah_pertemuan = $validateData['jumlah_pertemuan'];
        $jadwal->save();

        return response()->json([
            'status' => true,
            'message' => 'Data berhasil disimpan',
            'data' => $jadwal,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $jadwal = Jadwal::findOrFail($id);
        if($jadwal){
            return response()->json([
                'status' => true,
                'message' => 'Data Jadwal ditemukan',
                'data' => $jadwal,
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Data Jadwal tidak ditemukan',
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $validateData = $request->validate([
            'nama_mk'=> 'required|min:5|max:20',
            'nama_dosen'=> 'required|min:5|max:20',
            'waktu'=> 'required|min:5|max:20',
            'jam'=> 'required|min:5|max:20',
            'jumlah_pertemuan'=> 'required',
        ]);

        Jadwal::where('id', $jadwal->id)
        ->update([
            'nama_mk' => $validateData['nama_mk'],
            'nama_dosen' => $validateData['nama_dosen'],
            'waktu' => $validateData['waktu'],
            'jam' => $validateData['jam'],
            'jumlah_pertemuan' => $validateData['jumlah_pertemuan']
        ]);


        return response()->json([
            'status' => true,
            'message' => 'Data berhasil diupdate',
            'data' => $jadwal,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $jadwal->delete();

        return response()->json([
            'status' => true,
            'message' => 'Data berhasil dihapus',
            'data' => $jadwal,
        ]);
    }
}
