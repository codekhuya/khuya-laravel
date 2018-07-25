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
        try{
            $keyword = request()->input('keyword');
            $orderBy = request()->input('orderBy');
            $typeSort = request()->input('typeSort');
            $limit = request()->input('limit', 9);
    
            $promotions = Promotion::withCount('promotionCodes')
            ->when($orderBy, function($query) use($orderBy, $typeSort)
                {
                    $query->orderBy($orderBy, $typeSort);
                },
                function($query)
                {
                    $query->orderBy('id','desc');
                }
            )
            ->when($keyword, function($query, $keyword)
                {
                    $query->where('name', 'like', "%$keyword%");
                })
            ->paginate($limit);
            return $this->sendMessage("success", 'Send data successful.', $promotions, 200);
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
    public function store(Request $request)
    {
        try{
            $validate = Promotion::validator($request->all());
            if($validate->fails()){
                return $this->sendMessage("failed", 'Input Invalid', $validate->errors(), 400);
            }
            $promotion = new Promotion();
            
            $promotion->name = $request->name;
            $promotion->description = $request->description ?? null;
            $promotion->started_date = $request->started_date ." ". $request->started_time;
            $promotion->ended_date = $request->ended_date ." ". $request->ended_time;
            $promotion->actived = $request->actived ?? 1;
            $promotion->disposable = $request->disposable ?? 0;
            $promotion->save();
            return $this->sendMessage("success", 'Send Data Successful.', $promotion, 200);
            
        }catch(Exception $e){
            return $this->sendMessage("failed", 'Unable to create. Please try again.', $e->getMessage(), 502);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Promotion  $promotion
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $proList = Promotion::withTrashed()->withCount('promotionCodes')->findOrFail($id);
            return $this->sendMessage("success", 'Send Data Successful.', $proList, 200);
        }catch(\Exception $e){
            return $this->sendMessage('failed', 'Promotion Not Found.', $e->getMessage(), 404);
        }
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
    public function update($id)
    {
        try{
            $input = request()->all();
            $promotion = Promotion::findOrFail($id);
            $validate = Promotion::validator($input);
            if($validate->fails()){
                return $this->sendMessage("failed", 'Input Invalid', $validate->errors(), 400);
            }
            $promotion->name = $input['name'];
            $promotion->description = $input['description'] ?? null;
            $promotion->started_date = $input['started_date'] ." ". $input['started_time'];
            $promotion->ended_date = $input['ended_date'] ." ". $input['ended_time'];
            $promotion->actived = $input['actived'];
            $promotion->disposable = $input['disposable'];
            $promotion->save();
            return $this->sendMessage("success", 'Update Successful.', $promotion, 200);
        }catch(\Exception $e){
            return $this->sendMessage('failed', 'Promotion Not Found.', $e->getMessage(), 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Promotion  $promotion
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $promotion = Promotion::findOrFail($id);
            $promotion->delete();
            return $this->sendMessage("success", 'Delete Successful.', $promotion, 200);
        }catch(\Exception $e){
            return $this->sendMessage('Failed', 'Promotion Not Found.', $e->getMessage(), 404);
        }
    }
}
