<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Absen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AbsenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $absen = Absen::all();
        return response()->json(
            ['status' => true, 'data' => $absen]
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
            'foto' => 'nullable|file|image|max:5000',
            'nama_mk'=> 'required|min:5|max:20',
            'nama_dosen'=> 'required|min:5|max:20',
            'waktu'=> 'required|min:5|max:20',
            'jam'=> 'required|min:5|max:20',
            'jumlah_pertemuan'=> 'required',
        ]);

        //handle file upload
        $ext = $request->foto->getClientOriginalExtension();
        $nama_file = "foto-". time() . "." . $ext;
        $path = $request->foto->storeAs("public", $nama_file);

        $absen = new Absen();
        $absen->foto = $nama_file;
        $absen->nama_mk = $validateData['nama_mk'];
        $absen->nama_dosen = $validateData['nama_dosen'];
        $absen->waktu = $validateData['waktu'];
        $absen->jam = $validateData['jam'];
        $absen->jumlah_pertemuan = $validateData['jumlah_pertemuan'];
        $absen->save();

        return response()->json([
            'status' => true,
            'message' => 'Data berhasil disimpan',
            'data' => $absen,
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
        $absen = Absen::findOrFail($id);
        if($absen){
            return response()->json([
                'status' => true,
                'message' => 'Data Jadwal ditemukan',
                'data' => $absen,
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
        $absen = Absen::findOrFail($id);
        $validateData = $request->validate([
            'foto' => 'nullable|file|image|max:5000',
            'nama_mk'=> 'required|min:5|max:20',
            'nama_dosen'=> 'required|min:5|max:20',
            'waktu'=> 'required|min:5|max:20',
            'jam'=> 'required|min:5|max:20',
            'jumlah_pertemuan'=> 'required',
        ]);

        $nama_file = $absen->foto;
        if($request->foto){
            // get extension image
            $ext = $request->foto->getClientOriginalExtension();
            $nama_file = "foto-". time() .".". $ext; //foto img1 di /storage/public
            $path = $request->foto->storeAs("public", $nama_file);

            //Hapus file lama
            Storage::delete('public/' .$absen->foto);
        }

        Absen::where('id', $absen->id)
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
            'data' => $absen,
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
        $absen = Absen::findOrFail($id);
        $absen->delete();

        return response()->json([
            'status' => true,
            'message' => 'Data berhasil dihapus',
            'data' => $absen,
        ]);
    }
}
