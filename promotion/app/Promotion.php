<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\PromotionCode;
class Promotion extends Model
{
    protected $fillable = ['name', 'description', 'started_date', 'ended_date', 'actived', 'disposabled'];

    public function promotionCodes(){
        return $this->hasMany(Promotion::class);
    }
}
