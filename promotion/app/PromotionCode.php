<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Promotion;

class PromotionCode extends Model
{
    protected $fillable = ['code', 'actived', 'value', 'type'];
    
    public function promotion(){
        return $this->belongsTo(Promotion::class);
    }

    public static function codeGenerate($length = 8, $value = null)
    {
        $pool = "";
        if(is_null($value)){
            //Tao ra ma IN HOA, xoa khoang trang
            $pool = str_replace(" ", '', strtoupper(str_random($length)));
            $existedCode = PromotionCode::where('code',$pool)->first();
            if($existedCode){
                //Neu code da ton tai thi tao lai ma khac
                return $pool = PromotionCode::codeGenerate($length);
            }
            return $pool;
        }else{
            $pool = str_replace(" ", '', strtoupper($value));
            return $pool;
        }
    }
}
