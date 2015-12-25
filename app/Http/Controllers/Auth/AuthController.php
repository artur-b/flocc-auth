<?php

namespace Flocc\Http\Controllers\Auth;

use Flocc\User;
use Validator;
use Flocc\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mail;
use Socialite;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            //'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $activation_code = str_random(60) . $data['email'];
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'activation_code' => $activation_code,
        ]);
        return $user;
    }
    
    public function postRegister(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }
        
        $user = $this->create($request->all());
        
        Mail::send('emails.verify', ['user' => $user], function ($m) use ($user) {
            $m->from('hello@flocc.eu', 'Flocc');
            $m->to($user->email, $user->name);
            $m->subject('Flocc registration');
        });
        
        // autologin?
        // Auth::login($user);

        // TOFIX: return to main view with flash message
        return redirect('auth/postregister');
    }
    
    public function redirectToProvider()
    {
        return Socialite::driver('facebook')->redirect();
    }
    
    public function handleProviderCallback()
    {
        try {
            $user = Socialite::driver('facebook')->user();
        } catch (Exception $e) {
            return redirect('auth/facebook');
        }
 
        $authUser = $this->findOrCreateUser($user);
 
        Auth::login($authUser);
 
        return redirect('/');
    }
    
    private function findOrCreateUser($facebookUser)
    {
        $authUser = User::where('facebook_id', $facebookUser->id)->first();
 
        if ($authUser){
            return $authUser;
        }
 
        return User::create([
            'name' => $facebookUser->name,
            'email' => $facebookUser->email,
            'facebook_id' => $facebookUser->id,
            'activation_code' => 'facebook',
            'active' => 1,
            //'avatar' => $facebookUser->avatar
        ]);
    }
    
    public function verifyEmail($code)
    {
        $verifiedUser = User::where('activation_code', $code)->first();
        
        if ($verifiedUser) {
            $verifiedUser->active = 1;
            $verifiedUser->save();
            
            Auth::login($verifiedUser);
            
            return redirect('/');

        }
        
        return redirect('auth/login');
    }
}
