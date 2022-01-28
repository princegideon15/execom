<?php

namespace App\Http\Controllers;
use App\Charts\MemisChart;
use Illuminate\Http\Request;
use App\Member;
use App\Logs;
use Auth;

include(app_path() . '\Colors\RandomColor.php'); // local
// include(app_path() . '/Colors/RandomColor.php'); // server
use \Colors\RandomColor;

class MemisController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

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

    public function basic_per_div(){
        $labels = array();
        $values = array();
        $titles = array();
        $division = Member::get_divisions();

        $randomColor = RandomColor::many(count($division), array(
            'luminosity' => 'dark',
            'hue' => 'red',
         ));

        foreach($division as $row){
            array_push($labels, "Division {$row->div_number}");
            array_push($values, $row->total);
            array_push($titles, "Division {$row->div_number} : {$row->div_name}");
        }

        $result = array_merge(['labels' => $labels], ['values' => $values], ['colors' => $randomColor], ['titles' => $titles]);

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_ip_address' => $this->get_ip(),
        'log_description' => "Graph : MEMIS : Members Per Division", 
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ );

        Logs::create($logs);

        return $result;
    }

    public function basic_per_reg(){
        $labels = array();
        $values = array();
        $titles = array();
    
        $region = Member::get_regions();

        $randomColor = RandomColor::many(count($region), array(
            'luminosity' => 'dark',
        'hue' => 'red'
        ));

         foreach($region as $row){
             array_push($labels, $row->region_name);
             array_push($values, $row->total);
             array_push($titles, $row->region_name);
         }


        $result = array_merge(['labels' => $labels], ['values' => $values], ['colors' => $randomColor], ['titles' => $titles]);

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_ip_address' => $this->get_ip(),
        'log_description' => "Graph : MEMIS : Members Per Region", 
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ );

        Logs::create($logs);

        return $result;
    }

    public function basic_per_cat(){
        $labels = array();
        $values = array();
        $titles = array();
    
        $category = Member::get_categories();
        
        $randomColor = RandomColor::many(count($category), array(
            'luminosity' => 'dark',
            'hue' => 'red'
            ));

        foreach($category as $row){
            array_push($labels, $row->membership_type_name);
            array_push($values, $row->total);
            array_push($titles, $row->membership_type_name);
        }

        $result = array_merge(['labels' => $labels], ['values' => $values], ['colors' => $randomColor], ['titles' => $titles]);

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_ip_address' => $this->get_ip(),
        'log_description' => "Graph : MEMIS : Members Per Category", 
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ );

        Logs::create($logs);

        return $result;
    }

    public function basic_per_stat(){
        $labels = array();
        $values = array();
        $titles = array();
    
        $status = Member::get_status();

        $randomColor = RandomColor::many(count($status), array(
            'luminosity' => 'dark',
            'hue' => 'red'
            ));

        foreach($status as $row){
            array_push($labels, $row->membership_status_name);
            array_push($values, $row->total);
            array_push($titles, $row->membership_status_name);
        }

        $result = array_merge(['labels' => $labels], ['values' => $values], ['colors' => $randomColor], ['titles' => $titles]);

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_ip_address' => $this->get_ip(),
        'log_description' => "Graph : MEMIS : Members Per Status", 
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ );

        Logs::create($logs);

        return $result;
    }

    public function basic_per_sex(){
        $labels = array();
        $values = array();
        $titles = array();
    
        $sex = Member::get_sex();

        foreach($sex as $row){
            array_push($labels, $row->sex);
            array_push($values, $row->total);
            array_push($titles, $row->sex);
        }

        $randomColor = RandomColor::many(count($sex), array(
            'luminosity' => 'dark',
            'hue' => 'red'
            ));

        $result = array_merge(['labels' => $labels], ['values' => $values], ['colors' => $randomColor], ['titles' => $titles]);

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_ip_address' => $this->get_ip(),
        'log_description' => "Graph : MEMIS : Members Per Sex", 
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ );

        Logs::create($logs);
        
        return $result;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {   

        $division_list = Member::get_divisions_list();
        $region_list = Member::get_regions_list();
        $category_list = Member::get_categories();
        $status_list = Member::get_status();
        $sex_list = Member::get_sex();
        $country_list = Member::get_countries();


        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_ip_address' => $this->get_ip(),
        'log_description' => "Graph : MEMIS Overall", 
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ );

        Logs::create($logs);
        

        return view('charts.memis', compact('id','division_list', 'region_list', 'category_list', 'status_list', 'sex_list', 'country_list'));
    }

    public function get_all_division(){

       return Member::get_divisions_list();

    }

    public function per_division(Request $req){

        $logs = array('log_user_id' => Auth::id(), 
                      'log_email' => Auth::user()->email, 
                      'log_description' => 'Members per Division',
                      'log_ip_address' => $this->get_ip(), 
                      'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ ,
                      'log_model' => 'Member::per_division()');

        Logs::create($logs);

        return Member::per_division($req->id);
    }

    public function per_region(Request $req){

        $logs = array('log_user_id' => Auth::id(), 
                      'log_email' => Auth::user()->email, 
                      'log_description' => 'Members per Region', 
                      'log_ip_address' => $this->get_ip(),
                      'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ ,
                      'log_model' => 'Member::per_region()');

        Logs::create($logs);

        return Member::per_region($req->id);
    }

    public function per_category(Request $req){

        $logs = array('log_user_id' => Auth::id(), 
                      'log_email' => Auth::user()->email, 
                      'log_description' => 'Members per Category', 
                      'log_ip_address' => $this->get_ip(),
                      'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ ,
                      'log_model' => 'Member::per_category()');

        Logs::create($logs);

        return Member::per_category($req->id);
    }

    public function per_status(Request $req){

        $logs = array('log_user_id' => Auth::id(), 
                      'log_email' => Auth::user()->email, 
                      'log_description' => 'Members per Status', 
                      'log_ip_address' => $this->get_ip(),
                      'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ ,
                      'log_model' => 'Member::per_status()');

        Logs::create($logs);

        return Member::per_status($req->id);
    }

    public function per_sex(Request $req){

        $logs = array('log_user_id' => Auth::id(), 
                      'log_email' => Auth::user()->email, 
                      'log_description' => 'Members per Sex', 
                      'log_ip_address' => $this->get_ip(),
                      'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ ,
                      'log_model' => 'Member::per_sex()');

        Logs::create($logs);

        return Member::per_sex($req->id);
    }

    public function all_members(Request $req){
        
        $logs = array('log_user_id' => Auth::id(), 
                      'log_email' => Auth::user()->email, 
                      'log_description' => 'All Members', 
                      'log_ip_address' => $this->get_ip(),
                      'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ ,
                      'log_model' => 'Member::get_all_members()');

        Logs::create($logs);

        return Member::get_all_members();
    }

    public function get_awards(Request $req){

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_description' => "Achievement Awards", 
        'log_ip_address' => $this->get_ip(),
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ ,
        'log_model' => 'Member::get_awards()');

        Logs::create($logs);
        

        return Member::get_awards();
    }

    public function get_gb(Request $req){

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_description' => "Governing Board", 
        'log_ip_address' => $this->get_ip(),
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ ,
        'log_model' => 'Member::get_gb()');

        Logs::create($logs);
        

        return Member::get_gb($req->id);
    }

    public function get_users(){
        return Member::get_users();
    }

    public function get_province(Request $req){
          
        $labels = array();
        $values = array();

        $province = Member::get_province($req->id);

        return $province;//continue on monday
    }

    public function get_discrepancies(){
        $members = Member::count_members();
        $abroad = Member::get_abroad();
        $no_region = Member::get_no_region();
        $no_stat = Member::get_no_status();

        $disc = array();
        $disc['ABROAD'] = $abroad;
        $disc['NO_REGION'] = $no_region;
        $disc['NO_STATUS'] = $no_stat;

        return $disc;
    }

    public function do_bar_graph_by_id(Request $req){

        $filters = array();
        foreach($req->all() as $key => $value) {
            if($key != 'radio_generate_chart' && $key != 'id' && $value > 0){
                array_push($filters, $value);
            }
        }
        $all = count($filters);
        return Member::do_bar_graph_by_id($req, $all, $req->id);
    }
    
    public function do_bar_graph(Request $req){

        $filters = array();
        foreach($req->all() as $key => $value) {
            if($key != 'radio_generate_chart' && $value > 0){
                array_push($filters, $value);
            }
        }
        $all = count($filters);
        return Member::do_bar_graph($req, $all);
    }
    
    
    public function do_stack_column_graph(Request $req){

        $filters = array();
        foreach($req->all() as $key => $value) {
            if($key != 'radio_generate_chart' && $value > 0){
                array_push($filters, $value);
            }
        }
        // $all = count($filters);
        // return Member::do_stack_graph($req, $all);
        return Member::do_stack_column_graph($req);
        
    }
    
    public function do_stack_graph(Request $req){

        $filters = array();
        foreach($req->all() as $key => $value) {
                array_push($filters, $value);
        }

        return Member::do_stack_graph($req);
        
    }
    
    public function do_column_graph(Request $req){

        $filters = array();
        foreach($req->all() as $key => $value) {
            if($key != 'radio_generate_chart' && $value > 0){
                array_push($filters, $value);
            }
        }
        // $all = count($filters);
        // return Member::do_stack_graph($req, $all);
        return Member::do_column_graph($req);
        
    }

    public function drilldown_region(Request $req){
        $filters = array();
        foreach($req->all() as $key => $value) {
            if($key != 'radio_generate_chart' && $value > 0){
                array_push($filters, $value);
            }
        }
        $all = count($filters);
        return Member::do_drilldown_region($req, $all);

        // return $req->par1;
    }

    public function get_csf(){

        $csf = Member::get_csf();

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_ip_address' => $this->get_ip(),
        'log_description' => "Graph : MEMIS : Customer Service Feedback", 
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ );

        Logs::create($logs);

        return $csf;
    }

    public function get_questions(){
        return Member::get_questions();
    }

    public function get_csf_list(){
        return Member::get_csf_list();
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
