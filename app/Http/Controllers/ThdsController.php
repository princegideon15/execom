<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Thds;
use App\Logs;
use Auth;
use Browser;

class ThdsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

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
     * Get CSF from SKMS database
     *
     * @return void
     */
    public function get_csf_list(){
        return Thds::get_csf_list();
    }

    /**
     * Get CSF from SKMS database
     *
     * @return void
     */
    public function get_csf_desc($id, $user){
        return Thds::get_csf_desc($id, $user);
    }

    public function get_csf_answers($user){
        return Thds::get_csf_answers($user);
    }

    public function get_thds($id){
        return Thds::get_thds($id);
    }
    
    public function get_action_thds($id){
        return Thds::get_action_thds($id);
    }

    /**
     * Get Customer service feedback
     *
     * @return void
     */
    public function get_csf($id){
        
        $os = Browser::platformFamily() . ' ' . Browser::platFormVersion();
        $browser = Browser::browserName();
        $ip = $this->get_ip();

        $csf = Thds::get_csf($id);

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_ip_address' => $ip,
        'log_user_agent' => $os,
        'log_browser' => $browser,
        'log_description' => "Graph : Thesis & Dissertation : Customer Service Feedback", 
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ );

        Logs::create($logs);

        return $csf;
    }
}
