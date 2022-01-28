<?php

namespace App\Http\Controllers;

use App\Charts\BrisChart;
use Illuminate\Http\Request;
use App\Research;
use App\Logs;
use Auth;

include(app_path() . '\Colors\RandomColor.php'); // local
// include(app_path() . '/Colors/RandomColor.php'); // server
use \Colors\RandomColor;

class BrisController extends Controller
{

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

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_ip_address' => $this->get_ip(),
        'log_description' => "Graph : BRIS : Projects Per Status", 
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ );

        Logs::create($logs);

        return $result;
    }

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

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_ip_address' => $this->get_ip(),
        'log_description' => "Graph : BRIS : Nibras", 
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ );

        Logs::create($logs);

        return $result;
    }

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

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_ip_address' => $this->get_ip(),
        'log_description' => "Graph : BRIS : Dost Agendas", 
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ );

        Logs::create($logs);

        return $result;
    }

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

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_ip_address' => $this->get_ip(),
        'log_description' => "Graph : BRIS : Programs Per Status", 
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ );

        Logs::create($logs);

        return $result;
    }

    public function research_type(){
        return Research::get_pc();
    }

    public function project_status(){
        return Research::get_ps();
    }

    public function hnrda(){
        return Research::get_hnrda();
    }

    public function prexc(){
        return Research::get_prexc();
    }

    public function pag(){
        return Research::get_pag();
    }

    public function dost(){
        return Research::get_dost();
    }

    public function strat(){
        return Research::get_strat();
    }

    public function pdp(){
        return Research::get_pdp();
    }

    public function nibra(){
        return Research::get_nibra();
    }

    public function nsub(){
        return Research::get_nsub();
    }

    public function nsea(){
        return Research::get_nsea();
    }

    public function snt(){
        return Research::get_snt(); 
    }

    public function sdg(){
        return Research::get_sdg();
    }

    public function get_project_status(Request $req){
        
        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_description' => 'BRIS Projects', 
        'log_ip_address' => $this->get_ip(),
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ ,
        'log_model' => 'Research::get_project_status()');

        Logs::create($logs);

        return Research::get_project_status($req->status);
    }

    public function get_nibra_by_id(Request $req){

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_description' => 'BRIS Nibra', 
        'log_ip_address' => $this->get_ip(),
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ ,
        'log_model' => 'Research::get_nibra_by_id()');

        Logs::create($logs);

        return Research::get_nibra_by_id($req->id);
    }

    public function get_dost_agenda_by_id(Request $req){

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_description' => 'BRIS Agendas',
        'log_ip_address' => $this->get_ip(), 
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ ,
        'log_model' => 'Research::get_dost_agenda_by_id()');

        Logs::create($logs);

        return Research::get_dost_agenda_by_id($req->id);
    }

    public function get_program_status(Request $req){

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_description' => 'BRIS Programs', 
        'log_ip_address' => $this->get_ip(),
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ ,
        'log_model' => 'Research::get_program_status()');

        Logs::create($logs);

        return Research::get_program_status($req->status);
    }

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
}
