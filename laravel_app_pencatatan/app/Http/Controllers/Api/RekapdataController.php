<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\RekapData;
use App\Models\Stokmobil;
use App\Models\Warna;
use App\Models\Jenis;


class RekapdataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function dataListByDateRange(Request $request)
    {
        $userId = Auth::id();
        // Validasi permintaan
        $request->validate([
            'tanggal_awal' => 'required|date|date_format:Y-m-d',
            'tanggal_akhir' => 'required|date|date_format:Y-m-d',
        ]);

        // Ambil tanggal awal dan tanggal akhir dari permintaan
        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');

        // Query untuk mendapatkan daftar data berdasarkan jangkauan tanggal
        $transaksis = Stokmobil::where('user_id', $userId)
        ->whereBetween('tanggal_beli', [$tanggalAwal, $tanggalAkhir])
        ->get();

        $listTransaksi = [];

        // Loop melalui setiap transaksi untuk memformat data dan mengambil URL foto
        foreach ($transaksis as $data) {
            $fotoUrl = asset('storage/' . $data->foto); // Mendapatkan URL foto dari field 'foto'
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
                'foto' => $fotoUrl, // Menambahkan URL foto ke respons
            ];
        }

        return response()->json([
            'status' => true,
            'data_tanggal' => $listTransaksi, // Mengirim data transaksi dengan URL foto
        ]);
    }

    public function dataListByMerk(Request $request)
    {
        $userId = Auth::id();
        // Validasi permintaan
        $request->validate([
            'merk' => 'required|string', // Pastikan 'merk' adalah string yang diperlukan
            'tanggal_awal' => 'required|date|date_format:Y-m-d',
            'tanggal_akhir' => 'required|date|date_format:Y-m-d',
        ]);

        // Ambil tanggal awal dan tanggal akhir dari permintaan
        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');

        // Ambil nilai 'merk' dari permintaan
        $merk = $request->input('merk');

        // Query untuk mendapatkan daftar data berdasarkan merek dan jangkauan tanggal
        $transaksis = Stokmobil::whereHas('jenis', function ($query) use ($merk) {
            $query->where('nama', $merk);
        })
        ->where('user_id', $userId) // Menambahkan kondisi untuk membatasi data berdasarkan pengguna yang terotentikasi
        ->whereBetween('tanggal_beli', [$tanggalAwal, $tanggalAkhir])
        ->get();

        $listTransaksi = [];

        // Loop melalui setiap transaksi untuk memformat data dan mengambil URL foto
        foreach ($transaksis as $data) {
            $fotoUrl = asset('storage/' . $data->foto); // Mendapatkan URL foto dari field 'foto'
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
                'foto' => $fotoUrl, // Menambahkan URL foto ke respons
            ];
        }

        if($listTransaksi){
            // Ambil semua unit mobil dari daftar transaksi
            $unitMobil = array_column($listTransaksi, 'nama_mobil');

            // Hapus duplikat dari daftar unit mobil
            $unitMobil = array_unique($unitMobil);

            // Hitung total unit mobil
            $totalUnitMobil = count($unitMobil);

            return response()->json([
                'status' => true,
                'data_merk' => $listTransaksi,
                'total_unit' => $totalUnitMobil,
                'message' => 'Data Transaksi ditemukan',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Belum Ada Transaksi',
            ]);
        }
    }

    public function dataListByTransmisi(Request $request)
    {
        $userId = Auth::id();
        // Validasi permintaan
        $request->validate([
            'transmisi' => 'required|string', // Pastikan 'transmisi' adalah string yang diperlukan
            'tanggal_awal' => 'required|date|date_format:Y-m-d',
            'tanggal_akhir' => 'required|date|date_format:Y-m-d',
        ]);

        // Ambil tanggal awal dan tanggal akhir dari permintaan
        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');

        // Ambil nilai 'transmisi' dari permintaan
        $transmisi = $request->input('transmisi');

        // Query untuk mendapatkan daftar data berdasarkan transmisi dan jangkauan tanggal
        $transaksis = Stokmobil::where('transmisi', $transmisi)
        ->whereBetween('tanggal_beli', [$tanggalAwal, $tanggalAkhir])
        ->where('user_id', $userId) // Menambahkan kondisi untuk membatasi data berdasarkan pengguna yang terotentikasi
        ->get();

        $listTransaksi = [];

        // Loop melalui setiap transaksi untuk memformat data dan mengambil URL foto
        foreach ($transaksis as $data) {
            $fotoUrl = asset('storage/' . $data->foto); // Mendapatkan URL foto dari field 'foto'
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
                'foto' => $fotoUrl, // Menambahkan URL foto ke respons
            ];
        }

        if($listTransaksi){
            // Ambil semua unit mobil dari daftar transaksi
            $unitMobil = array_column($listTransaksi, 'nama_mobil');

            // Hapus duplikat dari daftar unit mobil
            $unitMobil = array_unique($unitMobil);

            // Hitung total unit mobil
            $totalUnitMobil = count($unitMobil);

            return response()->json([
                'status' => true,
                'data_transmisi' => $listTransaksi,
                'total_unit' => $totalUnitMobil,
                'message' => 'Data Transaksi ditemukan',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Belum Ada Transaksi',
            ]);
        }
    }

    public function dataListByTahun(Request $request)
    {
        $userId = Auth::id();
        // Validasi permintaan
        $request->validate([
            'tahun' => 'required', // Pastikan 'tahun' adalah integer yang diperlukan
            'tanggal_awal' => 'required|date|date_format:Y-m-d',
            'tanggal_akhir' => 'required|date|date_format:Y-m-d',
        ]);

        // Ambil tanggal awal dan tanggal akhir dari permintaan
        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');

        // Ambil nilai 'tahun' dari permintaan
        $tahun = $request->input('tahun');

        // Query untuk mendapatkan daftar data berdasarkan tahun mobil dan jangkauan tanggal
        $transaksis = Stokmobil::where('tahun_mobil', $tahun)
                                ->whereBetween('tanggal_beli', [$tanggalAwal, $tanggalAkhir])
                                ->where('user_id', $userId)
                                ->get();


        $listTransaksi = [];

        // Loop melalui setiap transaksi untuk memformat data dan mengambil URL foto
        foreach ($transaksis as $data) {
            $fotoUrl = asset('storage/' . $data->foto); // Mendapatkan URL foto dari field 'foto'
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
                'foto' => $fotoUrl, // Menambahkan URL foto ke respons
            ];
        }

        if($listTransaksi){
            // Ambil semua unit mobil dari daftar transaksi
            $unitMobil = array_column($listTransaksi, 'nama_mobil');

            // Hapus duplikat dari daftar unit mobil
            $unitMobil = array_unique($unitMobil);

            // Hitung total unit mobil
            $totalUnitMobil = count($unitMobil);

            return response()->json([
                'status' => true,
                'data_tahun' => $listTransaksi,
                'total_unit' => $totalUnitMobil,
                'message' => 'Data Transaksi ditemukan',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Belum Ada Transaksi',
            ]);
        }
    }

}
