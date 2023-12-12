<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Ejournal;
use App\Library;
use App\Search;
use App\Member;
use App\Nrcpnet;
use App\Research;
use App\Logs;
use Auth;
use Browser;

/**
 * Manage quick and advance search
 */
class SearchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private $ipaddress;

    /**
     * Get IP address of the users
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
     * Quick search in MemIS
     *
     * @param Request $req
     * @return void
     */
    public function search_overall_memis(Request $req){

        $data = $req->search;

        $keyword = $data['keyword'];
        $sys = $data['sys'];
        $section = $data['filter'];
        
        $os = Browser::platformFamily() . ' ' . Browser::platFormVersion();
        $browser = Browser::browserName();
        $ip = $this->get_ip();

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_description' => "Quick Search > {$keyword}", 
        'log_ip_address' => $ip,
        'log_user_agent' => $os,
        'log_browser' => $browser,
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ );

        Logs::updateOrcreate($logs);

            if($data['filter'] == 1){ // specialization

                return Member::get_specializations($keyword);

            }else if($data['filter'] == 2){// last name, first name

                if($keyword == ''){

                    $last = '';
                    $first = '';
                    $config = 'all';

                }else{
                    $name = explode(',', $keyword);
                    if(count($name) > 1){
                            
                        $first = str_replace(' ', '', $name[1]);
                        $last = str_replace(' ', '', $name[0]);
                        $config = 'and';
                    }else{
                        
                        $first = $keyword;
                        $last = $keyword;
                        $config = 'or';
                    }
                }

                return Member::get_specific_member($last, $first, $config);  
            }else if($data['filter'] == 3){ //all members
                
                if($keyword == ''){

                    $last = '';
                    $first = '';
                    $config = 'all';

                }else{
                    $name = explode(',', $keyword);
                    if(count($name) > 1){
                            
                        $first = str_replace(' ', '', $name[1]);
                        $last = str_replace(' ', '', $name[0]);
                        $config = 'and';
                    }else{
                        
                        $first = $keyword;
                        $last = $keyword;
                        $config = 'or';
                    }
                }
                
                return Member::get_all_members_per_loc($last, $first, null, null, null, null, $config);

            }else if($data['filter'] == 4){ //awards
                
                return Member::get_awards($keyword);
            }else if($data['filter'] == 5){ //gb
                return Member::search_gb($keyword);
            }
    }

    /**
     * Quick search in BRIS
     *
     * @param Request $req
     * @return void
     */
    public function search_overall_bris(Request $req){
        
        $data = $req->search;

        $keyword = $data['keyword'];
        $sys = $data['sys'];
        $section = $data['filter'];
        
        $os = Browser::platformFamily() . ' ' . Browser::platFormVersion();
        $browser = Browser::browserName();
        $ip = $this->get_ip();

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_description' => "Quick Search > {$keyword}", 
        'log_ip_address' => $ip,
        'log_user_agent' => $os,
        'log_browser' => $browser,
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ );

        Logs::updateOrcreate($logs);

        if($section == 1){ // projects{
            return Research::search_projects($keyword);
        }else if($section == 2){
            return Research::search_programs($keyword);
        }
        // else{
        //     return Research::search_proposals($keyword);
        // }
        
    }

    /**
     * Quick search in eJournal
     *
     * @param Request $req
     * @return void
     */
    public function search_overall_ejournal(Request $req){

        $data = $req->search;

        $keyword = $data['keyword'];
        $sys = $data['sys'];
        $section = $data['filter'];
        
        $os = Browser::platformFamily() . ' ' . Browser::platFormVersion();
        $browser = Browser::browserName();
        $ip = $this->get_ip();

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_description' => "Quick Search > {$keyword}", 
        'log_ip_address' => $ip,
        'log_user_agent' => $os,
        'log_browser' => $browser,
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ );

        Logs::updateOrcreate($logs);
        
        if($section == 1){ // title
            return Ejournal::search($keyword, 'art_title');
        }else{
            return Ejournal::search($keyword, 'art_author');
        }
    }

    /**
     * Quick search in LMS
     *
     * @param Request $req
     * @return void
     */
    public function search_overall_lms(Request $req){

        $data = $req->search;

        $keyword = $data['keyword'];
        $sys = $data['sys'];
        $section = $data['filter'];
        
        $os = Browser::platformFamily() . ' ' . Browser::platFormVersion();
        $browser = Browser::browserName();
        $ip = $this->get_ip();

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_description' => "Quick Search > {$keyword}", 
        'log_ip_address' => $ip,
        'log_user_agent' => $os,
        'log_browser' => $browser,
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ );

        Logs::updateOrcreate($logs);
        
        $results= array();
        $categories = Library::get_categories();
        foreach($categories as $row){
            $output = Library::search($keyword, $row->cat_id);
            array_push($results, $output);
        }

        return $results;
    
    }

    /**
     * Quick search in NRCPnet
     *
     * @param Request $req
     * @return void
     */
    public function search_overall_nrcpnet(Request $req){

        $data = $req->search;

        $keyword = $data['keyword'];
        $sys = $data['sys'];
        $section = $data['filter'];
        
        $os = Browser::platformFamily() . ' ' . Browser::platFormVersion();
        $browser = Browser::browserName();
        $ip = $this->get_ip();

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_description' => "Quick Search > {$keyword}", 
        'log_ip_address' => $ip,
        'log_user_agent' => $os,
        'log_browser' => $browser,
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ );

        Logs::updateOrcreate($logs);

        if($section == 1){ // employee
            return Nrcpnet::search($keyword, null , 'name');
        }else{
            return Nrcpnet::search($keyword, null , 'plantillaGroupName');
        }          
    }

    /**
     * Advance search
     *
     * @param Request $req
     * @return void
     */
    public function search(Request $req){
        $data = $req->search;

        $keyword = $data['keyword'];
        $search = $req->keyword;
        
        $os = Browser::platformFamily() . ' ' . Browser::platFormVersion();
        $browser = Browser::browserName();
        $ip = $this->get_ip();

        $logs = array('log_user_id' => Auth::id(), 
        'log_email' => Auth::user()->email, 
        'log_description' => "Advanced Search > {$search}", 
        'log_ip_address' => $ip,
        'log_user_agent' => $os,
        'log_browser' => $browser,
        'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'/'. __FUNCTION__ );

        Logs::updateOrcreate($logs);

        if($data['sys'] == 1){ // members

            if($data['filter'] == 1){ // specialization

                $region = ($data['region'] > 0) ? $data['region'] : null;
                $province = ($data['province'] > 0) ? $data['province'] : null;
                $city = ($data['city'] > 0) ? $data['city'] : null;
                $brgy = ($data['brgy'] != '') ? $data['brgy'] : null;
                $keyword = ($data['keyword'] != '') ? $data['keyword'] : null;

                return Member::get_specializations($keyword, $region, $province, $city, $brgy);

            }else if($data['filter'] == 2){// last name, first name

                if($keyword == ''){

                    $last = '';
                    $first = '';
                    $config = 'all';

                }else{
                    $name = explode(',', $keyword);
                    if(count($name) > 1){
                            
                        $first = str_replace(' ', '', $name[1]);
                        $last = str_replace(' ', '', $name[0]);
                        $config = 'and';
                    }else{
                        
                        $first = $keyword;
                        $last = $keyword;
                        $config = 'or';
                    }
                }

                return Member::get_specific_member($last, $first, $config); 
                
                
            }else if($data['filter'] == 3){// all members


                $region = ($data['region'] > 0) ? $data['region'] : null;
                $province = ($data['province'] > 0) ? $data['province'] : null;
                $city = ($data['city'] > 0) ? $data['city'] : null;
                $brgy = ($data['brgy'] != '') ? $data['brgy'] : null;
                
                if($keyword == ''){

                    $last = '';
                    $first = '';
                    $config = 'all';

                }else{
                    $name = explode(',', $keyword);
                    if(count($name) > 1){
                            
                        $first = str_replace(' ', '', $name[1]);
                        $last = str_replace(' ', '', $name[0]);
                        $config = 'and';
                    }else{
                        
                        $first = $keyword;
                        $last = $keyword;
                        $config = 'or';
                    }
                }
                
                return Member::get_all_members_per_loc($last, $first, $region, $province, $city, $brgy, $config);
            
            }else if($data['filter'] == 4){ // nrcp achievement awardee

                $division = ($data['division'] > 0) ? $data['division'] : null;
                $year = ($data['year'] > 0) ? $data['year'] : null;

                return Member::advanced_search_awardee($division, $year, $keyword);

            }else if($data['filter'] == 5){ // governing board
                $division = (isset($data['division'])) ? $data['division'] : 0;
                $year = (isset($data['year'])) ? $data['year'] : 0;
                return Member::advanced_search_gb($division, $year, $keyword);
            }
            else{

                $region = null;
                $province = null;
                $city = null;
                $brgy = null;
                $keyword = ($data['keyword'] != '') ? $data['keyword'] : null;
                $division = null;
                $year = null;


                $specializations = Member::get_specializations($keyword, $region, $province, $city, $brgy);

                
                if($keyword == ''){

                    $last = '';
                    $first = '';
                    $config = 'all';

                }else{
                    $name = explode(',', $keyword);
                    if(count($name) > 1){
                            
                        $first = str_replace(' ', '', $name[1]);
                        $last = str_replace(' ', '', $name[0]);
                        $config = 'and';
                    }else{
                        
                        $first = $keyword;
                        $last = $keyword;
                        $config = 'or';
                    }
                }

                $specific = Member::get_specific_member($last, $first, $config); 
                
                $members = Member::get_all_members_per_loc($last, $first, $region, $province, $city, $brgy, $config);
                
                $awardee = Member::advanced_search_awardee($division, $year, $keyword);

                $gb = Member::advanced_search_gb($division, $year, $keyword);

                $results = array();

                // array_push($results, 'all');
                array_push($results, $specializations);
                array_push($results, $specific);
                array_push($results, $members);
                array_push($results, $awardee);
                array_push($results, $gb);

                return $results;
            }

        }else if($data['sys'] == 2){ // bris

            if($data['filter'] == 1){ // projects{
                return Research::search_projects($keyword);
            }else if($data['filter'] == 2){
                return Research::search_programs($keyword);
            }else{
                $proj = Research::search_projects($keyword);
                $prog =  Research::search_programs($keyword);

                $results = array();

                array_push($results, 'all');
                array_push($results, $proj);
                array_push($results, $prog);

                return $results;
            }
            // else{
            //     return Research::search_proposals($keyword);
            // }

        }else if($data['sys'] == 3){ // ejournal
            
            if($data['filter'] == 1){ // title

                return Ejournal::search($keyword, 'art_title');

            }else if($data['filter'] == 2){ // author
                
                return Ejournal::search($keyword, 'art_author');

            }else{
           
                $title = Ejournal::search($keyword, 'art_title');
                $author =  Ejournal::search($keyword, 'art_author');

                $results = array();

                array_push($results, 'all');
                array_push($results, $title);
                array_push($results, $author);

                return $results;
            }
        }else if($data['sys'] == 4){ // lms
            
            $results = array();

            if($data['filter'] > 0){
                $cat = $data['filter'];
                return Library::search($keyword, $cat);
            }else{
               
                $categories = Library::get_categories();
                $cat_array = array();
                array_push($results, 'all');
                foreach($categories as $row){
                    $output = Library::search($keyword, $row->cat_id);
                    array_push($results, $output);
                    array_push($cat_array, $row->category);
                }
                array_push($results, $cat_array);
                return $results;
            }
        }else{

            if($data['filter'] == 1){ //employee name
                $code = null;
                return Nrcpnet::search($keyword, $code, 'name');

            }else if($data['filter'] == 2){ // division
                
                $code = ($data['nrcpnet_div'] != null) ? $data['nrcpnet_div'] : null;
                return Nrcpnet::search($keyword, $code, 'plantillaGroupCode');

            }else{
                $name = Nrcpnet::search($keyword, null , 'name');
                $div =  Nrcpnet::search($keyword, null , 'plantillaGroupName');

                $results = array();

                array_push($results, 'all');
                array_push($results, $name);
                array_push($results, $div);

                return $results;
            }
        }
    }

    public function get_region(){
        return Search::get_regions();
    }

    public function get_province(Request $req){
        return Search::get_provinces($req->id);
    }

    public function get_city(Request $req){
        return Search::get_cities($req->id);
    }

    public function get_divisions(){
        return Search::get_divisions();
    }
}




