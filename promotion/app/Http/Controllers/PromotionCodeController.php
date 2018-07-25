<?php

namespace App\Http\Controllers;

use App\PromotionCode;
use Illuminate\Http\Request;
use App\Promotion;

class PromotionCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
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
                        $query->where('code', 'like', "%$keyword%");
                    })
                ->paginate($limit);
            
            // return response()->json($codeList);
            return $this->sendMessage("success", 'Send data successful.', $codeList, 200);
        }catch(\Exception $e){
            return $this->sendMessage("failed", 'Server busy. Please try again.', $e->getMessage(), 502);
        }
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
    public function store()
    {
        try{
            $input = request()->all();
            $validate = PromotionCode::validator($input);
            if($validate->fails()){
                return $this->sendMessage("failed", 'Input Invalid', $validate->errors(), 400);
            }
            $promotion = Promotion::find($input['promotion_id']);
            if(!$promotion){
                return $this->sendMessage("failed", 'Promotion not found', null, 404);
            }

            //Neu Code cua Promotion hien tai: dung mot lan
            if($promotion->disposable){
                //Neu khong nhap so luong thi nem ra loi
                if(!request()->has('codes_amount')){
                    return $this->sendMessage("failed", 'Input Invalid', 'The amount of promotion code must be not null and greater than 0.', 400);
                }
                /**
                 * Tu dong tao ra ma random 
                 * va them PromotionCode vao database theo so luong nhap vao
                 * */ 
                $i = 0;
                while($i < $input['codes_amount']){
                $input['code'] = PromotionCode::codeGenerate();
                //Cach 2
                // $input['code'] = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 6);
                $pcode = PromotionCode::create($input);
                $i++;
                }
            //Neu Code cua Promotion hien tai: dung nhieu lan
            }else{
                if(!request()->has('code')){
                    return $this->sendMessage("Code Invalid", 'Code must be not null', null, 400);
                }
                $input['code'] = PromotionCode::codeGenerate(strlen($input['code']), $input['code']);
                $promotion->promotionCodes()->create($input);
            }
            return $this->sendMessage("success", 'Saved successful.', $promotion->withCount('promotionCodes')->find($promotion->id), 200);
        }catch(\Exception $e){
            return $this->sendMessage("failed", 'Errors', $e->getMessage(), 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PromotionCode  $promotionCode
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $pcode = PromotionCode::findOrFail($id);
            return $this->sendMessage('success', 'Send data successful.', $pcode, 200);
        }catch(\Exception $e){
            return $this->sendMessage('failed', 'Code not found.', $e->getMessage(), 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PromotionCode  $promotionCode
     * @return \Illuminate\Http\Response
     */
    public function edit(PromotionCode $promotionCode)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PromotionCode  $promotionCode
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        try{
            $input = request()->all();
            $pcode = PromotionCode::findOrFail($id);
            $input['code'] = str_replace(" ", '', strtoupper($input['code']));
            $pcode->update($input);
            return $this->sendMessage('success', 'Update successful.', $pcode, 200);
        }catch(\Exception $e){
            return $this->sendMessage('failed', 'Promotion code not found.', $e->getMessage(), 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PromotionCode  $promotionCode
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $code = PromotionCode::findOrFail($id);
            $code->delete();
            return $this->sendMessage('success', 'Delete successfull.','OK', 200);
        }catch(\Exception $e){
            return $this->sendMessage('failed', 'Code not found.', $e->getMessage(), 404);
        }
    }

    /**
     * Kiểm tra mã hợp lệ hay khong
     *
     * @param  \App\Promotion  $promotion
     * @return \Illuminate\Http\Response
     */
    public function checkCode(Request $inputCode){
        try{
            if(!$inputCode->has('code') || $inputCode['code']==null){
                return response()->json(['status'=>'false', 'message'=>'Code Not Found.']);
            }
            $code = PromotionCode::where('code', $inputCode['code'])->with('promotion')->firstOrFail();

            $now = now();
            $current_date = $now->toDateString();
            $current_time = $now->toTimeString();

            $startedDate = str_before($code->promotion->started_date, " ");
            $endedDate = str_before($code->promotion->ended_date, " ");
            $startedTime = str_after($code->promotion->started_date, " ");
            $endedTime = str_after($code->promotion->ended_date, " ");

            if($code->code){
                // Kiem tra PromotionCode va Promotion con active hay khong.
                if(!$code->actived || !$code->promotion->actived){
                    return response()->json(['actived'=>'false', 'message'=>'Code Invalid.']);
                }
                //Kiem tra ngay con hieu luc
                if(($current_date >= $startedDate) && ($current_date <= $endedDate)){
                    //Neu thoa man ngay thi tiep tuc kiem tra gio con hieu luc
                    if(($current_time >= $startedTime) && ($current_time <= $endedTime)){
                        //Neu code dung 1 lan (disposable = 1) thi check la da dung
                        if($code->promotion->disposable){
                            $code->actived = 0;
                            $code->save();
                        }
                        return response()->json(['status'=>'true', $code]);
                    }else{
                        return response()->json(['time'=>'false', 'message'=>'Code Invalid.']);
                    }
                }else{
                    return response()->json(['date'=>'false', 'message'=>'Code Invalid.']);
                }
            }
        }catch(\Exception $e){
            return $this->sendMessage('failed', 'Code not found.', $e->getMessage(), 404);
        }
    }
}
