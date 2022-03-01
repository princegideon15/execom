<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Support\MessageBag;
use App\User;
use App\Logs;
use Auth;
use Hash;
use Cookie;
use DB;
use Mail;
use Carbon\Carbon;
use Session;
use Crypt;
use App\Execom;
use Browser;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

      /**
     * The maximum number of attempts to allow.
     *
     * @return int
     */
    protected $maxAttempts = 3;


    /**
     * The number of minutes to throttle for.
     *
     * @return int
     */
    protected $decayMinutes = 3;

    protected $attempts = 0;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = RouteServiceProvider::OTP;
    // protected $redirectTo = '/otp';

    private $ipaddress;

    /**
     * Get IP address of the user
     *
     * @return void
     */
    public function get_ip(){
        
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN'; 

            return $ipaddress;
    }    

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
        $this->middleware('guest')->except('logout');
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(){
        return [
            'email.required' => 'Email is required.',
            'email.exists' => 'Email does not exists.',

            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
        ];
    }

    /**
     * Validate login authentication
     * 
     * If username and password is correct, proceeds to OTP page
     *
     * @param Request $req
     * @return void
     */
    public function doLogin(Request $req){
        // get info of user by email
        $recipient = $req->email;
        $userdata = array(
            'email' => $req->email,
            'password'  => $req->password,
            'status' => 1,
        );

        $remember_me = $req->has('remember_me') ? true : false; 

        if($remember_me == 1){
            $rem = Cookie::forever('remember', $remember_me);
            $email = Cookie::forever('email', $req->email);
            $pass = Cookie::forever('password', $req->password);
        }else{
            $rem = Cookie::forget('remember');
            $email = Cookie::forget('email');
            $pass = Cookie::forget('password');
        }


        $validator = Validator::make($req->all(), [
            'email' => 'required|exists:users',
            'password' => 'required|string|min:8'
        ], $this->messages());


        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($req)) {
            $this->fireLockoutEvent($req);

            return $this->sendLockoutResponse($req);
        }

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }else{

            // attempt to do the login
            if (Auth::attempt($userdata)) {

            // validation successful!
            // redirect them to the secure section or whatever
            // return Redirect::to('secure');
            // for now we'll just echo success (even though echoing in a controller is bad)
            $att = Cookie::forget('attempts');
            
            $name =  Auth::user()->name;
            $crypt_rec = Crypt::encrypt($recipient);

            // generate OTP
            session()->regenerate();
            $otp = random_int(100000, 999999);
            $data = array('name' => $name, 'otp' => $otp);
            $otp_timestamp = Carbon::now()->timestamp;
            session(['session_timestamp' => $otp_timestamp]);
            session(['session_otp' => $otp]);

            // send OTP in the user's email
            Mail::send('auth.otpmail',$data, function($message) use ($recipient, $name) {
                $message->to($recipient, $name)->subject
                    ('ExeCom IS One-Time PIN (OTP)');
                $message->from('nrcp.execom@gmail.com','ExeCom IS Admin');
            });
            
            return redirect()->route('otp', ['email' => $crypt_rec]);

            } 

            else {        
                
           

            $total = Cookie::get('attempts');
            $val = ($total > 0) ? $total++ : 0;
            $val++;
            
            $att = Cookie::forever('attempts', $val);

     

            if($val > 2   ){ 
                $message = new MessageBag(['error' => ['You\'ve reached the maximum logon attempts. Try again after 3 minutes.']]); // if Auth::attempt fails (wrong credentials) create a new message bag instance.
            }else{
                $message = new MessageBag(['error' => ['Password invalid or Account not active. (' . $val .'/3 attempts)']]); // if Auth::attempt fails (wrong credentials) create a new message bag instance.
            } 
            
            
            $this->incrementLoginAttempts($req);

            $logs = array('log_user_id' => Auth::id(), 
                          'log_email' => $req->email, 
                          'log_ip_address' => $this->get_ip(),
                          'log_description' => 'Login attempt', 
                          'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'::'. __FUNCTION__);

            Logs::create($logs);

            $os = Browser::platformFamily() . ' ' . Browser::platFormVersion();
            $browser = Browser::browserName();
            $name = Execom::where('email', $recipient)->value('name');
            $date = date("F j, Y, g:i a");
            $ip = $this->get_ip();
            $data = array('title' => 'Login Attempt', 'name' => $name, 'recipient' => $recipient, 'os' => $os, 'browser' => $browser, 'date' => $date, 'ip' => $ip);

            Mail::send('auth.loginmail', $data, function($message) use ($recipient, $name) {
                $message->to($recipient, $name)->subject
                    ('ExeCom IS Login Attempt Information');
                $message->from('nrcp.execom@gmail.com','ExeCom IS Admin');
            });

            // validation not successful, send back to form 
            return redirect()->back()->withErrors($message)->withCookie($att)->withInput($req->only('email')); // redirect back to the login page, using ->withErrors($errors) you send the error created above
            }
        }

    }

    /**
     * Check if logged in already or OTP was generated
     *
     * @param [type] $email
     * @return void
     */
    public function otp($email){
            if(session('logged_in') == 1){
                return redirect()->route('home');
            }else{
                if(session('session_otp') != ''){
                    $recipient = Crypt::decrypt($email);
                    return view('auth.otp', compact('recipient'));
                }else{
                    return redirect('/');
                }
            }
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function otp_messages(){
        return [
            'otp.required' => 'OTP is required.',
            'otp.min' => 'OTP must be 6 digits.'
        ];
    }

    /**
     * Verify entered OTP in OTP page
     *
     * @param Request $req
     * @return void
     */
    public function verify_otp(Request $req){

        $validator = Validator::make($req->all(), [
            'otp' => 'required|min:6'
        ], $this->otp_messages());

        $otp = $req->otp;
       
        $session_otp =  session('session_otp'); 
        $session_timestamp = session('session_timestamp'); 
        $current_timestamp = Carbon::now()->timestamp;

    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }else{
            if(($current_timestamp - $session_timestamp) > 300)  // 300 refers to 300 seconds
            {
                $message = new MessageBag(['error' => ['OTP expired. Please try again.']]); 
                
            return redirect()->back()->withErrors($message);
            }
            else{
                if ($otp == $session_otp) 
                {
                    session()->forget(['session_otp', 'session_timestamp']);
                    session(['logged_in' => 1]);

                    // get user info for logs
                    $recipient = 'nrcp.execom@gmail.com';
                    $logs = array('log_user_id' => Auth::id(), 
                    'log_email' => $recipient, 
                    'log_ip_address' => $this->get_ip(),
                    'log_user_agent' => Browser::platformFamily() . ' ' . Browser::platFormVersion(),
                    'log_browser' => Browser::browserName(),
                    'log_description' => 'Login', 
                    'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'::'. __FUNCTION__);

                    // save log
                    Logs::create($logs);

                    // get browser info and other user info
                    $os = Browser::platformFamily() . ' ' . Browser::platFormVersion();
                    $browser = Browser::browserName();
                    $name = Auth::user()->name;
                    $date = date("F j, Y, g:i a");
                    $ip = $this->get_ip();
                    $data = array('title' => 'Login Success', 'name' => $name, 'recipient' => $recipient, 'os' => $os, 'browser' => $browser, 'date' => $date, 'ip' => $ip);

                    // send info to gmail of execom
                    Mail::send('auth.loginmail', $data, function($message) use ($recipient, $name) {
                        $message->to($recipient, $name)
                        ->cc(['gerard_balde@yahoo.com','gerardbalde15@gmail.com'])->subject
                            ('ExeCom IS Login Information');
                        $message->from('nrcp.execom@gmail.com','ExeCom IS Admin');
                    });

                    return redirect()->route('home');
                } 
                else {
                    $message = new MessageBag(['error' => ['OTP incorrect']]);
                    return redirect()->back()->withErrors($message);
                }
            }

        }
    }

    /**
     * Resend OTP if expired
     *
     * @param Request $req
     * @return void
     */
    public function resend(Request $req){

            $email = $req->email;
        
            session()->forget(['session_otp', 'session_timestamp']);
            $name =  Auth::user()->name;
            $recipient = Crypt::decrypt($email);

            session()->regenerate();
            $otp = random_int(100000, 999999);
            $data = array('name' => $name, 'otp' => $otp);
            $otp_timestamp = Carbon::now()->timestamp;
            session(['session_timestamp' => $otp_timestamp]);
            session(['session_otp' => $otp]);

   
            Mail::send('auth.otpmail',$data, function($message) use ($recipient, $name) {
                $message->to($recipient, $name)->subject
                    ('ExeCom IS One-Time PIN (OTP)');
                $message->from('nrcp.execom@gmail.com','ExeCom IS Admin');
            });
            
            $crypt_rec = Crypt::encrypt($email);
            return redirect()->route('otp', ['email' => $crypt_rec]);
    }
    
    /**
     * Logouts user, flush sessions
     *
     * @return void
     */
    public function logout(){
        $logs = array('log_user_id' => Auth::id(), 
                      'log_email' =>  Auth::user()->email,
                      'log_ip_address' => $this->get_ip(), 
                      'log_description' => 'Logout', 
                      'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'::'. __FUNCTION__);

        Logs::create($logs); 
        Session::flush();
        Auth::logout();

        return redirect('/');
    }




}
