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

    public static function codeGenerate($length = 9)
    {
        $pool = strtoupper(str_random($length));
        $existedCode = PromotionCode::where('code',$pool)->first();
        if($existedCode){
            //Neu trung thi goi lai
            return $pool = PromotionCode::codeGenerate($length);
        }
        return $pool;
    }
}
