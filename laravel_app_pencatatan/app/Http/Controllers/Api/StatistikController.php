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
