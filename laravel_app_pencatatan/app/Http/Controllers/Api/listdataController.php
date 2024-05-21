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
    public function index()
    {
        // Ambil data mobil berdasarkan status
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
                'status' => $data->status,
            ];
        }

        return response()->json(['data' => $listmobil], 200);
    }


    public function store(Request $request)
{
    // Validate incoming request data
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

    // Check for validation errors
    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => $validator->errors()->first(),
        ]);
    }

    // Store the uploaded photo in the 'public' storage
    $ext = $request->foto->getClientOriginalExtension();
    $nama_file = "foto-" . time() . "." . $ext;
    $path = $request->foto->storeAs('public', $nama_file);

    // Create a new entry in the database
    $listdata = new Listdata();
    $listdata->fill($request->all());
    $listdata->foto = $nama_file; // Store the full URL of the photo
    $listdata->status = 'available'; // Set the status to 'available' by default
    $listdata->save();

    // Check if data was saved successfully
    if ($listdata) {
        // Return a JSON response indicating successful data storage
        return response()->json([
            'status' => true,
            'message' => 'Data Mobil berhasil disimpan',
            'data' => $listdata,
        ]);
    } else {
        // Return a JSON response indicating unsuccessful data storage
        return response()->json([
            'status' => false,
            'message' => 'Gagal menyimpan data Mobil',
        ]);
    }
}

    public function show($status)
    {
        // Validasi status
        if (!in_array($status, ['sold', 'available'])) {
            // Jika status tidak valid, kembalikan pesan error
            return response()->json(['error' => 'Invalid status'], 400);
        }

        // Ambil data mobil berdasarkan status
        $listdata = Listdata::where('status', $status)->get();

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
                'status' => $data->status,
            ];
        }

        return response()->json([
            'status' => true,
            'data' => $listmobil
            ], 200);

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
            'data' => null, // Tidak perlu mengirim data kembali setelah dihapus
        ]);
    }


    public function updateStatus($id)
    {
    // Validasi request jika diperlukan

    $listdata = Listdata::find($id);
    if ($listdata) {
        $listdata->status = 'sold'; // Atur status menjadi "sold"
        $listdata->save();

        return response()->json([
            'status' => true,
            'message' => 'Status mobil berhasil diubah menjadi sold',
            'data' => $listdata // (Opsional) Kirim kembali data mobil yang telah diperbarui
        ]);
    } else {
        return response()->json([
            'status' => false,
            'message' => 'Mobil tidak ditemukan',
        ]);
    }
    }



}
