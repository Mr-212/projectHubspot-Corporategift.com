<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    private $user ;
    public function __construct(){
       $this->user = User::class;
    }

    public function index(){
   
    }
    

    public function get_login()
    {
        return view('auth.login');
    }
    public function post_login(Request $request)
    {
        $rules = ['email'=>'required|email','password'=>'required'];
        $validator = Validator::make($request->all(),$rules);
        if(!$validator->fails()){
            $credentials = $request->only('email', 'password');
            if(Auth::attempt($credentials)){
                $request->session()->regenerate();
                return redirect()->intended('/dashboard');
            }
        }
        else
            return redirect('auth/login')->withErrors($validator)->withInput();
      
    }


    public function get_sign_up()
    {
        return view('auth.sign-up');
    
    }

    public function post_sign_up(Request $request)
    {
        $rules = [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',  
        ];
        $validator = Validator::make($request->all(),$rules);
        if(!$validator->fails()){
            $password = Hash::make($request->get('password'));
            $request->request->add(['password'=>$password]);
            $user = $this->user::create($request->all());
            if($user){
                Auth::login($user);
                return redirect()->intended('/dashboard');
            }
        }
        else
             return redirect('auth/sign-up')->withErrors($validator)->withInput();
    
    }


    public function logout(){
        if(auth()->check()){
          Auth::logout();
          return redirect('auth/login');
        }
    }
}
