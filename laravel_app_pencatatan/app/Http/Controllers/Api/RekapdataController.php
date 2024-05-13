<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RekapData;
use App\Models\Listdata;

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
         $request->validate([
             'tanggal_awal' => 'required|date|date_format:Y-m-d',
             'tanggal_akhir' => 'required|date|date_format:Y-m-d',
         ]);

         // Ambil tanggal awal dan tanggal akhir dari permintaan
         $tanggalAwal = $request->input('tanggal_awal');
         $tanggalAkhir = $request->input('tanggal_akhir');

         // Query untuk mendapatkan daftar data berdasarkan jangkauan tanggal
         $dataList = Listdata::whereBetween('tanggal_beli', [$tanggalAwal, $tanggalAkhir])->get();

         return response()->json([
             'status' => true,
             'data_tanggal' => $dataList,
         ]);
     }

    public function index()
    {

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
