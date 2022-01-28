<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

use Mail;
use Hash;
use Carbon\Carbon;
use Session;
use Crypt;
use App\Logs;
use App\Execom;
use Auth;


class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    // use ResetsPasswords;

    private $ipaddress;

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
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    // protected $redirectTo = RouteServiceProvider::HOME;

    public function messages(){
        return [
            'email.required' => 'Email is required.',
            'email.exists' => 'Email does not exists.',

            'password.required' => 'New password is required.',
            'password.min' => 'New password must be at least 8 characters.',

            'rep_password.required' => 'Repeat password is required.',
            'rep_password.min' => 'Repeat password must be at least 8 characters.',
            'rep_password.same' => 'New password and Repeast password must match.'
            
        ];
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

    public function verify(Request $req){
        
    
        $recipient = $req->email;

        $validator = Validator::make($req->all(), [
            'email' => 'required|exists:users',
            'password' => 'required|string|min:8',
            'rep_password' => 'required|string|min:8|same:password'
        ], $this->messages());

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        else{
            $recipient = $req->email;
    
            session()->regenerate();
            $otp = random_int(100000, 999999);
            $data = array('name' => 'Gerard Balde', 'otp' => $otp);
            $otp_timestamp = Carbon::now()->timestamp;
            session(['session_timestamp' => $otp_timestamp]);
            session(['session_otp' => $otp]);
            session(['session_new_p' => $req->password]);

            $name = Execom::where('email', $recipient)->value('name');
    
            Mail::send('auth.otpmail',$data, function($message) use ($recipient, $name) {
                $message->to($recipient, $name)->subject
                    ('ExeCom IS Reset Pasword One-Time PIN (OTP)');
                $message->from('nrcp.execom@gmail.com','ExeCom IS Admin');
            });

            // return view('auth.passwords.otp', compact('recipient'));
            $crypt_rec = Crypt::encrypt($recipient);

            
            return redirect()->route('reset-otp', ['email' => $crypt_rec]); //todo
            
        }
        
    }
    
    public function otp($email){
        if(session('logged_in') == 1){
            return redirect()->route('home');
        }else{
            if(session('session_otp') != ''){
                $recipient = Crypt::decrypt($email);
                return view('auth.passwords.otp', compact('recipient'));
            }else{
                return redirect('/');
            }
        }
    }

    public function verify_otp(Request $req){
        
        $session_new_p =  session('session_new_p'); 
        
        $userdata = array(
            'password'  => Hash::make($session_new_p),
            'status' => 0,
        );


        $validator = Validator::make($req->all(), [
            'otp' => 'required|min:6'
        ], $this->otp_messages());

        $otp = $req->otp;
       
        $session_otp =  session('session_otp'); 
        $session_timestamp = session('session_timestamp'); 
        $current_timestamp = Carbon::now()->timestamp;

        // return $session_timestamp . ' - ' . $current_timestamp;exit;

        if ($validator->fails()) {
            // return 'required field';
            return redirect()->back()->withErrors($validator)->withInput();
        }else{

            if(($current_timestamp - $session_timestamp) > 300)  // 300 refers to 300 seconds
            {
                $message = new MessageBag(['error' => ['OTP expired. Please try again.']]); //todo 
                
                // return $session_timestamp - $current_timestamp . ' -  opt expired';
            return redirect()->back()->withErrors($message);
            // return 'expired';

            }
            else{
                if ($otp == $session_otp) 
                {
                    $email = Crypt::decrypt($req->crypt_e);
                    session()->forget(['session_otp', 'session_timestamp', 'session_new_p']);
                    
                    $status = Execom::where('email', $email)->value('status'); 
            
                    if($status == 0){
                        return view('auth.passwords.confirm');
                    }else{
                        Execom::where('email', $email)->update($userdata);

                        $name = Execom::where('email', $email)->value('name');
                        $id = Execom::where('email', $email)->value('user_id');

                        $this->mail($email, $name, $id);

                        return view('auth.passwords.confirm');
                    }       
                } 
                else {
                    $message = new MessageBag(['error' => ['OTP incorrect']]);
                    
                    return redirect()->back()->withErrors($message);
                }
            }

        }
    }

    public function mail($email, $name, $id)
    {
        $data = array('name' => $name, 'email' => $email, 'id' => $id);

        Mail::send(['html'=>'email.message'], $data, function($message) use ($email, $name, $id) {

            $message->to($email, $name)
            ->subject('ExeCom IS Password Reset and Account Activation');

            $message->from('nrcp.execom@gmail.com','ExeCom IS Admin');

        });

        $logs = array('log_user_id' => $id,
          'log_email' => $email,
          'log_ip_address' => $this->get_ip(),
          'log_description' => 'Password Reset and Account Activation', 
          'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'::'. __FUNCTION__);

        Logs::create($logs);
    }

    public function activate($id){

        $status = Execom::where('user_id', $id)->value('status'); 
       
        if($status == 1){
            // return abort(404);
            return view('auth.login')->with('activated', 'Your account is already activated.');
        }else{
            Execom::where('user_id', $id)->update(['status'=>1]);
            $email = Execom::where('user_id', $id)->value('email');

            $logs = array('log_user_id' => $id,
            'log_email' => $email,
            'log_ip_address' => $this->get_ip(),
            'log_description' => 'Account activated', 
            'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'::'. __FUNCTION__);

            Logs::create($logs);

            return view('auth.login')->with('activated', 'Your account is now activated.');
        }
        
    }

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
                ('ExeCom IS Reset Pasword One-Time PIN (OTP)');
            $message->from('nrcp.execom@gmail.com','ExeCom IS Admin');
        });
        
        $crypt_rec = Crypt::encrypt($email);
        return redirect()->route('otp', ['email' => $crypt_rec]);
}
}
