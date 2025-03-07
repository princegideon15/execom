<?php

namespace App\Http\Controllers;

use App\Charts\BrisChart;
use Illuminate\Http\Request;
use App\Research;
use App\Logs;
use Auth;
use Browser;

// generate random color for basic bar graph only
// for local use
include(app_path() . '\Colors\RandomColor.php');

// generate random color for basic bar graph only
// for server use
// include(app_path() . '/Colors/RandomColor.php'); 
use \Colors\RandomColor;

/**
 * Manages BRIS data
 */
class BrisController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $type = Research::proj_type();
        $status = Research::proj_status();
        // $class = Research::classification();
        $prexc = Research::prexc();
        $snt = Research::snt();
        $hnrda = Research::hnrda();
        $nsea = Research::nsea();
        $dost = Research::dost_agenda();
        $pa = Research::priority_areas();
        $nibra = Research::nibra();
        $nsub = Research::nibra_sub();
        $strat = Research::strat();
        $sdg = Research::sdg();
        $pdp = Research::pdp();

        return view('charts.bris', compact('type', 'status', 'class', 'prexc',  
                                           'snt',' hnrda', 'nsea', 'dost', 'pa',
                                           'nibra', 'nsub', 'strat', 'sdg', 'pdp', 'hnrda'));
    }

    /**
     * Generate basic bar graph for Projects
     *
     * @return void
     */
    public function basic_per_proj(){
        
        $labels = array();
        $values = array();
        $titles = array();
        $projects = Research::get_projects_per_stat();

        $randomColor = RandomColor::many(count($projects), array(
            'hue' => 'red'
         ));

        foreach($projects as $row){
            array_push($labels, $row->prs_name);
            array_push($values, $row->total);
            array_push($titles, $row->prs_name);
        }

        $result = array_merge(['labels' => $labels], ['values' => $values], ['colors' => $randomColor], ['titles' => $titles]);

        $os = Browser::platformFamily() . ' ' . Browser::platFormVersion();
        $browser = Browser::browserName();
        $ip = $this->get_ip();

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_ip_address' => $ip,
        'log_user_agent' => $os,
        'log_browser' => $browser,
        'log_description' => "Graph : BRIS : Projects Per Status", 
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ );

        Logs::create($logs);

        return $result;
    }

    /**
     * Generate basci bar graph for NIBRA
     *
     * @return void
     */
    public function basic_per_nibr(){
        
        $labels = array();
        $values = array();
        $titles = array();
        $nibras = Research::get_nibras();

        $randomColor = RandomColor::many(count($nibras), array(
            'hue' => 'red'
         ));

        foreach($nibras as $row){
            array_push($labels, $row->nibra_name);
            array_push($values, $row->total);
            array_push($titles, $row->nibra_name);
        }

        $result = array_merge(['labels' => $labels], ['values' => $values], ['colors' => $randomColor], ['titles' => $titles]);

        $os = Browser::platformFamily() . ' ' . Browser::platFormVersion();
        $browser = Browser::browserName();
        $ip = $this->get_ip();

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_ip_address' => $ip,
        'log_user_agent' => $os,
        'log_browser' => $browser,
        'log_description' => "Graph : BRIS : Nibras", 
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ );

        Logs::create($logs);

        return $result;
    }

    /**
     * Generate basic bar graph for DOST 11-Point Agenda
     *
     * @return void
     */
    public function basic_per_prior(){
        
        $labels = array();
        $values = array();
        $titles = array();
        $prior = Research::get_dost_agendas();

        $randomColor = RandomColor::many(count($prior), array(
            'hue' => 'red'
         ));

        foreach($prior as $row){
            array_push($labels, $row->dost_agenda_code);
            array_push($values, $row->total);
            array_push($titles, $row->dost_agenda_code);
        }

        $result = array_merge(['labels' => $labels], ['values' => $values], ['colors' => $randomColor], ['titles' => $titles]);

        $os = Browser::platformFamily() . ' ' . Browser::platFormVersion();
        $browser = Browser::browserName();
        $ip = $this->get_ip();

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_ip_address' => $ip,
        'log_user_agent' => $os,
        'log_browser' => $browser,
        'log_description' => "Graph : BRIS : Dost Agendas", 
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ );

        Logs::create($logs);

        return $result;
    }

    /**
     * Generate basic bar graph for Programs
     *
     * @return void
     */
    public function basic_per_prog(){
        
        $labels = array();
        $values = array();
        $titles = array();
        $programs = Research::get_programs_per_stat();

        $randomColor = RandomColor::many(count($programs), array(
            'hue' => 'red'
         ));

        foreach($programs as $row){
            array_push($labels, $row->prs_name);
            array_push($values, $row->total);
            array_push($titles, $row->prs_name);
        }

        $result = array_merge(['labels' => $labels], ['values' => $values], ['colors' => $randomColor], ['titles' => $titles]);

        $os = Browser::platformFamily() . ' ' . Browser::platFormVersion();
        $browser = Browser::browserName();
        $ip = $this->get_ip();

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_ip_address' => $ip,
        'log_user_agent' => $os,
        'log_browser' => $browser,
        'log_description' => "Graph : BRIS : Programs Per Status", 
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ );

        Logs::create($logs);

        return $result;
    }

    /**
     * Get research types
     *
     * @return void
     */
    public function research_type(){
        return Research::get_pc();
    }

    /**
     * Get project status
     *
     * @return void
     */
    public function project_status(){
        return Research::get_ps();
    }

    /**
     * Get HNRDA
     *
     * @return void
     */
    public function hnrda(){
        return Research::get_hnrda();
    }

    /**
     * Get PREXC
     *
     * @return void
     */
    public function prexc(){
        return Research::get_prexc();
    }

    /**
     * Get Priority areas
     *
     * @return void
     */
    public function pag(){
        return Research::get_pag();
    }

    /**
     * Get DOST agendas
     *
     * @return void
     */
    public function dost(){
        return Research::get_dost();
    }

    /**
     * Get Strategic plan outcome
     *
     * @return void
     */
    public function strat(){
        return Research::get_strat();
    }

    /**
     * Get PDP
     *
     * @return void
     */
    public function pdp(){
        return Research::get_pdp();
    }

    /**
     * Get Nibra
     *
     * @return void
     */
    public function nibra(){
        return Research::get_nibra();
    }

    /**
     * Get NIBRA sub items
     *
     * @return void
     */
    public function nsub(){
        return Research::get_nsub();
    }

    /**
     * Get NSEA
     *
     * @return void
     */
    public function nsea(){
        return Research::get_nsea();
    }

    /**
     * Get Science and Technology
     *
     * @return void
     */
    public function snt(){
        return Research::get_snt(); 
    }

    /**
     * Get SDG
     *
     * @return void
     */
    public function sdg(){
        return Research::get_sdg();
    }

    /**
     * Get project status
     *
     * @param Request $req
     * @return void
     */
    public function get_project_status(Request $req){
        
        $os = Browser::platformFamily() . ' ' . Browser::platFormVersion();
        $browser = Browser::browserName();
        $ip = $this->get_ip();

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_description' => 'BRIS Projects', 
        'log_ip_address' => $ip,
        'log_user_agent' => $os,
        'log_browser' => $browser,
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ ,
        'log_model' => 'Research::get_project_status()');

        Logs::create($logs);

        return Research::get_project_status($req->status);
    }

    /**
     * Get NIBRA by id
     *
     * @param Request $req
     * @return void
     */
    public function get_nibra_by_id(Request $req){
        $os = Browser::platformFamily() . ' ' . Browser::platFormVersion();
        $browser = Browser::browserName();
        $ip = $this->get_ip();

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_description' => 'BRIS Nibra', 
        'log_ip_address' => $ip,
        'log_user_agent' => $os,
        'log_browser' => $browser,
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ ,
        'log_model' => 'Research::get_nibra_by_id()');

        Logs::create($logs);

        return Research::get_nibra_by_id($req->id);
    }

    /**
     * Get Dost agenda by id
     *
     * @param Request $req
     * @return void
     */
    public function get_dost_agenda_by_id(Request $req){

        $os = Browser::platformFamily() . ' ' . Browser::platFormVersion();
        $browser = Browser::browserName();
        $ip = $this->get_ip();

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_description' => 'BRIS Agendas',
        'log_ip_address' => $ip, 
        'log_user_agent' => $os, 
        'log_browser' => $browser, 
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ ,
        'log_model' => 'Research::get_dost_agenda_by_id()');

        Logs::create($logs);

        return Research::get_dost_agenda_by_id($req->id);
    }

    /**
     * Get program status
     *
     * @param Request $req
     * @return void
     */
    public function get_program_status(Request $req){

        $os = Browser::platformFamily() . ' ' . Browser::platFormVersion();
        $browser = Browser::browserName();
        $ip = $this->get_ip();

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_description' => 'BRIS Programs', 
        'log_ip_address' => $ip,
        'log_user_agent' => $os,
        'log_browser' => $browser,
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ ,
        'log_model' => 'Research::get_program_status()');

        Logs::create($logs);

        return Research::get_program_status($req->status);
    }

    /**
     * Get corrdinators
     *
     * @return void
     */
    public function get_coordinator(){
        return Research::get_coorindator();
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Get CSF from BRIS database
     *
     * @return void
     */
    public function get_csf_list(){
        return Research::get_csf_list();
    }

    public function get_csf_desc($id, $user){
        return Research::get_csf_desc($id, $user);
    }

    public function get_csf_answers($user){
        return Research::get_csf_answers($user);
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

        $csf = Research::get_csf($id);

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_ip_address' => $ip,
        'log_user_agent' => $os,
        'log_browser' => $browser,
        'log_description' => "Graph : BRIS : Customer Service Feedback", 
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ );

        Logs::create($logs);

        return $csf;
    }

}
