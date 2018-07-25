<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Promotion;
use Validator;

class PromotionCode extends Model
{
    protected $fillable = ['promotion_id', 'code', 'actived', 'value', 'type'];
    
    public function promotion(){
        return $this->belongsTo(Promotion::class);
    }

    protected function validator($data){
        return Validator::make($data, [
            'code' => 'unique:promotion_codes,code',
            'actived' => 'required',
            'value' => 'required|numeric',
            'type' => 'required',
            'promotion_id' => 'required|exists:promotions,id',
        ]);
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

        /**
         * CACH 2
         * $pool = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 6);
         * Cach nay tao mau khong can truy xuat vao database
         */
    }
}
