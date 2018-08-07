<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected function rules(){
        return [
        //
        ];
    }

    //Ham kiem tra hop le
    public function validate($data){
        $validate = Validate::make($data, $this->rules());
        //Neu du lieu khong hop le thi gan loi cho $errors va tra ve false
        if($validate -> fails()){
            $this->errors = $validate->errors();
            return false;
        }
        return true;
    }

    public function errors()
    {
        return $this->errors;
    }
}
