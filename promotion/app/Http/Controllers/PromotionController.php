<?php

namespace App\Http\Controllers;

use App\Promotion;
use App\PromotionCode;
use Illuminate\Http\Request;


class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $keyword = request()->input('keyword');
        $order = request()->input('order');
        $sort = request()->input('sort');
        $limit = request()->input('limit');

        $promotions = Promotion::with('promotionCodes')->
            when($order, function($query) use($order, $sort)
            {
                $query->orderBy($order, $sort);
            },
            function($query)
            {
                $query->orderBy('id','desc');
            }
        )
        ->when($keyword, function($query, $keyword)
            {
                $query->where('name', 'like', "%$keyword%")
                    ->orWhere('description', 'like', "%$keyword%")
                    ->orWhere('started_date', 'like',"%$keyword%")
                    ->orWhere('ended_date', 'like', "%$keyword%");
            })
        ->paginate($limit);
        return response()->json($promotions);
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
        $validate = Promotion::validator($request->all());
        if($validate->fails()){
            return $this->sendMessage(400, false, 'Loi xac thuc du lieu', $validate->errors());
        }
        $promotion = new Promotion();
        
        $promotion->name = $request->name;
        $promotion->description = $request->description;
        $promotion->started_date = (string)$request->started_date;
        $promotion->ended_date = (string)$request->ended_date;
        $promotion->actived = $request->actived;
        $promotion->disposable = $request->disposable;
        
        //Kiem tra neu code khong su dung 1 lan
        if(!$promotion->disposable){
            $promotion->amount = $request->amount;
            $promotion->save();

            $i = 0;
            while($i < $promotion->amount){
                //Neu co so luong code thi tu dong sinh ma code
                $code = new PromotionCode();
                $code->promotion_id = $promotion->id;
                $code->code = $code->codeGenerate();
                $code->value = $request->value;
                $code->type = $request->type;
                $code->actived = 1;
                $code->save();
                // $promotion->promotionCodes()->create($code);
                $i++;
            }
        }else{
            $promotion->amount = -1;
            $promotion->save();

            $code = new PromotionCode();
            $code->promotion_id = $promotion->id;
            $code->code = $code->codeGenerate(strlen($request->code), $request->code);
            $code->value = $request->value;
            $code->type = $request->type;
            $code->actived = 1;
            $code->save();
        }
        return response()->json($promotion->with('promotionCodes')->find($promotion->id));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Promotion  $promotion
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $proList = Promotion::findOrFail($id);
        return response()->json($proList);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Promotion  $promotion
     * @return \Illuminate\Http\Response
     */
    public function edit(Promotion $promotion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Promotion  $promotion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Promotion $promotion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Promotion  $promotion
     * @return \Illuminate\Http\Response
     */
    public function destroy(Promotion $promotion)
    {
        //
    }

    /**
     * Kiểm tra mã hợp lệ hay kh
     *
     * @param  \App\Promotion  $promotion
     * @return \Illuminate\Http\Response
     */
    // public function checkCode($code){
    //     $promotion = Promotion::where('actived', 1)
    //     ->with(['promotionCodes' => function($query) use($code)
    //         {
    //             $query->where('code', $code)
    //                 ->where('actived', 1);
    //         }
    //     ])->get();
    //     return response()->json($promotion);
    // }
}
