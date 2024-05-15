<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Listdata;

class StatistikController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */

     public function totalUnitKeseluruhan()
     {
         // Hitung total unit mobil yang terjual (statusnya 'sold')
         $totalUnitKeseluruhan = Listdata::all()->count();

         return response()->json([
             'status' => true,
             'total_keseluruhan' => $totalUnitKeseluruhan,
         ]);
     }

     public function totalUnitTersedia()
     {
         // Hitung total unit mobil yang terjual (statusnya 'sold')
         $totalUnitTersedia = Listdata::where('status', 'available')->count();

         return response()->json([
             'status' => true,
             'total_tersedia' => $totalUnitTersedia,
         ]);
     }

     public function totalUnitTerjual()
    {
        // Hitung total unit mobil yang terjual (statusnya 'sold')
        $totalTerjual = Listdata::where('status', 'sold')->count();

        return response()->json([
            'status' => true,
            'total_terjual' => $totalTerjual,
        ]);
    }
    public function totalTransmisiManual()
    {
        // Hitung total unit mobil yang terjual (statusnya 'sold')
        $totalUnitManual = Listdata::where('transmisi', 'manual')->count();

        return response()->json([
            'status' => true,
            'total_manual' => $totalUnitManual,
        ]);
    }
    public function totalTransmisiMatic()
    {
        // Hitung total unit mobil yang terjual (statusnya 'sold')
        $totalUnitMatic = Listdata::where('transmisi', 'matic')->count();

        return response()->json([
            'status' => true,
            'total_matic' => $totalUnitMatic,
        ]);
    }
    public function getTotalPenjualan()
    {
        // Query untuk menghitung total penjualan
        $totalSales = Listdata::where('status', 'sold')->sum('harga_jual');
        $totalUnitSold = Listdata::where('status', 'sold')->count();
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
    $request->validate([
        'tanggal_awal' => 'required|date|date_format:Y-m-d',
        'tanggal_akhir' => 'required|date|date_format:Y-m-d|after_or_equal:tanggal_awal',
    ]);

    // Ambil tanggal awal dan akhir dari permintaan
    $tanggalAwal = $request->input('tanggal_awal');
    $tanggalAkhir = $request->input('tanggal_akhir');

    // Query untuk menghitung total penjualan dan jumlah unit mobil terjual berdasarkan tanggal
    $totalSales = Listdata::whereBetween('tanggal_beli', [$tanggalAwal, $tanggalAkhir])
                            ->where('status', 'sold')->sum('harga_jual');
    $totalUnitSold = Listdata::whereBetween('tanggal_beli', [$tanggalAwal, $tanggalAkhir])
                                ->where('status', 'sold')->count();
    // Format total penjualan dengan tanda titik sebagai pemisah ribuan
    $formattedTotalSales = number_format($totalSales, 0, ',', '.');

    return response()->json([
        'status' => true,
        'total_penjualan' => $formattedTotalSales,
        'total_unit_terjual' => $totalUnitSold,
        'message' => 'Total penjualan dan jumlah unit terjual berhasil diambil berdasarkan tanggal',
    ]);
}
}
