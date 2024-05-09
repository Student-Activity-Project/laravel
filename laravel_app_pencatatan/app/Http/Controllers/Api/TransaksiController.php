<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Warna;
use App\Models\Jenis;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Mengambil semua data transaksi dari database
        $transaksis = Transaksi::all();
        $listTransaksi = [];
        
        // Iterate through each data and update the photo URL
        foreach ($transaksis as $data) {
            $fotoUrl = asset('storage/' . $data->foto); // Assuming 'foto' is the field storing photo paths
            $transmisi = $data->transmisi === 'manual' ? 'Manual' : 'Matic';
            $warna = Warna::find($data->id_warna_mobil)->nama;
            $merk = Jenis::find($data->id_jenis_mobil)->nama;
            $listTransaksi[] = [
                'id' => $data->id,
                'nama_mobil' => $data->nama_mobil,
                'transmisi' => $transmisi,
                'id_jenis_mobil' => $merk,
                'tanggal_beli' => $data->tanggal_beli,
                'tahun_mobil' => $data->tahun_mobil,
                'id_warna_mobil' => $warna,
                'nomor_polisi' => $data->nomor_polisi,
                'harga_jual' => $data->harga_jual,
                'catatan_perbaikan' => $data->catatan_perbaikan,
                'foto' => $fotoUrl,
            ];
        }

        // Mengembalikan data transaksi dalam bentuk JSON
        return response()->json([
            'status' => true,
            'message' => 'Data transaksi berhasil diambil',
            'data' => $listTransaksi,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {

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

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {

    }
}
