<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\RekapData;
use App\Models\Stokmobil;
use Illuminate\Support\Facades\Validator;
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
        // Validasi permintaan
        $validator = Validator::make($request->all(), [
            'tanggal_awal' => 'required|date|date_format:Y-m-d',
            'tanggal_akhir' => 'required|date|date_format:Y-m-d|after_or_equal:tanggal_awal',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data_tanggal' => null,
            ]);
        }

        // Ambil tanggal awal dan tanggal akhir dari permintaan
        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');

        try {
            // Query untuk mendapatkan daftar data berdasarkan jangkauan tanggal_jual
            $transaksis = Stokmobil::whereBetween('tanggal_jual', [$tanggalAwal, $tanggalAkhir])->get();

            if ($transaksis->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'data_tanggal' => null,
                    'message' => 'Belum Ada Transaksi Di Tanggal Tersebut',
                ]);
            }

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
                    'tanggal_jual' => $data->tanggal_jual,
                    'user_jual_id' => $data->user_jual_id, // Tambahkan user_jual_id ke dalam array
                    'user_jual' => $data->user_jual,
                ];
            }

            return response()->json([
                'status' => true,
                'data_tanggal' => $listTransaksi,
                'message' => 'Data Transaksi Ditemukan',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error retrieving data: ' . $e->getMessage(),
                'data_tanggal' => null,
            ]);
        }

    }

    public function dataListByMerk(Request $request)
    {
        // Validasi permintaan
        $rules = [
            'merk' => 'required|string', // Pastikan 'merk' adalah string yang diperlukan
            'tanggal_awal' => 'required|date|date_format:Y-m-d',
            'tanggal_akhir' => 'required|date|date_format:Y-m-d|after_or_equal:tanggal_awal',
        ];
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'data_merk' => null,
                'total_unit' => 0,
                'message' => $validator->errors()->first(),
            ]);
        }

        // Ambil tanggal awal dan tanggal akhir dari permintaan
        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');

        // Ambil nilai 'merk' dari permintaan
        $merk = $request->input('merk');

        // Query untuk mendapatkan daftar data berdasarkan merek dan jangkauan tanggal_jual
        $transaksis = Stokmobil::whereHas('jenis', function ($query) use ($merk) {
            $query->where('nama', $merk);
        })
        ->whereBetween('tanggal_jual', [$tanggalAwal, $tanggalAkhir])
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
                'tanggal_jual' => $data->tanggal_jual,
                'user_jual_id' => $data->user_jual_id, // Tambahkan user_jual_id ke dalam array
                'user_jual' => $data->user_jual,
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
                'message' => 'Data Transaksi Ditemukan',
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
        // Aturan validasi
        $rules = [
            'transmisi' => 'required|string', // Pastikan 'transmisi' adalah string yang diperlukan
            'tanggal_awal' => 'required|date|date_format:Y-m-d',
            'tanggal_akhir' => 'required|date|date_format:Y-m-d|after_or_equal:tanggal_awal',
        ];

        // Validasi permintaan
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            // Jika validasi gagal, kembalikan respons dengan pesan kesalahan
            return response()->json([
                'status' => false,
                'data_transmisi' => null,
                'total_unit' => 0,
                'message' => $validator->errors()->first(),
            ]);
        }

        // Ambil tanggal awal dan tanggal akhir dari permintaan
        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');

        // Ambil nilai 'transmisi' dari permintaan
        $transmisi = $request->input('transmisi');

        // Query untuk mendapatkan daftar data berdasarkan transmisi dan jangkauan tanggal_jual
        $transaksis = Stokmobil::where('transmisi', $transmisi)
        ->whereBetween('tanggal_jual', [$tanggalAwal, $tanggalAkhir])
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
                'tanggal_jual' => $data->tanggal_jual,
                'user_jual_id' => $data->user_jual_id, // Tambahkan user_jual_id ke dalam array
                'user_jual' => $data->user_jual,
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
        // Aturan validasi
        $rules = [
            'tahun' => ['required', 'integer', 'min:1900', 'max:' . date('Y')],
            'tanggal_awal' => ['required', 'date', 'date_format:Y-m-d'],
            'tanggal_akhir' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:tanggal_awal'],
        ];

        // Validasi permintaan
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            // Jika validasi gagal, kembalikan respons dengan pesan kesalahan
            return response()->json([
                'status' => false,
                'data_tahun' => null,
                'total_unit' => 0,
                'message' => $validator->errors()->first(),
            ]);
        }

        // Ambil tanggal awal dan tanggal akhir dari permintaan
        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');

        // Ambil nilai 'tahun' dari permintaan
        $tahun = $request->input('tahun');

        // Query untuk mendapatkan daftar data berdasarkan tahun mobil dan jangkauan tanggal_jual
        $transaksis = Stokmobil::where('tahun_mobil', $tahun)
                                ->whereBetween('tanggal_jual', [$tanggalAwal, $tanggalAkhir])
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
                'tanggal_jual' => $data->tanggal_jual,
                'user_jual_id' => $data->user_jual_id, // Tambahkan user_jual_id ke dalam array
                'user_jual' => $data->user_jual,
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
