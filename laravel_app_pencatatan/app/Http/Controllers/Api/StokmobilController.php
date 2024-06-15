<?php

namespace App\Http\Controllers\api;


use App\Http\Controllers\Controller;
use App\Models\Stokmobil;
use App\Models\Warna;
use App\Models\User;
use App\Models\Jenis;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
class StokmobilController extends Controller
{
    public function index()
    {
        $listdata = Stokmobil::all();

        $listmobil = [];

        // Iterate through each data and update the photo URL
        foreach ($listdata as $data) {
            $fotoUrl = asset('storage/' . $data->foto); // Assuming 'foto' is the field storing photo paths
            $transmisi = $data->transmisi === 'manual' ? 'Manual' : 'Matic';
            $warna = Warna::find($data->id_warna_mobil)->nama;
            $merk = Jenis::find($data->id_jenis_mobil)->nama;

            $listmobil[] = [
                'user_id' => $data->user_id,
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

    $validator = Validator::make($request->all(), [
        'nama_mobil' => 'required|min:2|max:20',
        'transmisi' => 'required',
        'id_jenis_mobil' => 'required',
        'tanggal_beli' => 'required|date|date_format:Y-m-d',
        'tahun_mobil' => 'required|integer|min:1900|max:' . date('Y'),
        'id_warna_mobil' => 'required',
        'nomor_polisi' => 'required|max:10',
        'harga_jual' => 'required|numeric|min:0|max:100000000000',
        'catatan_perbaikan' => 'required|max:200',
        'foto' => 'file|image|max:5000',
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
    $listdata = new Stokmobil();
    $listdata->fill($request->all());
    $listdata->foto = $nama_file; // Store the full URL of the photo
    $listdata->user_id = Auth::id();
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
        $listdata = Stokmobil::where('status', $status)->get();

        $listmobil = [];

        // Iterate through each data and update the photo URL
        foreach ($listdata as $data) {
            $fotoUrl = asset('storage/' . $data->foto); // Assuming 'foto' is the field storing photo paths
            $transmisi = $data->transmisi === 'manual' ? 'Manual' : 'Matic';
            $warna = Warna::find($data->id_warna_mobil)->nama;
            $merk = Jenis::find($data->id_jenis_mobil)->nama;

            $listmobil[] = [
                'user_id' => $data->user_id,
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
    $listdata = Stokmobil::findOrFail($id);

    $validator = Validator::make($request->all(), [
        'nama_mobil' => 'required|min:2|max:20',
        'transmisi' => 'required',
        'id_jenis_mobil' => 'required',
        'tanggal_beli' => 'required|date|date_format:Y-m-d',
        'tahun_mobil' => 'required|integer|min:1900|max:' . date('Y'),
        'id_warna_mobil' => 'required',
        'nomor_polisi' => 'required|max:10',
        'harga_jual' => 'required|numeric|min:0|max:100000000000',
        'catatan_perbaikan' => 'required|max:200',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => $validator->errors()->first(),
        ]);
    }

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
        $listdata = Stokmobil::findOrFail($id);
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

        $listdata = Stokmobil::findOrFail($id);

        if ($listdata) {
            $listdata->status = 'sold'; // Atur status menjadi "sold"
            $listdata->save();

            return response()->json([
                'status' => true,
                'message' => 'Mobil Berhasil Dijual',
                'data' => $listdata // (Opsional) Kirim kembali data mobil yang telah diperbarui
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Mobil Gagal Dijual',
            ]);
        }
    }

    public function updateFoto(Request $request, $id) {

        // Validasi permintaan untuk memastikan 'foto' ada
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5000',
        ]);

        // Temukan record listdata berdasarkan ID
        $listdata = Stokmobil::findOrFail($id);
        if (!Auth::check()) {
            // Kembalikan respon JSON yang gagal jika pengguna tidak terautentikasi
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized: Anda harus login untuk mengakses fitur ini'
            ], 401);
        }

        // Periksa apakah pengguna memiliki akses ke entitas yang akan diubah
        if ($listdata->user_id !== Auth::id()) {
            // Kembalikan respon JSON yang gagal jika pengguna tidak memiliki akses
            return response()->json([
                'status' => false,
                'message' => 'Forbidden: Anda tidak memiliki izin untuk mengakses atau mengubah data ini'
            ], 403);
        }

        if ($listdata) {

            // Dapatkan ekstensi file
            $ext = $request->file('foto')->getClientOriginalExtension();
            // Buat nama file baru
            $nama_file = "foto-" . time() . "." . $ext;
            // Simpan file di disk 'public'
            $path = $request->file('foto')->storeAs("public", $nama_file);

            // Perbarui field 'foto' di database
            $listdata->foto = $nama_file;
            $listdata->save();

            // Kembalikan respon JSON yang sukses
            return response()->json([
                'status' => true,
                'message' => 'Sukses Mengubah Data',
                'data' => $listdata
            ], 200);
        } else {

            // Kembalikan respon JSON yang gagal jika record tidak ditemukan
            return response()->json([
                'status' => false,
                'message' => 'Gagal Mengubah Data, Record Tidak Ditemukan'
            ], 404);
        }
    }



}
