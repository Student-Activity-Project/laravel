<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Listdata;
use App\Models\Warna;
use App\Models\Jenis;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ListdataController extends Controller
{
    public function index() //GET
    {
        $listdata = Listdata::all();
        $listmobil = [];

        // Iterate through each data and update the photo URL
        foreach ($listdata as $data) {
            $fotoUrl = asset('storage/' . $data->foto); // Assuming 'foto' is the field storing photo paths
            $transmisi = $data->transmisi === 'manual' ? 'Manual' : 'Matic';
            $warna = Warna::find($data->id_warna_mobil)->nama;
            $merk = Jenis::find($data->id_jenis_mobil)->nama;
            $listmobil[] = [
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

        return response()->json([
            'status' => true,
            'data' => $listmobil
        ]);
    }

    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'nama_mobil' => 'required',
        'transmisi' => 'required',
        'id_jenis_mobil' => 'required',
        'tanggal_beli' => 'required|date|date_format:Y-m-d',
        'tahun_mobil' => 'required',
        'id_warna_mobil' => 'required',
        'nomor_polisi' => 'required',
        'harga_jual' => 'required|numeric|min:0',
        'catatan_perbaikan' => 'required',
        'foto' => 'nullable|file|image|max:5000',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => $validator->errors()->first(),
        ]);
    }

    // Simpan file foto ke penyimpanan 'public'
    $ext = $request->foto->getClientOriginalExtension();
    $nama_file = "foto-" . time() . "." . $ext;
    $path = $request->foto->storeAs('public', $nama_file);


    // Buat entri baru dalam database
    $listdata = new Listdata();
    $listdata->fill($request->all());
    $listdata->foto = $nama_file; // Gunakan URL lengkap foto
    $listdata->save();

    return response()->json([
        'status' => true,
        'message' => 'Data Mobil berhasil disimpan',
        'data' => $listdata,
    ]);
}

    public function show($id)
    {
        try {
            $listdata = Listdata::findOrFail($id);
            return response()->json([
                'status' => true,
                'message' => 'Data Mobil ditemukan',
                'data' => $listdata,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data Mobil tidak ditemukan',
            ]);
        }
    }

    public function update(Request $request, $id)
{
    $listdata = Listdata::findOrFail($id);

    $validator = Validator::make($request->all(), [
        'nama_mobil' => 'required',
        'transmisi' => 'required',
        'id_jenis_mobil' => 'required',
        'tanggal_beli' => 'required|date|date_format:Y-m-d',
        'tahun_mobil' => 'required',
        'id_warna_mobil' => 'required',
        'nomor_polisi' => 'required',
        'harga_jual' => 'required|numeric|min:0',
        'catatan_perbaikan' => 'required',
        // 'foto' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => $validator->errors()->first(),
        ]);
    }

    // Periksa apakah ada file foto yang dikirimkan
    // if ($request->hasFile('foto')) {
    //     // Hapus foto lama jika ada
    //     if ($listdata->foto) {
    //         Storage::delete('public/' . $listdata->foto);
    //     }

    //     // Simpan foto baru
    //     $ext = $request->foto->getClientOriginalExtension();
    //     $nama_file = "foto-" . time() . "." . $ext;
    //     $path = $request->foto->storeAs("public", $nama_file);
    //     $listdata->foto = $nama_file;
    // }

    // Isi model Listdata dengan data yang diperbarui
    $listdata->fill($request->all());
    $listdata->save();

    return response()->json([
        'status' => true,
        'message' => 'Data Mobil berhasil diupdate',
        'data' => $listdata,
    ]);
}

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


    public function markAsSold($id)
    {
        // Cari data mobil berdasarkan ID
        $mobil = Listdata::find($id);

        // Periksa apakah data mobil ditemukan
        if (!$mobil) {
            return response()->json([
                'status' => false,
                'message' => 'Data Mobil tidak ditemukan',
            ]);
        }

        // Buat objek Transaksi baru untuk mobil yang dijual
        $transaksi = new Transaksi();
        $transaksi->nama_mobil = $mobil->nama_mobil;
        $transaksi->transmisi = $mobil->transmisi;
        $transaksi->id_jenis_mobil = $mobil->id_jenis_mobil;
        $transaksi->tanggal_beli = $mobil->tanggal_beli;
        $transaksi->tahun_mobil = $mobil->tahun_mobil;
        $transaksi->id_warna_mobil = $mobil->id_warna_mobil;
        $transaksi->nomor_polisi = $mobil->nomor_polisi;
        $transaksi->harga_jual = $mobil->harga_jual;
        $transaksi->catatan_perbaikan = $mobil->catatan_perbaikan;
        $transaksi->foto = $mobil->foto;
        $transaksi->status = 'sold';

        // Simpan objek Transaksi ke dalam database
        $transaksi->save();

        // Hapus data mobil dari list stok
        $mobil->delete();

        // Beri respons berhasil
        if($transaksi){
            return response()->json([
                'status' => true,
                'message' => 'Mobil berhasil dijual dan dipindahkan ke transaksi',
                'data' => $transaksi,
            ]);
        }else{
            return response()->json([
            'status' => false,
            'message' => 'Mobil Tidak Berhasil disimpan',
            ]);
        }
    }



}
