<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stokmobil;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class StatistikController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function totalUnitKeseluruhan()
    {

        // Hitung total unit mobil yang terkait dengan pengguna
        $totalUnitKeseluruhan = Stokmobil::count();

        return response()->json([
            'status' => true,
            'total_keseluruhan' => $totalUnitKeseluruhan,
        ]);
    }

    public function totalUnitTersedia()
    {
        // Mendapatkan ID pengguna yang terotentikasi

        // Hitung total unit mobil yang tersedia (statusnya 'available') dan terkait dengan pengguna
        $totalUnitTersedia = Stokmobil::where('status', 'available')->count();

        return response()->json([
            'status' => true,
            'total_tersedia' => $totalUnitTersedia,
        ]);
    }

    public function totalUnitTerjual()
    {
        // Hitung total unit mobil yang terjual (statusnya 'sold') dan terkait dengan pengguna
        $totalTerjual = Stokmobil::where('status', 'sold')->count();

        return response()->json([
            'status' => true,
            'total_terjual' => $totalTerjual,
        ]);
    }

    public function totalTransmisiManual()
    {

        // Hitung total unit mobil dengan transmisi manual dan terkait dengan pengguna
        $totalUnitManual = Stokmobil::where('transmisi', 'manual')->count();

        return response()->json([
            'status' => true,
            'total_manual' => $totalUnitManual,
        ]);
    }

    public function totalTransmisiMatic()
    {

        // Hitung total unit mobil dengan transmisi matic dan terkait dengan pengguna
        $totalUnitMatic = Stokmobil::where('transmisi', 'matic')->count();

        return response()->json([
            'status' => true,
            'total_matic' => $totalUnitMatic,
        ]);
    }

    public function getTotalPenjualan()
    {

        // Query untuk menghitung total penjualan terkait dengan pengguna
        $totalSales = Stokmobil::where('status', 'sold')->sum('harga_jual');
        $totalUnitSold = Stokmobil::where('status', 'sold')->count();

        // Format total penjualan dengan tanda titik sebagai pemisah ribuan
        $formattedTotalSales = number_format($totalSales, 0, ',', '.');

        return response()->json([
            'status' => true,
            'total_penjualan' => $formattedTotalSales,
            'total_unit_terjual' => $totalUnitSold,
            'message' => 'Total penjualan dan jumlah unit terjual berhasil diambil',
        ]);
    }

    public function getTotalPenjualanTanggal(Request $request)
    {
        // Validasi permintaan
        $rules = ([
            'tanggal_awal' => 'required|date|date_format:Y-m-d',
            'tanggal_akhir' => 'required|date|date_format:Y-m-d|after_or_equal:tanggal_awal',
        ]);

        $validator = Validator::make($request->all(), $rules);

        // Ambil tanggal awal dan akhir dari permintaan
        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');

        // Query untuk menghitung total penjualan dan jumlah unit mobil terjual berdasarkan tanggal dan pengguna
        $totalSales = Stokmobil::whereBetween('tanggal_jual', [$tanggalAwal, $tanggalAkhir])
                              ->where('status', 'sold')->sum('harga_jual');
        $totalUnitSold = Stokmobil::whereBetween('tanggal_jual', [$tanggalAwal, $tanggalAkhir])
                                 ->where('status', 'sold')->count();

        // Format total penjualan dengan tanda titik sebagai pemisah ribuan
        $formattedTotalSales = number_format($totalSales, 0, ',', '.');

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'total_penjualan' => 0,
                'total_unit_terjual' => 0,
                'message' => $validator->errors()->first(),
            ]);
        }else{
            return response()->json([
                'status' => true,
                'total_penjualan' => $formattedTotalSales,
                'total_unit_terjual' => $totalUnitSold,
                'message' => 'Total penjualan dan jumlah unit terjual berhasil diambil berdasarkan tanggal',
            ]);
        }

    }
}
