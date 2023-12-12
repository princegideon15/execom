<?php

namespace App\Http\Controllers;

use App\Feedback;
use App\Logs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use Browser;

/**
 * Manages feedbacks of ExeCom IS users
 */
class FeedbackController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    
    private $table = 'tblfeedbacks';
    private $ipaddress;

    /**
     * Get IP address of the user
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
     * Store UI/UX feedback of internal users
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = array();
        $where = array();

        $model = new Feedback;
        $row = $model->getTableColumns($this->table);
        foreach($row as $field){
            if($field == 'fb_usr_id'){
                $data[$field] = Auth::user()->user_id;
            }else{
                $data[$field] = $request->input($field);
            }

        }

        $os = Browser::platformFamily() . ' ' . Browser::platFormVersion();
        $browser = Browser::browserName();
        $ip = $this->get_ip();

        $logs = array('log_user_id' => Auth::user()->user_id, 
        'log_email' => Auth::user()->email, 
        'log_description' => 'Submit Feedback', 
        'log_ip_address' => $ip,
        'log_user_agent' => $os,
        'log_browser' => $browser,
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'::'. __FUNCTION__);

        Logs::create($logs);

        Feedback::updateOrCreate($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Feedback  $feedback
     * @return \Illuminate\Http\Response
     */
    public function show(Feedback $feedback)
    {
        return Feedback::get_ratings();
    }

    /**
     * Get Customer service feedback
     *
     * @return void
     */
    public function get_overall_csf_graph($id){
        
        $csf = Feedback::get_overall_csf_graph($id);
        return $csf;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Feedback  $feedback
     * @return \Illuminate\Http\Response
     */
    public function edit(Feedback $feedback)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Feedback  $feedback
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Feedback $feedback)
    {
       Feedback::where('fb_notif', 0)
          ->update(['fb_notif' => 1]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Feedback  $feedback
     * @return \Illuminate\Http\Response
     */
    public function destroy(Feedback $feedback)
    {
        //
    }

    public function verify(){
        $output = Feedback::where('fb_usr_id', Auth::user()->user_id)->first();
        if($output != ''){
            return 1;
        }else{
            return 0;
        }
    }

    public function all(){
        return Feedback::get_feedbacks();
    }
}
