<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Nrcpnet;
use App\Logs;
use Auth;

/**
 * manages NRCPnet data
 */
class NrcpnetController extends Controller
{

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
     * Get plantilla personnels
     *
     * @param Request $req
     * @return void
     */
    public function get_plant(Request $req){

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_description' => 'NRCPnet Plantilla Personnel', 
        'log_ip_address' => $this->get_ip(),
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ ,
        'log_model' => 'Nrcpnet::get_plantillas()');

        Logs::create($logs);


        $keyword = ($req->data != null) ? $req->data : null;

        return Nrcpnet::get_plantillas($keyword);

    }

    /**
     * Get contractual personnels
     *
     * @param Request $req
     * @return void
     */
    public function get_cont(Request $req){

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_description' => 'NRCPnet Contractual Personnel', 
        'log_ip_address' => $this->get_ip(),
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ ,
        'log_model' => 'Nrcpnet::get_contractuals()');

        Logs::create($logs);

        $keyword = ($req->data != null) ? $req->data : null;

        return Nrcpnet::get_contractuals($keyword);

    }

    /**
     * Get job order personnels
     *
     * @param Request $req
     * @return void
     */
    public function get_jo(Request $req){

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_description' => 'NRCPnet Job Orders', 
        'log_ip_address' => $this->get_ip(),
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ ,
        'log_model' => 'Nrcpnet::get_jos()');

        Logs::create($logs);

        $keyword = ($req->data != null) ? $req->data : null;

        return Nrcpnet::get_jos($keyword);

    }

    /**
     * Get vacant positions
     *
     * @param Request $req
     * @return void
     */
    public function get_vac(Request $req){

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_description' => 'NRCPnet Vacant Position', 
        'log_ip_address' => $this->get_ip(),
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__);

        Logs::create($logs);

        $keyword = ($req->data != null) ? $req->data : null;

        // return Nrcpnet::get_vacants($keyword);
        return 'no data available';

    }

    /**
     * Get departments
     *
     * @return void
     */
    public function get_divs(){
        return Nrcpnet::get_plantilla_group();
    }
}
