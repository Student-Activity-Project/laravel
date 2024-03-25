<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Listdata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class listdataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $listdata = Listdata::all();
        return response()->json(
            ['status' => true, 'data' => $listdata]
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
            'nama_mobil'=> 'required|min:5|max:20',
            'transmisi'=> 'required|min:5|max:20',
            'tanggal_beli'=> 'required|min:5|max:20',
            'tahun_mobil'=> 'required|min:5|max:20',
            'warna_mobil'=> 'required|min:5|max:20',
            'nomor_polisi'=> 'required|min:5|max:20',
            'harga_jual'=> 'required|min:5|max:20',
            'catatan_perbaikan'=> 'required|min:5|max:20',
            'foto' => 'nullable|file|image|max:5000',
        ]);

        //handle file upload
        $ext = $request->foto->getClientOriginalExtension();
        $nama_file = "foto-". time() . "." . $ext;
        $path = $request->foto->storeAs("public", $nama_file);

        $listdata = new Listdata();
        $listdata->nama_mobil = $validateData['nama_mobil'];
        $listdata->transmisi = $validateData['transmisi'];
        $listdata->tanggal_beli = $validateData['tanggal_beli'];
        $listdata->tahun_mobil = $validateData['tahun_mobil'];
        $listdata->warna_mobil = $validateData['warna_mobil'];
        $listdata->nomor_polisi = $validateData['nomor_polisi'];
        $listdata->harga_jual = $validateData['harga_jual'];
        $listdata->catatan_perbaikan = $validateData['catatan_perbaikan'];
        $listdata->foto = $nama_file;
        $listdata->save();

        return response()->json([
            'status' => true,
            'message' => 'Data Mobil berhasil disimpan',
            'data' => $listdata,
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
        $listdata = Listdata::findOrFail($id);
        if($listdata){
            return response()->json([
                'status' => true,
                'message' => 'Data Mobil ditemukan',
                'data' => $listdata,
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Data Mobil tidak ditemukan',
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
        $listdata = Listdata::findOrFail($id);
        $validateData = $request->validate([
            'nama_mobil'=> 'required|min:5|max:20',
            'transmisi'=> 'required|min:5|max:20',
            'tanggal_beli'=> 'required|min:5|max:20',
            'tahun_mobil'=> 'required|min:5|max:20',
            'warna_mobil'=> 'required|min:5|max:20',
            'nomor_polisi'=> 'required|min:5|max:20',
            'harga_jual'=> 'required|min:5|max:20',
            'catatan_perbaikan'=> 'required|min:5|max:20',
            'foto' => 'nullable|file|image|max:5000',
        ]);

        $nama_file = $listdata->foto;
        if($request->foto){
            // get extensst->foto->getClientOriginalExtension();
            $ext = $request->foto->getClientOriginalExtension();
            $nama_file = "foto-". time() .".". $ext; //foto img1 di /storage/public
            $path = $request->foto->storeAs("public", $nama_file);

            //Hapus file lama
            Storage::delete('public/' .$listdata->foto);
        }

        Listdata::where('id', $listdata->id)
        ->update([
            'nama_mobil' => $validateData['nama_mobil'],
            'transmisi' => $validateData['transmisi'],
            'tanggal_beli' => $validateData['tanggal_beli'],
            'tahun_mobil' => $validateData['tahun_mobil'],
            'warna_mobil' => $validateData['warna_mobil'],
            'nomor_polisi' => $validateData['nomor_polisi'],
            'harga_jual' => $validateData['harga_jual'],
            'catatan_perbaikan' => $validateData['catatan_perbaikan'],
            'foto' => $nama_file,
        ]);


        return response()->json([
            'status' => true,
            'message' => 'Data Mobil berhasil diupdate',
            'data' => $listdata,
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
        $listdata = Listdata::findOrFail($id);
        $listdata->delete();

        return response()->json([
            'status' => true,
            'message' => 'Data Mobil berhasil dihapus',
            'data' => $listdata,
        ]);
    }
}
