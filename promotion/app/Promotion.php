<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\PromotionCode;
use Validator;

class Promotion extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'description', 
    'started_date', 'ended_date', 'actived', 'disposable', 'amount'];

    public function promotionCodes(){
        return $this->hasMany(PromotionCode::class);
    }

    protected function validator($data){
        return Validator::make($data, [
            'name' => 'required|unique:promotions,name',
            'started_date' => 'required',
            'ended_date' => 'required',
            'actived' => 'required',
            'disposable' => 'required',
            'amount' => 'required|numeric',
        ]);
    }
}
