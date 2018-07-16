<?php

namespace App\Http\Controllers;

use App\PromotionCode;
use Illuminate\Http\Request;

class PromotionCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $keyword = request()->input('keyword');
        $orderBy = request()->input('orderBy');
        $typeSort = request()->input('typeSort');
        $limit = request()->input('limit', 9);

        $codeList = PromotionCode::
            when($orderBy, function($query) use($orderBy, $typeSort)
                {
                    //Sap xep theo ten cot va kieu sap xep duoc truyen len
                    $query->orderBy($orderBy, $typeSort);
                }, function($query)
                {
                    //Neu ko co thi mac dinh sap theo id moi nhat
                    $query->orderBy('id', 'desc');
                })
            ->when($keyword, function($query, $keyword)
                {
                    //Tim kiem theo keyword duoc truyen len
                    $query->where('name', 'like', "%$keyword%")
                        ->orWhere('actived', 'like', "%$keyword%")
                        ->orWhere('value', 'like', "%$keyword%")
                        ->orWhere('type', 'like', "%$keyword%");
                })
            ->paginate($limit);
        
        return response()->json($codeList);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PromotionCode  $promotionCode
     * @return \Illuminate\Http\Response
     */
    public function show(PromotionCode $promotionCode)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PromotionCode  $promotionCode
     * @return \Illuminate\Http\Response
     */
    public function edit(PromotionCode $promotionCode)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PromotionCode  $promotionCode
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PromotionCode $promotionCode)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PromotionCode  $promotionCode
     * @return \Illuminate\Http\Response
     */
    public function destroy(PromotionCode $promotionCode)
    {
        //
    }
}
