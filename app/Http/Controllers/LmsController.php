<?php

namespace App\Http\Controllers;

use App\Charts\LmsChart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Library;
use App\Logs;
use Auth;
use Response;
use Storage;
use Browser;

/**
 * Manages LMS data
 */
class LmsController extends Controller
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
        $categories = Library::get_categories();

        $articles = Library::count_articles();
        $views = Library::count_views();
        $downloads = Library::count_downloads();
        $active_users = Library::count_active_users();
        // $visitors = Library::count_visictors();
        
        return view('charts.lms', compact('categories','articles', 'views', 'downloads', 'active_users'));
    }

    public function articles_by_categories()
    {
        return Library::get_categories();
    }

    public function articles_by_views()
    {
        return Library::get_views();
    }

    public function articles_by_downloads(){
        return Library::get_downloads();
    }

    public function categories(Request $req){

        $cat = Library::get_cat_label($req->cat);

        $os = Browser::platformFamily() . ' ' . Browser::platFormVersion();
        $browser = Browser::browserName();
        $ip = $this->get_ip();
        
        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_description' => "View {$cat}", 
        'log_ip_address' => $ip,
        'log_user_agent' => $os,
        'log_browser' => $browser,
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ ,
        'log_model' => 'Library::get_category()');

        Logs::create($logs);

        return Library::get_category($req->cat);
    }

    /**
     * Download PDF files if available
     *
     * @param [type] $id
     * @return void
     */
    public function download_file($id){

        $cat = Library::get_cat_label($id);

        // get file name and set source directory
        $file = Library::get_file($id);
        // $file_path = "../ejournal/assets/uploads/abstract/{$file}";
        $file_path = "/var/www/html/lms/assets/uploads/article_full_text/{$file}";

        //copy external fils to storage
        $contents = file_get_contents($file_path);
        $name = substr($file_path, strrpos($file_path, '/') + 1);
        Storage::put($name, $contents);

        $os = Browser::platformFamily() . ' ' . Browser::platFormVersion();
        $browser = Browser::browserName();
        $ip = $this->get_ip();

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_description' => "Download {$cat} ({$file})", 
        'log_ip_address' => $ip,
        'log_user_agent' => $os,
        'log_browser' => $browser,
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ ,
        'log_model' => 'Library::get_file()');

        Logs::create($logs);
        
        //download file
        return response()->download(storage_path("app/{$file}"))->deleteFileAfterSend(true);
    }

    /**
     * Get PDF
     *
     * @param [type] $id
     * @return void
     */
    public function view_pdf($id){

        $cat = Library::get_cat_label($id);

        // get file name and set source directory
        $file = Library::get_file($id);
        // $file_path = "../ejournal/assets/uploads/abstract/{$file}";
        $file_path = "/var/www/html/lms/assets/uploads/article_full_text/{$file}";

        //copy external fils to storage
        $contents = file_get_contents($file_path);
        $name = substr($file_path, strrpos($file_path, '/') + 1);
        Storage::put($name, $contents);

        $os = Browser::platformFamily() . ' ' . Browser::platFormVersion();
        $browser = Browser::browserName();
        $ip = $this->get_ip();

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_description' => "Viewed {$cat} ({$file})", 
        'log_ip_address' => $ip,
        'log_user_agent' => $os,
        'log_browser' => $browser,
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ ,
        'log_model' => 'Library::get_file()');

        Logs::create($logs);
        
        $path = storage_path("app/{$file}");

        return Response::make(file_get_contents($path), 200, [

            'Content-Type'
        => 'application/pdf',

        
        'Content-Disposition' => 'inline; filename="'.$file.'"'

        ]);
    }

    /**
     * Get CSF from BRIS database
     *
     * @return void
     */
    public function get_csf_list(){
        return Library::get_csf_list();
    }

    public function get_csf_desc($id, $user){
        return Library::get_csf_desc($id, $user);
    }

    public function get_csf_answers($user){
        return Library::get_csf_answers($user);
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

        $csf = Library::get_csf($id);

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_ip_address' => $ip,
        'log_user_agent' => $os,
        'log_browser' => $browser,
        'log_description' => "Graph : Library : Customer Service Feedback", 
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ );

        Logs::create($logs);

        return $csf;
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
