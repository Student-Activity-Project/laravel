<?php

namespace App\Http\Controllers\api;


use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Stokmobil;
use App\Models\Warna;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

            // Mengambil nama pengguna yang menjual
            $userJual = $data->user_jual_id ? User::find($data->user_jual_id)->name : null;

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
                'tanggal_jual' => $data->tanggal_jual, // Tambahkan tanggal_jual ke dalam array
                'user_jual_id' => $data->user_jual_id, // Tambahkan user_jual_id ke dalam array
                'user_jual' => $data->user_jual,
            ];
        }

        return response()->json(['data' => $listmobil, 'message' => 'Mobil Berhasil Di-Load'], 200);
    }


    public function store(Request $request)
{

    $validator = Validator::make($request->all(), [
        'nama_mobil' => 'required|min:2|max:25|regex:/^[a-zA-Z0-9][a-zA-Z0-9 . ]*$/',
        'transmisi' => 'required',
        'id_jenis_mobil' => 'required',
        'tanggal_beli' => 'required|date|date_format:Y-m-d',
        'tahun_mobil' => 'required|integer|min:1900|max:' . date('Y') . '|regex:/^\d{4}$/',
        'id_warna_mobil' => 'required',
        'nomor_polisi' => 'required|max:10|regex:/^[A-Z]{1,2} \d{1,4} [A-Z]{1,3}$/',
        'harga_jual' => 'required|numeric|min:0|max:100000000000|regex:/^\d+$/',
        'catatan_perbaikan' => 'required|max:200|regex:/^[a-zA-Z0-9\s]*$/',
        'foto' => 'required|file|image|max:5000',
        'tanggal_jual' => 'nullable|date|date_format:Y-m-d',
    ]);

    // Check for validation errors
    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => $validator->errors()->first(),
        ]);
    }
    // Cek apakah mobil dengan atribut yang sama sudah ada dalam database
    $existingCar = Stokmobil::where('nama_mobil', $request->nama_mobil)
                             ->where('transmisi', $request->transmisi)
                             ->where('id_jenis_mobil', $request->id_jenis_mobil)
                            //  ->where('tanggal_beli', $request->tanggal_beli)
                             ->where('tahun_mobil', $request->tahun_mobil)
                             ->where('id_warna_mobil', $request->id_warna_mobil)
                             ->where('nomor_polisi', $request->nomor_polisi)
                            //  ->where('harga_jual', $request->harga_jual)
                            //  ->where('catatan_perbaikan', $request->catatan_perbaikan)
                             ->exists();

    // Jika mobil dengan atribut yang sama sudah ada, kembalikan respons JSON dengan pesan error
    if ($existingCar) {
        return response()->json([
            'status' => false,
            'message' => 'Data Mobil sudah ada dalam database',
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
    $user = auth()->user();
    $userJual = User::find($user->id);

    $listdata->status = 'available'; // Set the status to 'available' by default
    if ($request->status === 'sold') {
        $listdata->status = 'sold';
        $listdata->tanggal_jual = $request->tanggal_jual ?? Carbon::now()->format('Y-m-d');
        $listdata->user_jual_id = Auth::id(); // Set user_jual_id to current user
        $listdata->user_jual;
    }
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

            $user = auth()->user();
            $userJual = User::find($user->id);

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
                'tanggal_jual' => $data->tanggal_jual, // Tambahkan tanggal_jual ke dalam array
                'user_jual_id' => $data->user_jual_id, // Tambahkan user_jual_id ke dalam array
                'user_jual' => $data->user_jual,
            ];
        }

        return response()->json([
            'status' => true,
            'data' => $listmobil
            ], 200);

    }

    public function update(Request $request, $id)
    {

        try {
            $listdata = Stokmobil::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data Mobil tidak ditemukan',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'nama_mobil' => 'required|min:2|max:25|regex:/^[a-zA-Z0-9][a-zA-Z0-9 .]*$/',
            'transmisi' => 'required',
            'id_jenis_mobil' => 'required',
            'tanggal_beli' => 'required|date|date_format:Y-m-d',
            'tahun_mobil' => 'required|integer|min:1900|max:' . date('Y') . '|regex:/^\d{4}$/',
            'id_warna_mobil' => 'required',
            'nomor_polisi' => 'required|max:10|regex:/^[A-Z]{1,2} \d{1,4} [A-Z]{1,3}$/',
            'harga_jual' => 'required|numeric|min:0|max:100000000000|regex:/^\d+$/',
            'catatan_perbaikan' => 'required|max:200|regex:/^[a-zA-Z0-9\s]*$/',
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
        try {
            $listdata = Stokmobil::findOrFail($id);
            $listdata->delete();

            return response()->json([
                'status' => true,
                'message' => 'Data Mobil berhasil dihapus',
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data Mobil tidak ditemukan',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menghapus data mobil',
            ], 500);
        }
    }


    public function updateStatus($id)
    {
        try {
            $listdata = Stokmobil::findOrFail($id);
            $user = auth()->user();
            $userJual = User::find($user->id);
            Log::info('Authenticated user: ', ['id' => $user->id, 'username' => $user->username]);

            $listdata->status = 'sold'; // Set status to "sold"
            $listdata->tanggal_jual = Carbon::now()->format('Y-m-d');; // Set tanggal_jual to current date and time
            $listdata->user_jual_id = auth()->user()->id; // Set user_jual_id to the current user's ID
            $listdata->user_jual = $userJual->username;
            $listdata->save();


            return response()->json([
                'status' => true,
                'message' => 'Mobil Berhasil Dijual',
                'data' => $listdata,  // Optional: Return the updated car data
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Mobil tidak ditemukan',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menjual mobil',
            ]);
        }
    }

    public function updateFoto(Request $request, $id) {

        $validator = Validator::make($request->all(), [
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }


        // Temukan record listdata berdasarkan ID
        $listdata = Stokmobil::findOrFail($id);
        if (!Auth::check()) {
            // Kembalikan respon JSON yang gagal jika pengguna tidak terautentikasi
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized: Anda harus login untuk mengakses fitur ini'
            ]);
        }

        // Periksa apakah pengguna memiliki akses ke entitas yang akan diubah
        if ($listdata->user_id !== Auth::id()) {
            // Kembalikan respon JSON yang gagal jika pengguna tidak memiliki akses
            return response()->json([
                'status' => false,
                'message' => 'Forbidden: Anda tidak memiliki izin untuk mengakses atau mengubah data ini'
            ]);
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
            ]);
        } else {

            // Kembalikan respon JSON yang gagal jika record tidak ditemukan
            return response()->json([
                'status' => false,
                'message' => 'Gagal Mengubah Data, Record Tidak Ditemukan'
            ]);
        }
    }



}
