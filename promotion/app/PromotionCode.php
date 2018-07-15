<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Promotion;

class PromotionCode extends Model
{
    public function promotion(){
        return $this->belongsTo(Promotion::class);
    }
}
