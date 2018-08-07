<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\WellcomeMail;
use App\Mail\ActiveUserMail;
use Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            $v = User::validate($request->all());
            if($v->fails()){
                return response(['error'=>$v->errors()]);
            }
            $user = new User;
            $user->name = $request['name'];
            $user->email = $request['email'];
            $user->password = bcrypt($request['password']);
            $user->remember_token = str_random(30);
            $user->active = 0;
            Mail::to($request['email'])->send(new ActiveUserMail($user));
            $user->save();

            return $user;
        }catch(\Exception $e){
            return response()->json('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }

    public function login(){
        try{
            $validatedData = Validator::make(request()->all(),[
                'email' => 'required|email',
                'password' => 'required|string|min:6',
            ]);
            if($validatedData->fails()){
                return response()->json(["errors" => $validatedData->errors()]);
            }
    
            if(Auth::attempt(['email'=>request()->email, 'password'=>request()->password])){
                if(Auth::user()->active == false){
                    return response()->json(['error' => 'This User has been block.']);
                }
                $token = Auth::user()->createToken('loginToken')->accessToken;
    
                return response()->json(['loginToken' => $token]);
            }
            return response()->json(['error' => 'Wrong email or password.']);
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }
    
    public function logout(){
        try{
            // Auth::logout();
            
            //Huy bo token
            request()->user()->token()->revoke();
            return response()->json(['message' => 'Logout success.']); 
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()]); 
        }
    }

    public function activeUser(Request $request, $token){
        try{
            $user = User::where('remember_token', $token)->first();
            $user->active = 1;
            // Mail::to($user->email)->send(new WellcomeMail($user));
            $user->remember_token = null;
            $user->save();
            return response()->json(['success'=>'Account Activation Success.', 'data'=>$user]);
        }catch(\Exception $e){
            return response()->json(['message'=>'This request no longer valid.','error'=> $e->getMessage()]);
        }
    }





    // public function login2(Request $request){
    //     $this->validate($request,[
    //         'email' => 'required',
    //         'password' => 'required',
    //     ]);
    
    //     $credentials = $this->getCredentials($request);
    //     if(Auth::attempt($credentials, $request->has('remember'))){
    //         return redirect()->intended($this->redirectPath());
    //     }
    // }
    // public function forgotPassword(){
    //     try{
    //         $v = User::validate(request()->all());
    //         if($v->fails()){
    //             return response(['error'=>$v->errors()]);
    //         }
    //         //Kiem tra email da ton tai trong he thong chua
    //         $user = User::findOrFail('email');
    //     }catch(\Exception $e){
    //         return response()->json(['error' => 'Can not find a user with that e-mail address.']);
    //         // return response()->json(['error' => $e.getMessage()]);
    //     }
    // }
}
