<?php

namespace App\Http\Controllers;

use App\Charts\EjournalChart;
use Illuminate\Http\Request;
use App\Ejournal;
use App\Logs;
use Auth;

/**
 * Manages eJournal data
 */
class EjournalController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $count_journals = Ejournal::count_journals();
        $count_articles = Ejournal::count_articles();
        $count_downloads = Ejournal::count_downloads();
        $count_views = Ejournal::count_views();
        $count_cites = Ejournal::count_cites();
        $count_visitors = Ejournal::count_visitors();
        $years = Ejournal::get_visitors_year();
        
        return view('charts.ejournal', compact('count_journals', 'count_articles', 'count_downloads',
        'count_views', 'count_cites', 'count_visitors', 'journals', 'years'));
    }

    public function journals_by_year(){
        return Ejournal::get_journals_by_year();
    }

    public function articles_by_journal() {
       return Ejournal::get_articles_by_journal();
    }

    public function pdf_downloads_by_journal(){
        return Ejournal::get_pdf_downloads_by_journal();
    }

    public function abstract_views_by_journal(){
        return Ejournal::get_abstract_views_by_journal();
    }

    public function citations_by_journal(){
        return Ejournal::get_citations_by_journal();
    }

    public function visitors_by_year(Request $req){
        $year = ($req->year != '') ? $req->year : '';
        return Ejournal::get_visitors_by_year($year);
    }

    public function visitors_year(){
        return Ejournal::get_visitors_year();
    
    }

    /**
     * Get published articles
     */
    public function published_articles(){
        
        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_description' => 'EJOURNAL Published Articles', 
        'log_ip_address' => $this->get_ip(),
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ ,
        'log_model' => 'Ejournal::get_published_articles()');

        Logs::create($logs);

        return Ejournal::get_published_articles();
    }

    /**
     * Get cited articles
     *
     * @return void
     */
    public function cited_articles(){

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_description' => 'EJOURNAL Cited Articles', 
        'log_ip_address' => $this->get_ip(),
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ ,
        'log_model' => 'Ejournal::get_cited_articles()');

        Logs::create($logs);

        return Ejournal::get_cited_articles();
    }

    /**
     * Get viewed articles by their abstract by client
     *
     * @return void
     */
    public function viewed_articles(){

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_description' => 'EJOURNAL viewed Articles', 
        'log_ip_address' => $this->get_ip(),
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ ,
        'log_model' => 'Ejournal::get_viewed_articles()');

        Logs::create($logs);

        return Ejournal::get_viewed_articles();
    }

    /**
     * Get artciles with download full text pdf by client
     *
     * @return void
     */
    public function downloaded_articles(){

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_description' => 'EJOURNAL Downloaded Articles', 
        'log_ip_address' => $this->get_ip(),
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ ,
        'log_model' => 'Ejournal::get_downloaded_articles()');

        Logs::create($logs);

        return Ejournal::get_downloaded_articles();
    }

    /**
     * Get searched keywords saved in a text file
     *
     * @return void
     */
    public function searched_topics(){

        // $file = '../ejournal/assets/keywords.txt';
        $file = '/var/www/html/ejournal/assets/keywords.txt';
        
        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_description' => 'EJOURNAL Most Search Topics', 
        'log_ip_address' => $this->get_ip(),
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ ,
        'log_model' => $file);

        Logs::create($logs);

        // $file = '../ejournal/assets/keywords.txt';

        $file = fopen($file, "r");
        $keys = array();

        while (!feof($file)) {
        $keys[] = fgets($file);
        }
        fclose($file);

        $out = array();
        foreach($keys as $val)
        {
                $key = serialize($val);
                if (!isset($out[$key]))
                    $out[$key]=1;
                else
                    $out[$key]++;
        }
        arsort($out);

        $top10 = 0;

        $topics = array();
        $most = array();

        foreach($out as $val=>$count)
        {
            $top10++;
            if($top10 <= 10)
            {
                $item = unserialize($val);
                $get_key = substr($item, strpos($item, ">") + 1);
                $get_explode = explode("=>", $item,3);
                $filter = @$get_explode[0];
                $key = @$get_explode[1];
                $res = @$get_explode[2];
                $res = preg_replace('/\s/', '', $res);
                if($key != ''){
                    $most = ['topic' => rawurldecode($key),
                             'frequency' => $count];
                }
                    // array_push($topics, rawurldecode($key));
                    array_push($topics, $most);
            }
        }

        return $topics;

        // sort($topics);
        // $sorted_topics = array();
        // foreach($topics as $val){
        //     array_push($sorted_topics, $val);
        // }
        // return $sorted_topics;
    }

    /**
     * Get clients info who dowbloaded article full text pdf
     *
     * @return void
     */
    public function most_clients(){

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_description' => 'EJOURNAL Most Type of Clients', 
        'log_ip_address' => $this->get_ip(),
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ ,
        'log_model' => 'Ejournal::get_most_clients()');

        Logs::create($logs);

        return Ejournal::get_most_clients();
    }

    /**
     * Get client's location when they visited the website
     *
     * @return void
     */
    public function visitors_origin(){

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_description' => 'EJOURNAL Visitors Origin', 
        'log_ip_address' => $this->get_ip(),
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ ,
        'log_model' => 'Ejournal::get_visitors_origin()');

        Logs::create($logs);

        return Ejournal::get_visitors_origin();
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
