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

        $codeList = PromotionCode::with(['promotion' => function($query)
            {
                $query->select('id', 'name');
            }])
            ->when($orderBy, function($query) use($orderBy, $typeSort)
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
        $pcode = new PromotionCode();

        //Kiem tra nguoi dung co nhap code vao khong
        if(!$request->has('code')){
            //Neu khong thi tu generate ra 1 ma unique
            $pcode->code = PromotionCode::codeGenerate();
        }else{
            //Neu co thi lay gia tri tu nhap
            // $pcode->code = str_replace(" ",'',strtoupper($request->code));
            $pcode->code = PromotionCode::codeGenerate(strlen($request->code), $request->code);
        }
        $pcode->actived = $request->actived;
        $pcode->value = $request->value;
        $pcode->type = $request->type;
        $pcode->promotion_id = $request->promotion_id;
        $pcode->save();
        // return response()->json($pcode);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PromotionCode  $promotionCode
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pcode = PromotionCode::findOrFail($id);
        return response()->json($pcode);
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
