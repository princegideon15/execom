<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Mail\AccountCreated;
use Illuminate\Support\Facades\Mail;
 
use Hash;
use App\Ejournal;
use App\Library;
use App\Research;
use App\Member;
use App\Nrcpnet;
use App\Execom;
use App\Logs;
use App\Feedback;
use App\Thds;
use App\Grant;
use Cookie;
use Auth;
use App\Charts\FeedbackChart;
use Browser;

use \Colors\RandomColor;

/**
 * Manages displays in dashboard
 */
class HomeController extends Controller
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
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // ejournal
        $count_clients = Ejournal::count_clients();
        $count_citees = Ejournal::count_citees();
        $count_journals = Ejournal::count_journals();
        $count_articles = Ejournal::count_articles();
        $count_downloads = Ejournal::count_downloads();
        $count_views = Ejournal::count_views();
        $count_cites = Ejournal::count_cites();
        $count_visitors = Ejournal::count_visitors();

        // lms
        $lms_articles = Library::count_articles();
        $categories = Library::get_categories();

        // bris
        $bris_proj = Research::count_projects();
        $bris_basic = Research::count_research(1);
        $bris_applied = Research::count_research(2);
        $bris_prog = Research::count_programs();
        $nibras = Research::get_nibras();
        $dost_agendas = Research::get_dost_agendas();
        $ongoing = Research::count_status(2);
        $completed = Research::count_status(4);
        $terminated = Research::count_status(3);
        $extended = Research::count_status(6);

        // memis
        $members = Member::count_members();
        $division = Member::get_divisions();
        $region = Member::get_regions();
        $category = Member::get_categories();
        $status = Member::get_status();
        $sex = Member::get_sex();
        // $awards = Member:get_awards();
        // $gb = Member::count_gb();
        $position = Member::get_positions();


        // nrcpnet
        $count_plant = Nrcpnet::count_plantillas();
        $count_jo = Nrcpnet::count_jos();
        $count_cont = Nrcpnet::count_contractuals();
        $count_vac = 0; // Nrcpnet::count_vacant();

        //feedback
        $count_feedback = Feedback::where('fb_notif', 0)->count();
        $csf_desc = Feedback::get_csf_desc();

        //thesis and dissertation
        $count_ths = Thds::count_thd(1);
        $count_dis = Thds::count_thd(2);
        $count_app_thds = Thds::count_action_apps(2);
        $count_dis_thds = Thds::count_action_apps(3);

        // rdlip
        $count_paper = Grant::count_paper_grant();
        $count_pub = Grant::count_pub_grant();

        // csf
        $csf_memis = Member::get_csf_list();
        $csf_bris = Research::get_csf_list();
        $csf_ej = Ejournal::get_csf_list();
        $csf_lms = Library::get_csf_list();
        $csf_thds = Thds::get_csf_list();
        $csf_rdlip = Grant::get_csf_list();


        //tables
        $tables = Library::get_tables();
 
        return view('home', compact('count_journals', 'count_articles', 'count_downloads', 'count_citees',
                                    'count_views', 'count_cites', 'count_visitors',
                                    'lms_articles', 'categories', 'count_clients',
                                    'bris_proj', 'bris_basic', 'bris_applied', 'bris_prog', 'nibras', 
                                    'ongoing', 'completed', 'terminated', 'extended','dost_agendas',
                                    'members', 'division', 'region', 'category', 'status', 'sex', 'position',
                                    'count_plant', 'count_jo', 'count_cont', 'count_vac',
                                    'count_ths', 'count_dis', 'count_app_thds', 'count_dis_thds',
                                    'csf_memis', 'csf_bris', 'csf_ej', 'csf_lms', 'csf_thds', 'csf_rdlip',
                                    'count_feedback', 'tables', 'csf_desc',
                                    'count_paper', 'count_pub'));
    }

    /**
     * Get ExeCom users
     *
     * @return void
     */
    public function get_users($id = null){
        return Execom::get_users($id);
    }

    /**
     * Add execom user 
     *
     * @param Request $req
     * @return void
     */
    public function add_user(Request $req){

        $os = Browser::platformFamily() . ' ' . Browser::platFormVersion();
        $browser = Browser::browserName();
        $ip = $this->get_ip();

        $logs = array('log_user_id' => Auth::user()->user_id, 
        'log_email' =>  Auth::user()->email, 
        'log_description' => 'Added User',
        'log_ip_address' => $ip,
        'log_user_agent' => $os,
        'log_browser' => $browser,
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ );

        Logs::updateOrcreate($logs);
        
        // email nofitciation todo

        return Execom::add_user($req->id);
    }


    /**
     * update execom user 
     *
     * @param Request $req
     * @return void
     */
    public function update_user(Request $req){

        $os = Browser::platformFamily() . ' ' . Browser::platFormVersion();
        $browser = Browser::browserName();
        $ip = $this->get_ip();

        $logs = array('log_user_id' => Auth::user()->user_id, 
        'log_email' =>  Auth::user()->email, 
        'log_description' => 'Updated User',
        'log_ip_address' => $ip,
        'log_user_agent' => $os,
        'log_browser' => $browser,
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ );

        Logs::updateOrcreate($logs);

        $data = array();
        $where = array();

        $model = new Execom;
        $row = $model->getTableColumns('users');
        foreach($row as $field){
            if($field == 'password'){
                if($req->input('password') != null){
                    $data['password'] = Hash::make($req->password);
                }else{
                    $data[$field] = Execom::where('user_id', $req->input('user_id'))->value('password');
                }
            }else{
                $data[$field] = $req->input($field);
            }
            $where['user_id'] = $req->input('user_id');
        }

        Execom::updateOrCreate($where, $data);
    }

    /**
     * Remove user
     *
     * @param Request $req
     * @return void
     */
    public function remove_user(Request $req){
        
        $os = Browser::platformFamily() . ' ' . Browser::platFormVersion();
        $browser = Browser::browserName();
        $ip = $this->get_ip();

        $logs = array('log_user_id' => Auth::user()->user_id, 
        'log_email' =>  Auth::user()->email, 
        'log_description' => 'Removed User',
        'log_ip_address' => $ip, 
        'log_user_agent' => $os, 
        'log_browser' => $browser, 
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ );

        Logs::updateOrcreate($logs);

        return Execom::remove_user($req->id);
    }

    /**
     * Add ExeCom IS user
     *
     * @param Request $req
     * @return void
     */
    public function create_user(Request $req){

        $data = array();
        $data['email'] = $req->email;
        $data['password'] = Hash::make($req->password);
        $data['name'] = $req->name;
        $data['user_id'] = $randnum = rand(11111,99999);
        $data['role'] = $req->role;
        $data['status'] = 1;

        

        if (Execom::where('email', $req->email)->count() > 0) {
            return '1';
         }else{
            Execom::insert($data);            

            $os = Browser::platformFamily() . ' ' . Browser::platFormVersion();
            $browser = Browser::browserName();
            $ip = $this->get_ip();
            
            $logs = array('log_user_id' => Auth::user()->user_id,
            'log_email' =>  Auth::user()->email, 
            'log_description' => 'Created new user',
            'log_ip_address' => $ip,
            'log_user_agent' => $os,
            'log_browser' => $browser,
            'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ );

            Logs::updateOrcreate($logs);
            
            $email_data = [ 'name' => $req->name,
                            'email' => $req->email, 
                            'password' => $req->password];

            Mail::to($req->email)->send(new AccountCreated($email_data));

            return '2';
         }
    }

    public function send_email_test(){

        $email_data = [ 'name' => 'Gerard Balde',
        'email' => 'test@mail.com', 
        'password' => 'test pass'];
    
         Mail::to('gerardbalde15@gmail.com')->send(new AccountCreated($email_data));
    }
    
    public function activity_logs(){
        return Logs::orderBy('created_at', 'desc')->get();
    }

    public function save_logs(Request $req){

        $os = Browser::platformFamily() . ' ' . Browser::platFormVersion();
        $browser = Browser::browserName();
        $ip = $this->get_ip();

        $logs = array('log_user_id' => Auth::user()->user_id, 
        'log_email' =>  Auth::user()->email, 
        'log_description' => $req->log,
        'log_ip_address' => $ip,
        'log_user_agent' => $os,
        'log_browser' => $browser,
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ );

        Logs::updateOrcreate($logs);
    }

    /**
     * Get CSF from SKMS database
     *
     * @return void
     */
    public function get_csf_list(){
        $member = Member::get_csf_list();
        $research = Research::get_csf_list();
        $ejournal = Ejournal::get_csf_list();
        $library = Library::get_csf_list();
        $thds = Thds::get_csf_list();

        $result = array_merge(['memis' => $member], 
                              ['bris' => $research],
                              ['ej' => $ejournal],
                              ['lms' => $library],
                              ['thds' => $thds]);

        return $result;
    }
}
