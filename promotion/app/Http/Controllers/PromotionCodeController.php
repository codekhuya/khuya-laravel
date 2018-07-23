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
        $validate = PromotionCode::validator($request->all());
        if($validate->fails()){
            return $this->sendMessage(400, false, 'Loi xac thuc du lieu', $validate->errors());
        }
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
        return $this->sendMessage(200, true, 'Save success.', $pcode);
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
            $pcode = PromotionCode::withTrashed()->findOrFail($id);
            return $this->sendMessage(200, true, 'Send data successful.', $pcode);
        }catch(\Exception $e){
            return $this->sendMessage(404, false, 'Code not found.', $e->getMessage());
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
            return $this->sendMessage(200, true, 'Delete successfull.');
        }catch(\Exception $e){
            return $this->sendMessage(400, false, 'Code not found.', $e->getMessage());
        }
    }

    /**
     * Kiểm tra mã hợp lệ hay khong
     *
     * @param  \App\Promotion  $promotion
     * @return \Illuminate\Http\Response
     */
    public function checkCode($inputCode){
        try{
            $code = PromotionCode::where('code', $inputCode)->with('promotion')->firstOrFail();
            // return response()->json($code);
            $now = now();
            $now_date = str_before($now, " ");
            $now_time = str_after($now, " ");
            $startedTime = str_after($code->promotion->started_date, " ");
            $endedTime = str_after($code->promotion->ended_date, " ");
            $startedDate = str_before($code->promotion->started_date, " ");
            $endedDate = str_before($code->promotion->ended_date, " ");

            if($code->code){
                // Kiem tra promotion_actived va code_actived co active ko;
                if($code->actived && $code->promotion->actived){
                    //Neu khuyen mai trong ngay
                    //Kiem tra thoi gian khuyen mai: ngay";
                    if(($startedDate <= $now_date && $endedDate >= $now_date)){
                        if($now_date == $startedDate && $now_date == $endedDate){
                            //check gio
                            if($now_time >= $startedTime && $now_time <= $endedTime){
                                return "Khuyen mai con vai GIO de su dung";
                            }
                        }
                        return response()->json(['status' => 'OK Code','message' => 'Code con NGAY su dung']);
                    }else{
                        return response()->json(['status' => 'Expired Code','message' => 'Code da het han (ngay).']);
                    }
                }else{
                    return response()->json('Expired Code', 'Code da het.');
                }
            }
        }catch(\Exception $e){
            return response()->json(['code' => 404, 
            'status' => 'Note Found',
            'message' => 'Code khong ton tai',
            'errors' => 'Loi: '.$e->getMessage()]);
        }
    }


    public function codeGenerate2($length = 8, $value = null)
    {
        $existedCode = PromotionCode::pluck('code')->all();
        $pool = str_replace(" ", '', strtoupper(str_random($length)));
        if(is_null($value)){
            for($i=0; $i < strlen($existedCode);$i++){
                if($pool === $existedCode[i]){
                    $pool = str_replace(" ", '', strtoupper(str_random($length)));
                    break;
                }
            }  
            return $pool;
        }else{
            $pool = str_replace(" ", '', strtoupper($value));
            return $pool;
        }
    }
}
