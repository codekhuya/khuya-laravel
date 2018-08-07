<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Hash;
use Illuminate\Support\Facades\Mail;
use Validator;
use App\Mail\ResetPasswordMail;

class PasswordController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    public function changePassword(Request $request){
        try{
            $validatedData = Validator::make($request->all(),[
                'currentPassword' => 'required',
                'newPassword' => 'required|string|min:6',
                'c_newPassword' => 'required|string|min:6|same:newPassword',
            ]);
            if($validatedData->fails()){
                return response()->json(["errors" => $validatedData->errors()]);
            }

            //Kiem tra password nhap vao
            if(!(Hash::check($request->get('currentPassword'), Auth::user()->password))){
                return response()->json(['error' => 'Your current password does not matches with the password you provided. Please try again.']);
            }

            //Password cu va moi khong duoc trung nhau
            if(strcmp($request->get('currentPassword'), $request->get('newPassword')) == 0){
                return response()->json(["error" => "New Password cannot be same as your Current Password. Please choose a different password."]);
            }

            //Huy bo token login
            request()->user()->token()->revoke();

            //Cap nhat lai mat khau
            $user = Auth::user();
            $user->password = bcrypt($request->get('newPassword'));
            $user->save();
            return response()->json(["success" => "Changed password successfull.", 'data' => $user]);
        }
        catch(\Exception $e){
            return response()->json(["error" => $e->getMessage()]);
        }
    }


    //Ham nhap mail de request doi password
    public function sendMailForgotPassword(Request $request){
        try{
            $v = Validator::make($request->all(),[
                'email'=>'required|email'
            ]);
            if($v->fails()){
                return response()->json(["error" => $v->errors()]);
            }

            $user = User::where('email',$request['email'])->first();
            if(!$user){
                return response()->json(['error', 'This email does not exists. Please check again.']);
            }
            $user->remember_token = str_random(35);
            $user->save();
            //Send mail
            Mail::to($user->email)->send(new ResetPasswordMail($user));
            return response()->json(["success" => 'Send e-mail success, please check your e-mail.']);
        }catch(\Exception $e){
            return response()->json(['error', $e->getMessage()]);
        }
    }
    
    public function changePasswordByToken(Request $request, $token){
        // return $token;

        try{
            $validatedData = Validator::make($request->all(),[
                'newPassword' => 'required|string|min:6',
            ]);
            if($validatedData->fails()){
                return response()->json(["errors" => $validatedData->errors()]);
            }
            $userByToken = User::where('remember_token',$token)->first();
            if(!$userByToken){
                return response()->json(['error' => 'This request no longer valid. Please try again or contact administrator.']);
            }
            //Huy bo token login
            $request->user()->token()->revoke();
            
            //Cap nhat lai mat khau
            $userByToken->password = bcrypt($request->get('newPassword'));
            $userByToken->remember_token = null;
            $userByToken->save();
            return response()->json(["success" => "Changed password successfull.", 'data' => $userByToken]);
        }
        catch(\Exception $e){
            return response()->json(["error" => $e->getMessage()]);
        }
    }
}
