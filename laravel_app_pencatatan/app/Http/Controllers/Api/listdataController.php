<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Listdata;
use App\Models\Warna;
use App\Models\Jenis;
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
            'foto' => 'nullable|file|image|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $nama_file = $listdata->foto;
        if ($request->hasFile('foto')) {
            $ext = $request->foto->getClientOriginalExtension();
            $nama_file = "foto-" . time() . "." . $ext;
            $path = $request->foto->storeAs("public", $nama_file);
            Storage::delete('public/' . $listdata->foto);
        }

        $listdata->fill($request->all());
        $listdata->foto = $nama_file;
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
}
