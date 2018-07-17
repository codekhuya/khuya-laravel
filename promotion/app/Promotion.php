<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\PromotionCode;
class Promotion extends Model
{
    protected $fillable = ['name', 'description', 
    'started_date', 'ended_date', 'actived', 'disposable', 'amount'];

    public function promotionCodes(){
        return $this->hasMany(PromotionCode::class);
    }
}
