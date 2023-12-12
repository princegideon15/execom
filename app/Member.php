<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Member extends Model
{
    
    static function count_members(){
         return DB::connection('dbskms')->table('tblpersonal_profiles')
        ->select('*')
        ->join('tblusers','usr_id','=','pp_usr_id', 'inner')
        ->join('tblmembers','mem_usr_id','=','pp_usr_id', 'left')
        ->where('usr_grp_id', '3')
        ->where('mem_status', '!=', '3')
        ->whereNotNull('mem_status')
        ->count();
    }

    static function get_users(){
        return DB::connection('dbskms')->table('tblusers')
        ->where('usr_status_id', '1')
        ->get();
    }
    
    static function get_divisions(){

        return DB::connection('dbskms')->table('tbldivisions')
        ->select('div_id','div_number','div_name', 
        DB::connection('dbskms')
        ->raw('(SELECT COUNT(*) FROM tblpersonal_profiles 
                                     INNER JOIN tblusers ON usr_id = pp_usr_id 
                                     LEFT JOIN tblmembers ON mem_usr_id = usr_id 
                                     WHERE usr_grp_id LIKE 3 
                                     AND mem_div_id LIKE div_id 
                                     AND mem_status != 3) AS total'))
        ->groupBy('div_id','div_number', 'div_name')
        ->get();    
    }

    static function get_divisions_list(){

        return DB::connection('dbskms')->table('tbldivisions')
        ->select('div_id','div_number','div_name')
        ->get();
    }
    
    static function get_province($id){

        return DB::connection('dbskms')->table('tblprovinces')
        ->select('province_id', 'province_region_id','province_name', 
        DB::connection('dbskms')
        ->raw('(SELECT COUNT(*) FROM tblpersonal_profiles
                                     LEFT JOIN tblresidence_address on pp_usr_id = adr_usr_id 
                                     LEFT JOIN tblmembers on pp_usr_id = mem_usr_id
                                     INNER JOIN tblusers on usr_id = mem_usr_id 
                                     WHERE usr_grp_id LIKE 3 
                                     AND adr_province LIKE province_id) AS total'))
        ->where('province_region_id', $id)
        ->groupBy('province_id', 'province_region_id','province_name')
        ->get();    
    }

    static function get_countries(){

        return DB::connection('dbskms')->table('tblcountries')
        ->select('country_id','country_name')
        ->get();

    }

    static function get_regions(){

        // SELECT emp_id, emp_usr_id, region_name FROM tblpersonal_profiles
        // inner join tblusers on usr_id = pp_usr_id
        // left join tblemployments on emp_usr_id = pp_usr_id
        // left join tblmembers on mem_usr_id = emp_usr_id
        // left join tblregions on region_id = emp_region
        // WHERE usr_grp_id = 3 and mem_status != 3 and mem_status is not null and emp_country = 175 and emp_region > 0 and emp_id IN 
        // (SELECT max(emp_id) as emp_id
        // FROM tblemployments 
        // GROUP by emp_usr_id 
        // ORDER by emp_id DESC);

        return DB::select('SELECT region_id, region_name, COUNT(emp_id) as total '.
        'FROM ( SELECT emp_usr_id, region_name, region_id, usr_id, emp_id '.
        'FROM new_dbskms.tblpersonal_profiles '.
        'INNER JOIN new_dbskms.tblusers ON usr_id = pp_usr_id '.
        'LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = pp_usr_id '.
        'LEFT JOIN new_dbskms.tblmembers ON mem_usr_id = emp_usr_id '.
        'LEFT JOIN new_dbskms.tblregions ON region_id = emp_region '.
        'WHERE usr_grp_id like 3 AND mem_status != 3 AND mem_status IS NOT NULL AND emp_country = 175 AND emp_region > 0 '.
        'AND emp_id IN (SELECT MAX(emp_id) AS emp_id
        FROM new_dbskms.tblemployments 
        GROUP by emp_usr_id 
        ORDER by emp_id DESC) '.
        ') as q '.
        'GROUP BY region_id');
    }

    static function get_regions_list(){

        return DB::connection('dbskms')->table('tblregions')
        ->select('region_id','region_name')
        ->get();

    }

    static function get_categories(){

        return DB::connection('dbskms')->table('tblmembership_types')
        ->select('membership_type_id','membership_type_name', 
        DB::connection('dbskms')
        ->raw('(SELECT COUNT(*) FROM tblpersonal_profiles
                                INNER JOIN tblusers ON usr_id = pp_usr_id 
                                LEFT JOIN tblmembers ON mem_usr_id = usr_id 
                                WHERE usr_grp_id like 3 
                                AND mem_type like membership_type_id 
                                AND mem_status != 3) AS total'))
        ->groupBy('membership_type_id','membership_type_name')
        ->get();

    }

    static function get_status(){

        return DB::connection('dbskms')->table('tblmembership_status')
        ->select('membership_status_id','membership_status_name', 
        DB::connection('dbskms')
        ->raw('(SELECT COUNT(*) FROM tblpersonal_profiles 
                                INNER JOIN tblusers ON usr_id = pp_usr_id 
                                LEFT JOIN tblmembers ON mem_usr_id = usr_id 
                                WHERE usr_grp_id like 3 AND mem_status LIKE membership_status_id) AS total'))
        ->groupBy('membership_status_id','membership_status_name')
        ->get();

    }

    static function get_sex(){

        return DB::connection('dbskms')->table('tblsex')
        ->select('s_id','sex', 
        DB::connection('dbskms')
        ->raw('(SELECT COUNT(*) FROM tblpersonal_profiles
                                INNER JOIN tblusers on usr_id = pp_usr_id 
                                LEFT JOIN tblmembers on mem_usr_id = usr_id 
                                WHERE usr_grp_id LIKE 3
                                AND pp_sex LIKE s_id AND mem_status != 3) as total'))
        ->groupBy('s_id','sex')
        ->get();

    }

    static function per_division($id){
       return DB::connection('dbskms')->table('tblpersonal_profiles')
        ->select('*'
        ,DB::connection('dbskms')->raw('(SELECT title_name FROM tbltitles WHERE title_id LIKE pp_title) AS TITLE'))
        ->join('tblusers', 'usr_id', '=', 'pp_usr_id', 'inner')
        ->join('tblmembers', 'mem_usr_id', '=', 'usr_id', 'left')
        ->join('tblsex', 's_id', '=', 'pp_sex', 'left')
        ->where('usr_grp_id', '3')
        ->where('mem_status', '!=', '3')
        ->where('mem_div_id', $id)
        ->orderBy('pp_last_name', 'asc')
        ->get();
    }

    static function per_region($id){

        $query = DB::select('SELECT *, TITLE, PROVINCE, CITY, sex '.
        'FROM ( SELECT new_dbskms.tblpersonal_profiles.*, title_name AS TITLE, province_name AS PROVINCE, city_name AS CITY, sex '.
        'FROM new_dbskms.tblpersonal_profiles '.
        'INNER JOIN new_dbskms.tblusers ON usr_id = pp_usr_id '.
        'LEFT JOIN new_dbskms.tblemployments as pp ON emp_usr_id = pp_usr_id '.
        'LEFT JOIN new_dbskms.tblmembers ON mem_usr_id = pp_usr_id '.
        'LEFT JOIN new_dbskms.tblsex ON s_id = pp_sex '.
        'LEFT JOIN new_dbskms.tbldivisions ON div_id = mem_div_id '.
        'LEFT JOIN new_dbskms.tbltitles ON title_id = pp_title '.
        'LEFT JOIN new_dbskms.tblprovinces ON province_id = emp_province '.
        'LEFT JOIN new_dbskms.tblcities ON city_id = emp_city '.
        'LEFT JOIN new_dbskms.tblregions ON region_id = emp_region '.
        'WHERE usr_grp_id like 3  AND mem_status != 3 AND mem_status IS NOT NULL AND emp_country = 175 '.
        'AND emp_region = '. $id . ' '.
        'AND emp_id IN (SELECT MAX(emp_id) AS emp_id '.
        'FROM new_dbskms.tblemployments '.
        'GROUP by emp_usr_id '.
        'ORDER by emp_id DESC) '.
        'GROUP BY emp_usr_id '.
        ') as q');  

        return $query; 
    }

    static function per_category($id){
        return DB::connection('dbskms')->table('tblpersonal_profiles')
        ->select('*'
        ,DB::connection('dbskms')->raw('(select title_name from tbltitles where title_id like pp_title) as TITLE'))
        ->join('tblmembers', 'pp_usr_id', '=', 'mem_usr_id', 'left')
        ->join('tblusers', 'usr_id', '=', 'pp_usr_id', 'inner')
        ->join('tblsex', 's_id', '=', 'pp_sex', 'left')
        ->where('usr_grp_id', '3')
        ->where('mem_status', '!=', '3')
        ->where('mem_type', $id)
        ->orderBy('pp_last_name', 'asc')
        ->get();
    }

    static function per_status($id){
        return DB::connection('dbskms')->table('tblpersonal_profiles')
        ->select('*'
        ,DB::connection('dbskms')->raw('(select title_name from tbltitles where title_id like pp_title) as TITLE'))
        ->join('tblmembers', 'pp_usr_id', '=', 'mem_usr_id', 'left')
        ->join('tblusers', 'usr_id', '=', 'pp_usr_id', 'inner')
        ->join('tblsex', 's_id', '=', 'pp_sex', 'left')
        ->where('usr_grp_id', '3')
        ->where('mem_status', $id)
        ->orderBy('pp_last_name', 'asc')
        ->get();
    }


    static function per_sex($id){
        return DB::connection('dbskms')->table('tblpersonal_profiles')
        ->select('*'
        ,DB::connection('dbskms')->raw('(select title_name from tbltitles where title_id like pp_title) as TITLE'))
        ->join('tblmembers', 'pp_usr_id', '=', 'mem_usr_id', 'left')
        ->join('tblusers', 'usr_id', '=', 'pp_usr_id', 'inner')
        ->join('tblsex', 's_id', '=', 'pp_sex', 'left')
        ->where('usr_grp_id', '3')
        ->where('mem_status', '!=', '3')
        ->where('pp_sex', $id)
        ->orderBy('pp_last_name', 'asc')
        ->get();
    }

    static function get_awards($keyword = null){

        $query = DB::connection('dbskms')->table('tblpersonal_profiles')
        ->select('pp_last_name', 'pp_first_name', 'pp_middle_name', 'awa_citation', 'div_id', 'div_number', 'awa_year', 'awa_title', 'awa_giving_body', 'sex'
        ,DB::connection('dbskms')->raw('(select title_name from tbltitles where title_id like pp_title) as TITLE'))
        ->join('tblusers', 'usr_id', '=', 'pp_usr_id', 'inner')
        ->join('tblmembers', 'mem_usr_id', '=', 'usr_id', 'left')
        ->join('tblsex', 's_id', '=', 'pp_sex', 'left')
        ->join('tblawards', 'mem_usr_id', '=', 'awa_usr_id', 'left')
        ->join('tbldivisions', 'div_id', '=', 'mem_div_id', 'left')
        ->where('usr_grp_id', '3');

        if($keyword != null){
            $query->where('awa_citation', 'LIKE', '%' . $keyword .'%')
            ->orWhere('pp_last_name', 'LIKE', '%' . $keyword .'%');
        }else{
            $query->where('awa_title', 'LIKE', '%nrcp%')
            ->orWhere('awa_giving_body', 'LIKE', '%nrcp%')
            ->orWhere('awa_giving_body', 'LIKE', '%eusebio%')
            ->orWhere('awa_giving_body', 'LIKE', '%eusebio%');
        }
        
        $query->orderBy('pp_last_name', 'asc')
        ->orderBy('awa_year', 'desc');

        return $query->get();

    }

    static function advanced_search_awardee($division, $year, $keyword){

        $query = DB::connection('dbskms')->table('tblpersonal_profiles')
        ->select('pp_last_name', 'pp_first_name', 'pp_middle_name', 'awa_citation', 'div_number', 'awa_year', 'sex'
        ,DB::connection('dbskms')->raw('(select title_name from tbltitles where title_id like pp_title) as TITLE'))
        ->join('tblmembers', 'pp_usr_id', '=', 'mem_usr_id', 'left')
        ->join('tblmembership_profiles','mpr_usr_id','=','mem_usr_id', 'left')
        ->join('tblusers', 'usr_id', '=', 'pp_usr_id', 'inner')
        ->join('tblawards', 'mpr_usr_id', '=', 'awa_usr_id')
        ->join('tblsex', 's_id', '=', 'pp_sex', 'left')
        ->join('tbldivisions', 'div_id', '=', 'mpr_div_id')
        ->where('usr_grp_id', '3')
        ->where('mem_status', '!=', '3')
        ->Where('pp_last_name',  'LIKE', '%' . $keyword . '%');

        if($division != null){
            $query->where('div_id', $division)
            ->orderBy('div_id', 'asc');
        }

        if($year != null){
            $query->where('awa_year', $year)
            ->orderBy('awa_year', 'desc');
        }
        
        $query->orderBy('pp_last_name', 'asc');

        return $query->get();
    }

    static function search_gb($keyword){

        // return DB::connection('dbskms')->table('tblpersonal_profiles')
        // ->select('*'
        // ,DB::connection('dbskms')->raw('(select title_name from tbltitles where title_id like pp_title) as TITLE')advanced_search_gb
        // ,DB::connection('dbskms')->raw('(select div_number from tbldivisions where div_id like division_id) as div_number'))
        // ->join('tblposition_held_nrcp','pp_usr_id','=','ph_usr_id')
        // ->join('tblnrcp_positions', 'ph_pos', '=', 'pos_id')
        // ->join('tblsex', 's_id', '=', 'pp_sex', 'left')
        // ->where('division_id', '>', '0')
        // ->orderBy('division_id', 'asc')

        $query = DB::connection('dbskms')->table('tblpersonal_profiles')
            ->select('*'
            ,DB::connection('dbskms')->raw('(SELECT title_name FROM tbltitles WHERE title_id LIKE pp_title) AS TITLE')
            ,DB::connection('dbskms')->raw('(SELECT div_number FROM tbldivisions WHERE div_id LIKE pos_div_id) AS div_number'))
            ->join('tblposition_held_nrcp','pp_usr_id','=','ph_usr_id', 'left')
            ->join('tblnrcp_positions', 'ph_pos', '=', 'pos_id')
            ->join('tblsex','s_id','=','pp_sex');

        if($keyword != null){
            $query->where('pp_last_name', 'LIKE', '%' . $keyword .'%')
            ->orWhere('pp_last_name', 'LIKE', '%' . $keyword . '%');
        }
        
        $query->orderBy('pp_last_name', 'asc');

        return $query->get();
    }

    static function advanced_search_gb($division, $year, $keyword){
        $query = DB::connection('dbskms')->table('tblpersonal_profiles')
            ->select('*'
            ,DB::connection('dbskms')->raw('(select title_name from tbltitles where title_id like pp_title) as TITLE')
            ,DB::connection('dbskms')->raw('(select div_number from tbldivisions where div_id like pos_div_id) as div_number'))
            ->join('tblposition_held_nrcp','ph_usr_id','=','pp_usr_id', 'left')
            ->join('tblnrcp_positions', 'ph_pos', '=', 'pos_id')
            ->join('tblsex', 's_id', '=', 'pp_sex')
            ->where('pp_last_name', 'LIKE', '%' . $keyword .'%')
            ->orWhere('pp_last_name', 'LIKE', '%' . $keyword . '%');

        if($division > 0){
            $query->where('division_id', $division);
        }

        if($year > 0){
            $query->where('ph_from', 'LIKE', '%' . $year .'%')
            ->orWhere('ph_to', 'Like', '%' . $year . '%');
        }
      
        $query->orderBy('pp_last_name', 'asc');

        return $query->get();
        
    }

    static function get_positions(){

        return DB::connection('dbskms')->table('tblnrcp_positions')
        ->select('*',
        DB::connection('dbskms')->raw('(select count(*) from tblposition_held_nrcp where ph_pos like pos_id) as total'))
        ->get();
    }

    static function get_gb($id){

        if($id == 0){
            return DB::connection('dbskms')->table('tblpersonal_profiles')
            ->select('*'
            ,DB::connection('dbskms')->raw('(select title_name from tbltitles where title_id like pp_title) as TITLE')
            ,DB::connection('dbskms')->raw('(select div_number from tbldivisions where div_id like division_id) as div_number'))
            ->join('tblposition_held_nrcp','pp_usr_id','=','ph_usr_id')
            ->join('tblnrcp_positions', 'ph_pos', '=', 'pos_id')
            ->join('tblsex', 's_id', '=', 'pp_sex', 'left')
            ->where('division_id', '>', '0')
            ->orderBy('division_id', 'asc')
            // ->orderBy('pp_last_name', 'asc')
            ->get();
        }else{
            return DB::connection('dbskms')->table('tblpersonal_profiles')
            ->select('*'
            ,DB::connection('dbskms')->raw('(select title_name from tbltitles where title_id like pp_title) as TITLE'))
            ->join('tblposition_held_nrcp','pp_usr_id','=','ph_usr_id')
            ->join('tblsex', 's_id', '=', 'pp_sex', 'left')
            ->where('ph_pos', $id)
            // ->orderBy('pp_last_name', 'asc')
            ->get();
        }

        
    }

    static function get_specializations($keyword = null, $region = null, $province = null, $city = null, $brgy = null){

        $where = '';

        if($keyword != null){
            $where .= 'AND mpr_gen_specialization LIKE "%'. $keyword . '%" ';
            // $query->where('mpr_gen_specialization', 'LIKE','%'. $keyword .'%');
        }

        if($region != null){
            $where .= 'AND adr_region LIKE "%'. $region . '%" ';
            // $query->where('adr_region', $region);
        }
        
        if($province != null){
            $where .= 'AND adr_province LIKE "%'. $province . '%" ';
            // $query->where('adr_province', $province);
        }
        
        if($city!= null){
            $where .= 'AND adr_city LIKE "%'. $adr_city . '%" ';
            // $query->where('adr_city', $adr_city);
        }
        
        if($brgy != null){
            $where .= 'AND adr_brgy LIKE "%'. $brgy . '%" ';
            // $query->where('adr_brgy', 'LIKE', '%' . $brgy . '%');
        }
        
        return DB::select('SELECT * '.
        'FROM ( SELECT new_dbskms.tblpersonal_profiles.*, div_number, mpr_gen_specialization, title_name AS TITLE, region_name AS REGION, province_name AS PROVINCE, city_name AS CITY, sex '.
        'FROM new_dbskms.tblpersonal_profiles '.
        'INNER JOIN new_dbskms.tblusers ON usr_id = pp_usr_id '.
        'LEFT JOIN new_dbskms.tblemployments as pp ON emp_usr_id = pp_usr_id '.
        'LEFT JOIN new_dbskms.tblmembers ON mem_usr_id = pp_usr_id '.
        'LEFT JOIN new_dbskms.tblmembership_profiles ON mpr_usr_id = pp_usr_id '.
        'LEFT JOIN new_dbskms.tblresidence_address ON mpr_usr_id = pp_usr_id '.
        'LEFT JOIN new_dbskms.tblsex ON s_id = pp_sex '.
        'LEFT JOIN new_dbskms.tbldivisions ON div_id = mem_div_id '.
        'LEFT JOIN new_dbskms.tbltitles ON title_id = pp_title '.
        'LEFT JOIN new_dbskms.tblprovinces ON province_id = emp_province '.
        'LEFT JOIN new_dbskms.tblcities ON city_id = emp_city '.
        'LEFT JOIN new_dbskms.tblregions ON region_id = emp_region '.
        'WHERE usr_grp_id like 3  AND mem_status != 3 AND mem_status IS NOT NULL AND emp_country = 175 '. $where .
        'AND emp_id IN (SELECT MAX(emp_id) AS emp_id '. 
        'FROM new_dbskms.tblemployments '.
        'GROUP by emp_usr_id '.
        'ORDER by emp_id DESC) '.
        'GROUP BY emp_usr_id '.
        ') as q');  

        // $query = DB::connection('dbskms')->table('tblpersonal_profiles')
        //     ->select('*'
        //     ,DB::connection('dbskms')->raw('(SELECT region_name FROM tblregions WHERE region_id LIKE adr_region) AS REGION')
        //     ,DB::connection('dbskms')->raw('(SELECT province_name FROM tblprovinces WHERE province_id LIKE adr_province) AS PROVINCE')
        //     ,DB::connection('dbskms')->raw('(SELECT city_name FROM tblcities WHERE city_id LIKE adr_city) AS CITY')
        //     ,DB::connection('dbskms')->raw('(SELECT title_name FROM tbltitles WHERE title_id LIKE pp_title) AS TITLE'))
        //     ->join('tblusers', 'usr_id', '=', 'pp_usr_id', 'inner')
        //     ->join('tblmembers', 'mem_usr_id', '=', 'usr_id', 'left')
        //     ->join('tbldivisions', 'div_id', '=', 'mem_div_id')
        //     ->join('tblmembership_profiles','mpr_usr_id','=','mem_usr_id', 'left')
        //     ->join('tblsex','s_id','=','pp_sex', 'left')
        //     ->join('tblresidence_address','mpr_usr_id','=','adr_usr_id', 'left')
        //     ->where('usr_grp_id', '3')
        //     ->where('mem_status', '!=', '3')
        //     ->orderBy('pp_last_name', 'asc');

            

            // return $query->get();
    }

    static function get_specific_member($last, $first, $config = null){
        
        $query = DB::connection('dbskms')->table('tblpersonal_profiles')
        ->select('*'
        ,DB::connection('dbskms')->raw('(SELECT title_name FROM tbltitles WHERE title_id LIKE pp_title) AS TITLE'))
        ->join('tblusers', 'usr_id', '=', 'pp_usr_id', 'inner')
        ->join('tblmembers', 'mem_usr_id', '=', 'usr_id', 'left')
        ->join('tbldivisions', 'div_id', '=', 'mem_div_id')
        ->join('tblmembership_profiles','mpr_usr_id','=','mem_usr_id', 'left')
        ->join('tblsex', 's_id', '=', 'pp_sex', 'left')
        ->where('usr_grp_id', '3')
        ->where('mem_status', '!=', '3');

        if($config == 'or'){

            $query->where('pp_last_name', 'LIKE','%'. $last .'%')
            ->orwhere('pp_first_name', 'LIKE','%'. $first .'%');

        }else if($config == 'and'){
            
            $query->where('pp_last_name', 'LIKE','%'. $last .'%')
            ->Where('pp_first_name', 'LIKE','%'. $first .'%');
        }
        else{
            return $query->get(); exit;
        }

        return $query->get();
    }
    
    static function get_all_members(){

        return DB::select('SELECT * '.
        'FROM ( SELECT new_dbskms.tblpersonal_profiles.*, div_number, title_name AS TITLE, region_name AS REGION, province_name AS PROVINCE, city_name AS CITY, sex '.
        'FROM new_dbskms.tblpersonal_profiles '.
        'INNER JOIN new_dbskms.tblusers ON usr_id = pp_usr_id '.
        'LEFT JOIN new_dbskms.tblemployments as pp ON emp_usr_id = pp_usr_id '.
        'LEFT JOIN new_dbskms.tblmembers ON mem_usr_id = pp_usr_id '.
        'LEFT JOIN new_dbskms.tblmembership_profiles ON mpr_usr_id = pp_usr_id '.
        'LEFT JOIN new_dbskms.tblresidence_address ON mpr_usr_id = pp_usr_id '.
        'LEFT JOIN new_dbskms.tblsex ON s_id = pp_sex '.
        'LEFT JOIN new_dbskms.tbldivisions ON div_id = mem_div_id '.
        'LEFT JOIN new_dbskms.tbltitles ON title_id = pp_title '.
        'LEFT JOIN new_dbskms.tblprovinces ON province_id = emp_province '.
        'LEFT JOIN new_dbskms.tblcities ON city_id = emp_city '.
        'LEFT JOIN new_dbskms.tblregions ON region_id = emp_region '.
        'WHERE usr_grp_id like 3  AND mem_status != 3 AND mem_status IS NOT NULL AND emp_country = 175 '. $where .
        'AND emp_id IN (SELECT MAX(emp_id) AS emp_id '. 
        'FROM new_dbskms.tblemployments '.
        'GROUP by emp_usr_id '.
        'ORDER by emp_id DESC) '.
        'GROUP BY emp_usr_id '.
        ') as q');  
    }

    static function get_all_members_per_loc($last = null, $first = null, $region = null, $province = null, $city = null, $brgy = null, $config = null){

        $query = DB::connection('dbskms')->table('tblpersonal_profiles')
            ->select('*'
            ,DB::connection('dbskms')->raw('(select region_name from tblregions where region_id like adr_region) as REGION')
            ,DB::connection('dbskms')->raw('(select province_name from tblprovinces where province_id like adr_province) as PROVINCE')
            ,DB::connection('dbskms')->raw('(select city_name from tblcities where city_id like adr_city) as CITY')
            ,DB::connection('dbskms')->raw('(select title_name from tbltitles where title_id like pp_title) as TITLE'))
            ->join('tblusers', 'usr_id', '=', 'pp_usr_id', 'inner')
            ->join('tblmembers', 'mem_usr_id', '=', 'usr_id', 'left')
            ->join('tbldivisions', 'div_id', '=', 'mem_div_id')
            ->join('tblmembership_profiles','mpr_usr_id','=','mem_usr_id', 'left')
            ->join('tblsex', 's_id', '=', 'pp_sex', 'left')
            ->join('tblresidence_address','mpr_usr_id','=','adr_usr_id', 'left')
            ->where('usr_grp_id', '3')
            ->where('mem_status', '!=', '3')
            ->orderBy('pp_last_name', 'asc');

            if($config == 'or'){

                $query->where('pp_last_name', 'LIKE','%'. $last .'%')
                ->orwhere('pp_first_name', 'LIKE','%'. $first .'%');
    
            }
            
            if($config == 'and'){
                
                $query->where('pp_last_name', 'LIKE','%'. $last .'%')
                ->Where('pp_first_name', 'LIKE','%'. $first .'%');
            }

            if($region != null){
                $query->where('adr_region', $region);
            }
            
            if($province != null){
                $query->where('adr_province', $province);
            }
            
            if($city!= null){
                $query->where('adr_city', $city);
            }
            
            if($brgy != null){
                $query->where('adr_brgy', 'LIKE', '%' . $brgy . '%');
            }

            return $query->get();

    }
    // bar/pie graph
    static function do_bar_graph_by_id($req, $all, $id){

        $query = DB::connection('dbskms');
        $select = '';
        $where = '';
        $join = '';
        $group_by = '';
        $order_by = '';
        $having = '';
        $deceased = ' AND mem_status != 3 ';
        $key = '';
        $field = '';
        $sub_q = '';


        if($req->radio_default == 'memis_division'){ // division default
            if($req->memis_division == '999'){      
                

                if($req->memis_region > 0 && $req->memis_region != '999'){
   
                    if($req->memis_province > 0 && $req->memis_province != '999'){
                        
                        if($req->memis_city > 0 && $req->memis_city != '999'){
                            $where .= ' AND emp_city LIKE '. $req->memis_city .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';
                        }else{
                            $where .= ' AND emp_province LIKE '. $req->memis_province .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';
                        }
                    }
                    else{
                        $where .= ' AND emp_region LIKE '. $req->memis_region .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';    
                    }

                }

                if($req->memis_country > 0 && $req->memis_country != '999'){

                    $where .= ' AND emp_country LIKE '. $req->memis_country .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';    
                
                }
                
                if($req->memis_sex > 0){ // sex

                    if($req->memis_sex > 0 && $req->memis_sex != '999'){
                        $where .= ' AND pp_sex LIKE "' . $req->memis_sex . '" ';
                    }else{
                        $where .= ' AND pp_sex > 0 ';
                    }
                }

                if($req->memis_category > 0){ // category


                    $join .= ' JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';
    
                    if($req->memis_category > 0 && $req->memis_category != '999'){
                        $where .= ' AND membership_type_id LIKE "' . $req->memis_category . '" ';
                    }else{
                        $where .= ' AND membership_type_id > 0 ';
                    }

                }

                if($req->memis_status > 0){ // status
                  
                    $join .= '  JOIN new_dbskms.tblmembership_status ON mem_status = membership_status_id ';
    
                    if($req->memis_status > 0 && $req->memis_status != '999'){
                        $where .= ' AND membership_status_id LIKE ' . $req->memis_status . ' ';
                    }else{
                        $where .= ' AND membership_status_id > 0 ';
                    }
                    
                    $deceased = '';
                }

                if($req->memis_educ > 0){ // educ 

                    $key = ($having != '') ? 'AND' : ' HAVING ';
                    $field .= ' , MIN(adp_highest) as minadp ';
                    $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
                    $having .= ' '.$key.' minadp = ' . $req->memis_educ . ' ';

                }

                if($req->memis_age > 0){ // age 

                        $key = ($having != '') ? 'AND' : 'HAVING';
                        $field .= ' ,(YEAR(CURDATE()) - YEAR(pp_date_of_birth)) AS age ';
                        $where .= ' AND pp_date_of_birth like \'%-%\' and pp_date_of_birth > 0 ';
                        
                        if($req->memis_age == 1){
                            $having .= ' '.$key.' age > 20 AND age < 31 ';
                        }else if($req->memis_age == 2){
                            $having .= ' '.$key.' age > 30 AND age < 41 ';
                        }else if($req->memis_age == 3){
                            $having .= ' '.$key.' age > 40 AND age < 51 ';
                        }else if($req->memis_age == 4){
                            $having .= ' '.$key.' age > 50 AND age < 61 ';
                        }else if($req->memis_age == 5){
                            $having .= ' '.$key.' age > 60 AND age < 71 ';
                        }else if($req->memis_age == 6){
                            $having .= ' '.$key.' age > 70 ';
                        }else{
                            $having .= ' '.$key.' age > 0 ';
                        }
                }
           
                if($req->memis_start_year > 0 && $req->memis_end_year){
                    
                    $key = ($having != '') ? 'AND' : ' HAVING ';
                    $field .= ' ,YEAR(mem_date_elected) AS year ';
                    $having .= ' '.$key. ' year BETWEEN '. $req->memis_start_year .' AND '. $req->memis_end_year .' '; 
                }

                $group_by .= ' GROUP BY pp_usr_id ';
                $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
                $join .= ' JOIN new_dbskms.tbldivisions on div_id = mem_div_id ';
                $join .= ' LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';
                $join .= ' LEFT JOIN new_dbskms.tbltitles ON title_id = pp_title ';
                $join .= ' LEFT JOIN new_dbskms.tblsex ON s_id = pp_sex ';
                                
                
                $where .= ' AND div_id LIKE '. $id;

                $select .= 'SELECT pp_first_name, pp_last_name, pp_middle_name, pp_sex, pp_title, pp_email, pp_contact,  title_name, sex '. $field .
                'FROM new_dbskms.tblmembers '. 
                'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
                'WHERE usr_grp_id LIKE 3 '. $where . $deceased . $group_by . $having;

                

                return DB::select($select);
             
            }
    }else if($req->radio_default == 'memis_region'){ // region default

        if($req->memis_region == '999' && $all > 1){ 
            
            if($req->memis_division > 0){ // division

                $join .= ' JOIN new_dbskms.tbldivisions ON div_id = mem_div_id ';
    
                if($req->memis_division > 0 && $req->memis_division != '999'){
                    $where .= ' AND mem_div_id LIKE "' . $req->memis_division . '" ';
                }else{
                    $where .= ' AND mem_div_id > 0 ';
                }
            }

            if($req->memis_country > 0 && $req->memis_country != '999'){

                $where .= ' AND emp_country LIKE '. $req->memis_country .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';    
            
            }
            
            if($req->memis_sex > 0){ // sex

                if($req->memis_sex > 0 && $req->memis_sex != '999'){
                    $where .= ' AND pp_sex LIKE "' . $req->memis_sex . '" ';
                }else{
                    $where .= ' AND pp_sex > 0 ';
                }
            }

            if($req->memis_category > 0){ // category


                $join .= ' JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';

                if($req->memis_category > 0 && $req->memis_category != '999'){
                    $where .= ' AND membership_type_id LIKE "' . $req->memis_category . '" ';
                }else{
                    $where .= ' AND membership_type_id > 0 ';
                }

            }

            if($req->memis_status > 0){ // status
              
                $join .= '  JOIN new_dbskms.tblmembership_status ON mem_status = membership_status_id ';

                if($req->memis_status > 0 && $req->memis_status != '999'){
                    $where .= ' AND membership_status_id LIKE ' . $req->memis_status . ' ';
                }else{
                    $where .= ' AND membership_status_id > 0 ';
                }
                
                $deceased = '';
            }

            if($req->memis_educ > 0){ // educ 

                $key = ($having != '') ? 'AND' : ' HAVING ';
                $field .= ' , MIN(adp_highest) as minadp ';
                $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
                $having .= ' '.$key.' minadp = ' . $req->memis_educ . ' ';

            }

            if($req->memis_age > 0){ // age 

                    $key = ($having != '') ? 'AND' : 'HAVING';
                    $field .= ' ,(YEAR(CURDATE()) - YEAR(pp_date_of_birth)) AS age ';
                    $where .= ' AND pp_date_of_birth like \'%-%\' and pp_date_of_birth > 0 ';
                    
                    if($req->memis_age == 1){
                        $having .= ' '.$key.' age > 20 AND age < 31 ';
                    }else if($req->memis_age == 2){
                        $having .= ' '.$key.' age > 30 AND age < 41 ';
                    }else if($req->memis_age == 3){
                        $having .= ' '.$key.' age > 40 AND age < 51 ';
                    }else if($req->memis_age == 4){
                        $having .= ' '.$key.' age > 50 AND age < 61 ';
                    }else if($req->memis_age == 5){
                        $having .= ' '.$key.' age > 60 AND age < 71 ';
                    }else if($req->memis_age == 6){
                        $having .= ' '.$key.' age > 70 ';
                    }else{
                        $having .= ' '.$key.' age > 0 ';
                    }
            }
       
            if($req->memis_start_year > 0 && $req->memis_end_year){
                
                $key = ($having != '') ? 'AND' : ' HAVING ';
                $field .= ' ,YEAR(mem_date_elected) AS year ';
                $having .= ' '.$key. ' year BETWEEN '. $req->memis_start_year .' AND '. $req->memis_end_year .' '; 
            }

            $group_by .= ' GROUP BY pp_usr_id ';
            $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
            $join .= ' LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';
            $join .= ' JOIN new_dbskms.tblregions on region_id = emp_region ';
            $join .= ' LEFT JOIN new_dbskms.tbltitles ON title_id = pp_title ';
            $join .= ' LEFT JOIN new_dbskms.tblsex ON s_id = pp_sex ';
                            
            
            $where .= ' AND region_id LIKE '. $id;

            $select .= 'SELECT pp_first_name, pp_last_name, pp_middle_name, pp_sex, pp_title, pp_email, pp_contact,  title_name, sex '. $field .
            'FROM new_dbskms.tblmembers '. 
            'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
            'WHERE usr_grp_id LIKE 3 '. $where . $deceased . $group_by . $having;

            

            return DB::select($select);
            
        }else{

            $where = ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id) '; 

            $join .= 'LEFT JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
            $join .= 'LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';
            $join .= ' JOIN new_dbskms.tblregions on region_id = emp_region ';

            if($req->memis_start_year > 0 && $req->memis_end_year > 0){

                $key = ($having != '') ? 'AND' : ' HAVING ';
                $field .= ' ,YEAR(mem_date_elected) AS year ';
                $having .= ' '.$key. ' year BETWEEN '. $req->memis_start_year .' AND '. $req->memis_end_year .' '; 
            }

            $sub_q .= 'SELECT count(emp_usr_id) as total, region_name as label, region_id '. $field .  
            'FROM new_dbskms.tblmembers '. 
            'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
            'WHERE usr_grp_id LIKE 3'. $where . $deceased .
            'GROUP BY emp_usr_id'. $having . '';

            $select .= 'SELECT count(total) as total, label, region_id as bar_id FROM '. 
            '( ' . $sub_q . ') as tmp GROUP BY region_id';

            return  DB::select($select);
        }

    }else if($req->radio_default == 'memis_province'){ // province default

        if($req->memis_province == '999'){ 

            $where .= ' AND emp_region LIKE '. $req->memis_region;
            $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';
            
            if($req->memis_division > 0){ // division
                $join .= ' JOIN new_dbskms.tbldivisions ON div_id = mem_div_id ';
    
                if($req->memis_division > 0 && $req->memis_division != '999'){
                    $where .= ' AND mem_div_id LIKE "' . $req->memis_division . '" ';
                }else{
                    $where .= ' AND mem_div_id > 0 ';
                }
            }
            
            if($req->memis_sex > 0){ // sex

                if($req->memis_sex > 0 && $req->memis_sex != '999'){
                    $where .= ' AND pp_sex LIKE "' . $req->memis_sex . '" ';
                }else{
                    $where .= ' AND pp_sex > 0 ';
                }
            }

            if($req->memis_category > 0){ // category


                $join .= ' JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';

                if($req->memis_category > 0 && $req->memis_category != '999'){
                    $where .= ' AND membership_type_id LIKE "' . $req->memis_category . '" ';
                }else{
                    $where .= ' AND membership_type_id > 0 ';
                }

            }

            if($req->memis_status > 0){ // status
              
                $join .= '  JOIN new_dbskms.tblmembership_status ON mem_status = membership_status_id ';

                if($req->memis_status > 0 && $req->memis_status != '999'){
                    $where .= ' AND membership_status_id LIKE ' . $req->memis_status . ' ';
                }else{
                    $where .= ' AND membership_status_id > 0 ';
                }
                
                $deceased = '';
            }

            if($req->memis_educ > 0){ // educ 

                $key = ($having != '') ? 'AND' : ' HAVING ';
                $field .= ' , MIN(adp_highest) as minadp ';
                $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
                $having .= ' '.$key.' minadp = ' . $req->memis_educ . ' ';

            }

            if($req->memis_age > 0){ // age 

                    $key = ($having != '') ? 'AND' : 'HAVING';
                    $field .= ' ,(YEAR(CURDATE()) - YEAR(pp_date_of_birth)) AS age ';
                    $where .= ' AND pp_date_of_birth like \'%-%\' and pp_date_of_birth > 0 ';
                    
                    if($req->memis_age == 1){
                        $having .= ' '.$key.' age > 20 AND age < 31 ';
                    }else if($req->memis_age == 2){
                        $having .= ' '.$key.' age > 30 AND age < 41 ';
                    }else if($req->memis_age == 3){
                        $having .= ' '.$key.' age > 40 AND age < 51 ';
                    }else if($req->memis_age == 4){
                        $having .= ' '.$key.' age > 50 AND age < 61 ';
                    }else if($req->memis_age == 5){
                        $having .= ' '.$key.' age > 60 AND age < 71 ';
                    }else if($req->memis_age == 6){
                        $having .= ' '.$key.' age > 70 ';
                    }else{
                        $having .= ' '.$key.' age > 0 ';
                    }
            }
       
            if($req->memis_start_year > 0 && $req->memis_end_year){
                
                $key = ($having != '') ? 'AND' : ' HAVING ';
                $field .= ' ,YEAR(mem_date_elected) AS year ';
                $having .= ' '.$key. ' year BETWEEN '. $req->memis_start_year .' AND '. $req->memis_end_year .' '; 
            }

            $group_by .= ' GROUP BY pp_usr_id ';
            $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
            $join .= ' LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';
            $join .= ' JOIN new_dbskms.tblprovinces on province_id = emp_province ';
            $join .= ' LEFT JOIN new_dbskms.tbltitles ON title_id = pp_title ';
            $join .= ' LEFT JOIN new_dbskms.tblsex ON s_id = pp_sex ';
                            
            
            $where .= ' AND province_id LIKE '. $id;

            $select .= 'SELECT pp_first_name, pp_last_name, pp_middle_name, pp_sex, pp_title, pp_email, pp_contact,  title_name, sex '. $field .
            'FROM new_dbskms.tblmembers '. 
            'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
            'WHERE usr_grp_id LIKE 3 '. $where . $deceased . $group_by . $having;

            

            return DB::select($select);
        }
        
    }else if($req->radio_default == 'memis_city'){ // city default

        if($req->memis_city == '999' && $all > 1){ 

            $where .= ' AND emp_province LIKE '. $req->memis_province;
            $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';
            
            if($req->memis_division > 0){ // division
                $join .= ' JOIN new_dbskms.tbldivisions ON div_id = mem_div_id ';
    
                if($req->memis_division > 0 && $req->memis_division != '999'){
                    $where .= ' AND mem_div_id LIKE "' . $req->memis_division . '" ';
                }else{
                    $where .= ' AND mem_div_id > 0 ';
                }
            }
            
            if($req->memis_sex > 0){ // sex

                if($req->memis_sex > 0 && $req->memis_sex != '999'){
                    $where .= ' AND pp_sex LIKE "' . $req->memis_sex . '" ';
                }else{
                    $where .= ' AND pp_sex > 0 ';
                }
            }

            if($req->memis_category > 0){ // category


                $join .= ' JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';

                if($req->memis_category > 0 && $req->memis_category != '999'){
                    $where .= ' AND membership_type_id LIKE "' . $req->memis_category . '" ';
                }else{
                    $where .= ' AND membership_type_id > 0 ';
                }

            }

            if($req->memis_status > 0){ // status
              
                $join .= '  JOIN new_dbskms.tblmembership_status ON mem_status = membership_status_id ';

                if($req->memis_status > 0 && $req->memis_status != '999'){
                    $where .= ' AND membership_status_id LIKE ' . $req->memis_status . ' ';
                }else{
                    $where .= ' AND membership_status_id > 0 ';
                }
                
                $deceased = '';
            }

            if($req->memis_educ > 0){ // educ 

                $key = ($having != '') ? 'AND' : ' HAVING ';
                $field .= ' , MIN(adp_highest) as minadp ';
                $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
                $having .= ' '.$key.' minadp = ' . $req->memis_educ . ' ';

            }

            if($req->memis_age > 0){ // age 

                    $key = ($having != '') ? 'AND' : 'HAVING';
                    $field .= ' ,(YEAR(CURDATE()) - YEAR(pp_date_of_birth)) AS age ';
                    $where .= ' AND pp_date_of_birth like \'%-%\' and pp_date_of_birth > 0 ';
                    
                    if($req->memis_age == 1){
                        $having .= ' '.$key.' age > 20 AND age < 31 ';
                    }else if($req->memis_age == 2){
                        $having .= ' '.$key.' age > 30 AND age < 41 ';
                    }else if($req->memis_age == 3){
                        $having .= ' '.$key.' age > 40 AND age < 51 ';
                    }else if($req->memis_age == 4){
                        $having .= ' '.$key.' age > 50 AND age < 61 ';
                    }else if($req->memis_age == 5){
                        $having .= ' '.$key.' age > 60 AND age < 71 ';
                    }else if($req->memis_age == 6){
                        $having .= ' '.$key.' age > 70 ';
                    }else{
                        $having .= ' '.$key.' age > 0 ';
                    }
            }
       
            if($req->memis_start_year > 0 && $req->memis_end_year){
                
                $key = ($having != '') ? 'AND' : ' HAVING ';
                $field .= ' ,YEAR(mem_date_elected) AS year ';
                $having .= ' '.$key. ' year BETWEEN '. $req->memis_start_year .' AND '. $req->memis_end_year .' '; 
            }

            $group_by .= ' GROUP BY pp_usr_id ';
            $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
            $join .= ' LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';
            $join .= ' JOIN new_dbskms.tblcities on city_id = emp_city ';
            $join .= ' LEFT JOIN new_dbskms.tbltitles ON title_id = pp_title ';
            $join .= ' LEFT JOIN new_dbskms.tblsex ON s_id = pp_sex ';
                            
            
            $where .= ' AND city_id LIKE '. $id;

            $select .= 'SELECT pp_first_name, pp_last_name, pp_middle_name, pp_sex, pp_title, pp_email, pp_contact,  title_name, sex '. $field .
            'FROM new_dbskms.tblmembers '. 
            'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
            'WHERE usr_grp_id LIKE 3 '. $where . $deceased . $group_by . $having;

            

            return DB::select($select);
            
        }
    }else if($req->radio_default == 'memis_sex'){ // sex default

        if($req->memis_sex == '999'){ 

            $where .= ' AND pp_sex > 0 ';

            if($req->memis_division > 0){ // division

                $join .= ' JOIN new_dbskms.tbldivisions ON div_id = mem_div_id ';
    
                if($req->memis_division > 0 && $req->memis_division != '999'){
                    $where .= ' AND mem_div_id LIKE "' . $req->memis_division . '" ';
                }else{
                    $where .= ' AND mem_div_id > 0 ';
                }
            }

            if($req->memis_region > 0 && $req->memis_region != '999'){
   
                if($req->memis_province > 0 && $req->memis_province != '999'){
                    
                    if($req->memis_city > 0 && $req->memis_city != '999'){
                        $where .= ' AND emp_city LIKE '. $req->memis_city .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';
                    }else{
                        $where .= ' AND emp_province LIKE '. $req->memis_province .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';
                    }
                }
                else{
                    $where .= ' AND emp_region LIKE '. $req->memis_region .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';    
                }

            }

            if($req->memis_country > 0 && $req->memis_country != '999'){

                $where .= ' AND emp_country LIKE '. $req->memis_country .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';    
            
            }              

            if($req->memis_category > 0){ // category


                $join .= ' JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';

                if($req->memis_category > 0 && $req->memis_category != '999'){
                    $where .= ' AND membership_type_id LIKE "' . $req->memis_category . '" ';
                }else{
                    $where .= ' AND membership_type_id > 0 ';
                }

            }

            if($req->memis_status > 0){ // status
              
                $join .= '  JOIN new_dbskms.tblmembership_status ON mem_status = membership_status_id ';

                if($req->memis_status > 0 && $req->memis_status != '999'){
                    $where .= ' AND membership_status_id LIKE ' . $req->memis_status . ' ';
                }else{
                    $where .= ' AND membership_status_id > 0 ';
                }
                
                $deceased = '';
            }

            if($req->memis_educ > 0){ // educ 

                $key = ($having != '') ? 'AND' : ' HAVING ';
                $field .= ' , MIN(adp_highest) as minadp ';
                $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
                $having .= ' '.$key.' minadp = ' . $req->memis_educ . ' ';

            }

            if($req->memis_age > 0){ // age 

                    $key = ($having != '') ? 'AND' : 'HAVING';
                    $field .= ' ,(YEAR(CURDATE()) - YEAR(pp_date_of_birth)) AS age ';
                    $where .= ' AND pp_date_of_birth like \'%-%\' and pp_date_of_birth > 0 ';
                    
                    if($req->memis_age == 1){
                        $having .= ' '.$key.' age > 20 AND age < 31 ';
                    }else if($req->memis_age == 2){
                        $having .= ' '.$key.' age > 30 AND age < 41 ';
                    }else if($req->memis_age == 3){
                        $having .= ' '.$key.' age > 40 AND age < 51 ';
                    }else if($req->memis_age == 4){
                        $having .= ' '.$key.' age > 50 AND age < 61 ';
                    }else if($req->memis_age == 5){
                        $having .= ' '.$key.' age > 60 AND age < 71 ';
                    }else if($req->memis_age == 6){
                        $having .= ' '.$key.' age > 70 ';
                    }else{
                        $having .= ' '.$key.' age > 0 ';
                    }
            }
       
            if($req->memis_start_year > 0 && $req->memis_end_year){
                
                $key = ($having != '') ? 'AND' : ' HAVING ';
                $field .= ' ,YEAR(mem_date_elected) AS year ';
                $having .= ' '.$key. ' year BETWEEN '. $req->memis_start_year .' AND '. $req->memis_end_year .' '; 
            }

            $group_by .= ' GROUP BY pp_usr_id ';
            $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
            $join .= ' JOIN new_dbskms.tblsex on s_id = pp_sex ';
            $join .= ' LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';
            $join .= ' LEFT JOIN new_dbskms.tbltitles ON title_id = pp_title ';
                            
            
            $where .= ' AND s_id LIKE '. $id;

            $select .= 'SELECT pp_first_name, pp_last_name, pp_middle_name, pp_sex, pp_title, pp_email, pp_contact,  title_name, sex '. $field .
            'FROM new_dbskms.tblmembers '. 
            'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
            'WHERE usr_grp_id LIKE 3 '. $where . $deceased . $group_by . $having;

            

            return DB::select($select);
            
        }
    }else if($req->radio_default == 'memis_category'){ // category default

        if($req->memis_category == '999'){ 

            $where .= ' AND membership_type_id > 0 ';

            if($req->memis_division > 0){ // division

                $join .= ' JOIN new_dbskms.tbldivisions ON div_id = mem_div_id ';
    
                if($req->memis_division > 0 && $req->memis_division != '999'){
                    $where .= ' AND mem_div_id LIKE "' . $req->memis_division . '" ';
                }else{
                    $where .= ' AND mem_div_id > 0 ';
                }
            }

            if($req->memis_region > 0 && $req->memis_region != '999'){
   
                if($req->memis_province > 0 && $req->memis_province != '999'){
                    
                    if($req->memis_city > 0 && $req->memis_city != '999'){
                        $where .= ' AND emp_city LIKE '. $req->memis_city .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';
                    }else{
                        $where .= ' AND emp_province LIKE '. $req->memis_province .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';
                    }
                }
                else{
                    $where .= ' AND emp_region LIKE '. $req->memis_region .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';    
                }

            }

            if($req->memis_country > 0 && $req->memis_country != '999'){

                $where .= ' AND emp_country LIKE '. $req->memis_country .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';    
            
            }
            
            if($req->memis_sex > 0){ // sex

                if($req->memis_sex > 0 && $req->memis_sex != '999'){
                    $where .= ' AND pp_sex LIKE "' . $req->memis_sex . '" ';
                }else{
                    $where .= ' AND pp_sex > 0 ';
                }
            }

            if($req->memis_status > 0){ // status
              
                $join .= '  JOIN new_dbskms.tblmembership_status ON mem_status = membership_status_id ';

                if($req->memis_status > 0 && $req->memis_status != '999'){
                    $where .= ' AND membership_status_id LIKE ' . $req->memis_status . ' ';
                }else{
                    $where .= ' AND membership_status_id > 0 ';
                }
                
                $deceased = '';
            }

            if($req->memis_educ > 0){ // educ 

                $key = ($having != '') ? 'AND' : ' HAVING ';
                $field .= ' , MIN(adp_highest) as minadp ';
                $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
                $having .= ' '.$key.' minadp = ' . $req->memis_educ . ' ';

            }

            if($req->memis_age > 0){ // age 

                    $key = ($having != '') ? 'AND' : 'HAVING';
                    $field .= ' ,(YEAR(CURDATE()) - YEAR(pp_date_of_birth)) AS age ';
                    $where .= ' AND pp_date_of_birth like \'%-%\' and pp_date_of_birth > 0 ';
                    
                    if($req->memis_age == 1){
                        $having .= ' '.$key.' age > 20 AND age < 31 ';
                    }else if($req->memis_age == 2){
                        $having .= ' '.$key.' age > 30 AND age < 41 ';
                    }else if($req->memis_age == 3){
                        $having .= ' '.$key.' age > 40 AND age < 51 ';
                    }else if($req->memis_age == 4){
                        $having .= ' '.$key.' age > 50 AND age < 61 ';
                    }else if($req->memis_age == 5){
                        $having .= ' '.$key.' age > 60 AND age < 71 ';
                    }else if($req->memis_age == 6){
                        $having .= ' '.$key.' age > 70 ';
                    }else{
                        $having .= ' '.$key.' age > 0 ';
                    }
            }
       
            if($req->memis_start_year > 0 && $req->memis_end_year){
                
                $key = ($having != '') ? 'AND' : ' HAVING ';
                $field .= ' ,YEAR(mem_date_elected) AS year ';
                $having .= ' '.$key. ' year BETWEEN '. $req->memis_start_year .' AND '. $req->memis_end_year .' '; 
            }

            $group_by .= ' GROUP BY pp_usr_id ';
            $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
            $join .= ' JOIN new_dbskms.tblmembership_types on membership_type_id = mem_type ';
            $join .= ' LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';
            $join .= ' LEFT JOIN new_dbskms.tbltitles ON title_id = pp_title ';
            $join .= ' LEFT JOIN new_dbskms.tblsex ON s_id = pp_sex ';
                            
            
            $where .= ' AND membership_type_id LIKE '. $id;

            $select .= 'SELECT pp_first_name, pp_last_name, pp_middle_name, pp_sex, pp_title, pp_email, pp_contact,  title_name, sex '. $field .
            'FROM new_dbskms.tblmembers '. 
            'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
            'WHERE usr_grp_id LIKE 3 '. $where . $deceased . $group_by . $having;

            

            return DB::select($select);
        }
    }else if($req->radio_default == 'memis_status'){ // status default

        if($req->memis_status == '999'){ 

            $deceased = '';
            $where .= ' AND membership_status_id > 0 ';

            if($req->memis_division > 0){ // division

                $join .= ' JOIN new_dbskms.tbldivisions ON div_id = mem_div_id ';
    
                if($req->memis_division > 0 && $req->memis_division != '999'){
                    $where .= ' AND mem_div_id LIKE "' . $req->memis_division . '" ';
                }else{
                    $where .= ' AND mem_div_id > 0 ';
                }
            }
              
            if($req->memis_region > 0 && $req->memis_region != '999'){
   
                if($req->memis_province > 0 && $req->memis_province != '999'){
                    
                    if($req->memis_city > 0 && $req->memis_city != '999'){
                        $where .= ' AND emp_city LIKE '. $req->memis_city .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';
                    }else{
                        $where .= ' AND emp_province LIKE '. $req->memis_province .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';
                    }
                }
                else{
                    $where .= ' AND emp_region LIKE '. $req->memis_region .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';    
                }

            }

            if($req->memis_country > 0 && $req->memis_country != '999'){

                $where .= ' AND emp_country LIKE '. $req->memis_country .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';    
            
            }
            
            if($req->memis_sex > 0){ // sex

                if($req->memis_sex > 0 && $req->memis_sex != '999'){
                    $where .= ' AND pp_sex LIKE "' . $req->memis_sex . '" ';
                }else{
                    $where .= ' AND pp_sex > 0 ';
                }
            }

            if($req->memis_category > 0){ // category


                $join .= ' JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';

                if($req->memis_category > 0 && $req->memis_category != '999'){
                    $where .= ' AND membership_type_id LIKE "' . $req->memis_category . '" ';
                }else{
                    $where .= ' AND membership_type_id > 0 ';
                }

            }

            if($req->memis_educ > 0){ // educ 

                $key = ($having != '') ? 'AND' : ' HAVING ';
                $field .= ' , MIN(adp_highest) as minadp ';
                $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
                $having .= ' '.$key.' minadp = ' . $req->memis_educ . ' ';

            }

            if($req->memis_age > 0){ // age 

                    $key = ($having != '') ? 'AND' : 'HAVING';
                    $field .= ' ,(YEAR(CURDATE()) - YEAR(pp_date_of_birth)) AS age ';
                    $where .= ' AND pp_date_of_birth like \'%-%\' and pp_date_of_birth > 0 ';
                    
                    if($req->memis_age == 1){
                        $having .= ' '.$key.' age > 20 AND age < 31 ';
                    }else if($req->memis_age == 2){
                        $having .= ' '.$key.' age > 30 AND age < 41 ';
                    }else if($req->memis_age == 3){
                        $having .= ' '.$key.' age > 40 AND age < 51 ';
                    }else if($req->memis_age == 4){
                        $having .= ' '.$key.' age > 50 AND age < 61 ';
                    }else if($req->memis_age == 5){
                        $having .= ' '.$key.' age > 60 AND age < 71 ';
                    }else if($req->memis_age == 6){
                        $having .= ' '.$key.' age > 70 ';
                    }else{
                        $having .= ' '.$key.' age > 0 ';
                    }
            }
       
            if($req->memis_start_year > 0 && $req->memis_end_year){
                
                $key = ($having != '') ? 'AND' : ' HAVING ';
                $field .= ' ,YEAR(mem_date_elected) AS year ';
                $having .= ' '.$key. ' year BETWEEN '. $req->memis_start_year .' AND '. $req->memis_end_year .' '; 
            }

            $group_by .= ' GROUP BY pp_usr_id ';
            $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
            $join .= ' JOIN new_dbskms.tblmembership_status on membership_status_id = mem_status ';
            $join .= ' LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';
            $join .= ' LEFT JOIN new_dbskms.tbltitles ON title_id = pp_title ';
            $join .= ' LEFT JOIN new_dbskms.tblsex ON s_id = pp_sex ';
                            
            
            $where .= ' AND membership_status_id LIKE '. $id;

            $select .= 'SELECT pp_first_name, pp_last_name, pp_middle_name, pp_sex, pp_title, pp_email, pp_contact,  title_name, sex '. $field .
            'FROM new_dbskms.tblmembers '. 
            'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
            'WHERE usr_grp_id LIKE 3 '. $where . $deceased . $group_by . $having;

            

            return DB::select($select);

             
        }
    }else if($req->radio_default == 'memis_educ'){ // educ default

        if($req->memis_educ == '999'){ 

            
            $field .= ' , MIN(adp_highest) as minadp ';

            if($req->memis_division > 0){ // division

                $join .= ' JOIN new_dbskms.tbldivisions ON div_id = mem_div_id ';
    
                if($req->memis_division > 0 && $req->memis_division != '999'){
                    $where .= ' AND mem_div_id LIKE "' . $req->memis_division . '" ';
                }else{
                    $where .= ' AND mem_div_id > 0 ';
                }
            }
                
            if($req->memis_region > 0 && $req->memis_region != '999'){
   
                if($req->memis_province > 0 && $req->memis_province != '999'){
                    
                    if($req->memis_city > 0 && $req->memis_city != '999'){
                        $where .= ' AND emp_city LIKE '. $req->memis_city .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';
                    }else{
                        $where .= ' AND emp_province LIKE '. $req->memis_province .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';
                    }
                }
                else{
                    $where .= ' AND emp_region LIKE '. $req->memis_region .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';    
                }

            }

            if($req->memis_country > 0 && $req->memis_country != '999'){

                $where .= ' AND emp_country LIKE '. $req->memis_country .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';    
            
            }
            
            if($req->memis_sex > 0){ // sex

                if($req->memis_sex > 0 && $req->memis_sex != '999'){
                    $where .= ' AND pp_sex LIKE "' . $req->memis_sex . '" ';
                }else{
                    $where .= ' AND pp_sex > 0 ';
                }
            }

            if($req->memis_category > 0){ // category


                $join .= ' JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';

                if($req->memis_category > 0 && $req->memis_category != '999'){
                    $where .= ' AND membership_type_id LIKE "' . $req->memis_category . '" ';
                }else{
                    $where .= ' AND membership_type_id > 0 ';
                }

            }

            if($req->memis_status > 0){ // status
              
                $join .= '  JOIN new_dbskms.tblmembership_status ON mem_status = membership_status_id ';

                if($req->memis_status > 0 && $req->memis_status != '999'){
                    $where .= ' AND membership_status_id LIKE ' . $req->memis_status . ' ';
                }else{
                    $where .= ' AND membership_status_id > 0 ';
                }
                
                $deceased = '';
            }

            if($req->memis_age > 0){ // age 

                    $key = ($having != '') ? 'AND' : 'HAVING';
                    $field .= ' ,(YEAR(CURDATE()) - YEAR(pp_date_of_birth)) AS age ';
                    $where .= ' AND pp_date_of_birth like \'%-%\' and pp_date_of_birth > 0 ';
                    
                    if($req->memis_age == 1){
                        $having .= ' '.$key.' age > 20 AND age < 31 ';
                    }else if($req->memis_age == 2){
                        $having .= ' '.$key.' age > 30 AND age < 41 ';
                    }else if($req->memis_age == 3){
                        $having .= ' '.$key.' age > 40 AND age < 51 ';
                    }else if($req->memis_age == 4){
                        $having .= ' '.$key.' age > 50 AND age < 61 ';
                    }else if($req->memis_age == 5){
                        $having .= ' '.$key.' age > 60 AND age < 71 ';
                    }else if($req->memis_age == 6){
                        $having .= ' '.$key.' age > 70 ';
                    }else{
                        $having .= ' '.$key.' age > 0 ';
                    }
            }
       
            if($req->memis_start_year > 0 && $req->memis_end_year){
                
                $key = ($having != '') ? 'AND' : ' HAVING ';
                $field .= ' ,YEAR(mem_date_elected) AS year ';
                $having .= ' '.$key. ' year BETWEEN '. $req->memis_start_year .' AND '. $req->memis_end_year .' '; 
            }

            $group_by .= ' GROUP BY pp_usr_id ';
            $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
            $join .= ' JOIN new_dbskms.tblacademic_degree_profiles on adp_usr_id = usr_id ';
            $join .= ' JOIN new_dbskms.tbldegree_types on deg_id = adp_highest ';
            $join .= ' LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';
            $join .= ' LEFT JOIN new_dbskms.tbltitles ON title_id = pp_title ';
            $join .= ' LEFT JOIN new_dbskms.tblsex ON s_id = pp_sex ';
                            
            
            $where .= ' AND deg_id LIKE '. $id;

            $select .= 'SELECT pp_first_name, pp_last_name, pp_middle_name, pp_sex, pp_title, pp_email, pp_contact,  title_name, sex '. $field .
            'FROM new_dbskms.tblmembers '. 
            'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
            'WHERE usr_grp_id LIKE 3 '. $where . $deceased . $group_by . $having;

            

            return DB::select($select);
        }
    }else if($req->radio_default == 'memis_country'){ // country default

        if($req->memis_country == '999' ){ 

               
            $where .= ' AND emp_country > 0 ';
            $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';  

            if($req->memis_region > 0 && $req->memis_region != '999'){
   
                if($req->memis_province > 0 && $req->memis_province != '999'){
                    
                    if($req->memis_city > 0 && $req->memis_city != '999'){
                        $where .= ' AND emp_city LIKE '. $req->memis_city .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';
                    }else{
                        $where .= ' AND emp_province LIKE '. $req->memis_province .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';
                    }
                }
                else{
                    $where .= ' AND emp_region LIKE '. $req->memis_region .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';    
                }

            }

            
            if($req->memis_sex > 0){ // sex

                if($req->memis_sex > 0 && $req->memis_sex != '999'){
                    $where .= ' AND pp_sex LIKE "' . $req->memis_sex . '" ';
                }else{
                    $where .= ' AND pp_sex > 0 ';
                }
            }

            if($req->memis_category > 0){ // category


                $join .= ' JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';

                if($req->memis_category > 0 && $req->memis_category != '999'){
                    $where .= ' AND membership_type_id LIKE "' . $req->memis_category . '" ';
                }else{
                    $where .= ' AND membership_type_id > 0 ';
                }

            }

            if($req->memis_status > 0){ // status
              
                $join .= '  JOIN new_dbskms.tblmembership_status ON mem_status = membership_status_id ';

                if($req->memis_status > 0 && $req->memis_status != '999'){
                    $where .= ' AND membership_status_id LIKE ' . $req->memis_status . ' ';
                }else{
                    $where .= ' AND membership_status_id > 0 ';
                }
                
                $deceased = '';
            }

            if($req->memis_educ > 0){ // educ 

                $key = ($having != '') ? 'AND' : ' HAVING ';
                $field .= ' , MIN(adp_highest) as minadp ';
                $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
                $having .= ' '.$key.' minadp = ' . $req->memis_educ . ' ';

            }

            if($req->memis_age > 0){ // age 

                    $key = ($having != '') ? 'AND' : 'HAVING';
                    $field .= ' ,(YEAR(CURDATE()) - YEAR(pp_date_of_birth)) AS age ';
                    $where .= ' AND pp_date_of_birth like \'%-%\' and pp_date_of_birth > 0 ';
                    
                    if($req->memis_age == 1){
                        $having .= ' '.$key.' age > 20 AND age < 31 ';
                    }else if($req->memis_age == 2){
                        $having .= ' '.$key.' age > 30 AND age < 41 ';
                    }else if($req->memis_age == 3){
                        $having .= ' '.$key.' age > 40 AND age < 51 ';
                    }else if($req->memis_age == 4){
                        $having .= ' '.$key.' age > 50 AND age < 61 ';
                    }else if($req->memis_age == 5){
                        $having .= ' '.$key.' age > 60 AND age < 71 ';
                    }else if($req->memis_age == 6){
                        $having .= ' '.$key.' age > 70 ';
                    }else{
                        $having .= ' '.$key.' age > 0 ';
                    }
            }
       
            if($req->memis_start_year > 0 && $req->memis_end_year){
                
                $key = ($having != '') ? 'AND' : ' HAVING ';
                $field .= ' ,YEAR(mem_date_elected) AS year ';
                $having .= ' '.$key. ' year BETWEEN '. $req->memis_start_year .' AND '. $req->memis_end_year .' '; 
            }

            $group_by .= ' GROUP BY pp_usr_id ';
            $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
            $join .= ' LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';
            $join .= ' JOIN new_dbskms.tblcountries on country_id = emp_country ';
            $join .= ' LEFT JOIN new_dbskms.tbltitles ON title_id = pp_title ';
            $join .= ' LEFT JOIN new_dbskms.tblsex ON s_id = pp_sex ';
                            
            
            $where .= ' AND country_id LIKE '. $id;

            $select .= 'SELECT pp_first_name, pp_last_name, pp_middle_name, pp_sex, pp_title, pp_email, pp_contact,  title_name, sex '. $field .
            'FROM new_dbskms.tblmembers '. 
            'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
            'WHERE usr_grp_id LIKE 3 '. $where . $deceased . $group_by . $having;


            return DB::select($select);
            
        }
    }else if($req->radio_default == 'memis_age'){ // age default + no bar id 

        if($req->memis_age == '999'){     
            
            if($req->memis_division > 0){ // division

                $join .= ' JOIN new_dbskms.tbldivisions ON div_id = mem_div_id ';
    
                if($req->memis_division > 0 && $req->memis_division != '999'){
                    $where .= ' AND mem_div_id LIKE "' . $req->memis_division . '" ';
                }else{
                    $where .= ' AND mem_div_id > 0 ';
                }
            }

            if($req->memis_region > 0 && $req->memis_region != '999'){

                if($req->memis_province > 0 && $req->memis_province != '999'){
                    
                    if($req->memis_city > 0 && $req->memis_city != '999'){
                        $where .= ' AND emp_city LIKE '. $req->memis_city .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';
                    }else{
                        $where .= ' AND emp_province LIKE '. $req->memis_province .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';
                    }
                }
                else{
                    $where .= ' AND emp_region LIKE '. $req->memis_region .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';    
                }

            }

            if($req->memis_country > 0 && $req->memis_country != '999'){

                $where .= ' AND emp_country LIKE '. $req->memis_country .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';    
            
            }
            
            if($req->memis_sex > 0){ // sex

                if($req->memis_sex > 0 && $req->memis_sex != '999'){
                    $where .= ' AND pp_sex LIKE "' . $req->memis_sex . '" ';
                }else{
                    $where .= ' AND pp_sex > 0 ';
                }
            }

            if($req->memis_category > 0){ // category


                $join .= ' JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';

                if($req->memis_category > 0 && $req->memis_category != '999'){
                    $where .= ' AND membership_type_id LIKE "' . $req->memis_category . '" ';
                }else{
                    $where .= ' AND membership_type_id > 0 ';
                }

            }

            if($req->memis_status > 0){ // status
              
                $join .= '  JOIN new_dbskms.tblmembership_status ON mem_status = membership_status_id ';

                if($req->memis_status > 0 && $req->memis_status != '999'){
                    $where .= ' AND membership_status_id LIKE ' . $req->memis_status . ' ';
                }else{
                    $where .= ' AND membership_status_id > 0 ';
                }
                
                $deceased = '';
            }

            if($req->memis_educ > 0){ // educ 

                $key = ($having != '') ? 'AND' : ' HAVING ';
                $field .= ' , MIN(adp_highest) as minadp ';
                $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
                $having .= ' '.$key.' minadp = ' . $req->memis_educ . ' ';

            }
   
            if($req->memis_start_year > 0 && $req->memis_end_year){
                
                $key = ($having != '') ? 'AND' : ' HAVING ';
                $field .= ' ,YEAR(mem_date_elected) AS year ';
                $having .= ' '.$key. ' year BETWEEN '. $req->memis_start_year .' AND '. $req->memis_end_year .' '; 
            }

            $group_by .= ' GROUP BY pp_usr_id ';
            $join .= ' LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';
            $join .= ' LEFT JOIN new_dbskms.tbltitles ON title_id = pp_title ';
            $join .= ' LEFT JOIN new_dbskms.tblsex ON s_id = pp_sex ';

            
        
            $sub_q .= 'SELECT pp_first_name, pp_last_name, pp_middle_name, pp_sex, pp_title, pp_email, pp_contact,  title_name, sex, '.
            'CASE  '.
            'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 20 AND 31 then "1" '.
            'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 30 AND 41 then "2" '.
            'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 40 AND 51 then "3" '.
            'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 50 AND 61 then "4" '.
            'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 60 AND 71 then "5" '.
            'ELSE "6" END AS "range" '. $field .
            'FROM new_dbskms.tblpersonal_profiles '.
            'INNER JOIN new_dbskms.tblusers ON usr_id = pp_usr_id '.
            'LEFT JOIN new_dbskms.tblmembers ON mem_usr_id = usr_id '. $join . 
            ' WHERE pp_date_of_birth LIKE \'%-%\' '. 
            'AND pp_date_of_birth > 0 '. 
            'AND usr_grp_id LIKE 3 '. $where . $deceased . $group_by . $having;


            $select .= 'SELECT pp_first_name, pp_last_name, pp_middle_name, pp_sex, pp_title, pp_email, pp_contact,  title_name, sex FROM '. 
            '( ' . $sub_q . ') as tmp WHERE tmp.range = '. $id;

            return DB::select($select);


            
        }
    }else if($req->radio_default == 'memis_island'){ // island default 
        if($req->memis_island == '999'){

            if($req->memis_division > 0){ // division

                $join .= ' JOIN new_dbskms.tbldivisions ON div_id = mem_div_id ';
    
                if($req->memis_division > 0 && $req->memis_division != '999'){
                    $where .= ' AND mem_div_id LIKE "' . $req->memis_division . '" ';
                }else{
                    $where .= ' AND mem_div_id > 0 ';
                }
            }
            
            if($req->memis_sex > 0){ // sex

                if($req->memis_sex > 0 && $req->memis_sex != '999'){
                    $where .= ' AND pp_sex LIKE "' . $req->memis_sex . '" ';
                }else{
                    $where .= ' AND pp_sex > 0 ';
                }
            }

            if($req->memis_category > 0){ // category


                $join .= ' JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';

                if($req->memis_category > 0 && $req->memis_category != '999'){
                    $where .= ' AND membership_type_id LIKE "' . $req->memis_category . '" ';
                }else{
                    $where .= ' AND membership_type_id > 0 ';
                }

            }

            if($req->memis_status > 0){ // status
              
                $join .= '  JOIN new_dbskms.tblmembership_status ON mem_status = membership_status_id ';

                if($req->memis_status > 0 && $req->memis_status != '999'){
                    $where .= ' AND membership_status_id LIKE ' . $req->memis_status . ' ';
                }else{
                    $where .= ' AND membership_status_id > 0 ';
                }
                
                $deceased = '';
            }

            if($req->memis_educ > 0){ // educ 

                $key = ($having != '') ? 'AND' : ' HAVING ';
                $field .= ' , MIN(adp_highest) as minadp ';
                $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
                $having .= ' '.$key.' minadp = ' . $req->memis_educ . ' ';

            }

            if($req->memis_age > 0){ // age 

                    $key = ($having != '') ? 'AND' : 'HAVING';
                    $field .= ' ,(YEAR(CURDATE()) - YEAR(pp_date_of_birth)) AS age ';
                    $where .= ' AND pp_date_of_birth like \'%-%\' and pp_date_of_birth > 0 ';
                    
                    if($req->memis_age == 1){
                        $having .= ' '.$key.' age > 20 AND age < 31 ';
                    }else if($req->memis_age == 2){
                        $having .= ' '.$key.' age > 30 AND age < 41 ';
                    }else if($req->memis_age == 3){
                        $having .= ' '.$key.' age > 40 AND age < 51 ';
                    }else if($req->memis_age == 4){
                        $having .= ' '.$key.' age > 50 AND age < 61 ';
                    }else if($req->memis_age == 5){
                        $having .= ' '.$key.' age > 60 AND age < 71 ';
                    }else if($req->memis_age == 6){
                        $having .= ' '.$key.' age > 70 ';
                    }else{
                        $having .= ' '.$key.' age > 0 ';
                    }
            }
       
            if($req->memis_start_year > 0 && $req->memis_end_year){
                
                $key = ($having != '') ? 'AND' : ' HAVING ';
                $field .= ' ,YEAR(mem_date_elected) AS year ';
                $having .= ' '.$key. ' year BETWEEN '. $req->memis_start_year .' AND '. $req->memis_end_year .' '; 
            }

            $group_by .= ' GROUP BY pp_usr_id ';
            $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
            $join .= ' LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';
            $join .= ' JOIN new_dbskms.tblregions ON region_id = emp_region  ';
            $join .= ' LEFT JOIN new_dbskms.tbltitles ON title_id = pp_title ';
            $join .= ' LEFT JOIN new_dbskms.tblsex ON s_id = pp_sex ';
                            
            
            $where .= ' AND region_group LIKE '. $id;

            $select .= 'SELECT pp_first_name, pp_last_name, pp_middle_name, pp_sex, pp_title, pp_email, pp_contact,  title_name, sex '. $field .
            'FROM new_dbskms.tblmembers '. 
            'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
            'WHERE usr_grp_id LIKE 3 '. $where . $deceased . $group_by . $having;


            return DB::select($select);
  
        }
    }
    }

    static function do_drilldown_region($req, $all){

        $query = DB::connection('dbskms');
        $select = '';
        $where = '';
        $join = '';
        $group_by = '';
        $order_by = '';
        $having = '';
        $deceased = ' AND mem_status != 3 ';
        $key = '';
        $field = '';
        $sub_q = '';

        if($req->radio_default == 'memis_region'){ // province default

         

                $where .= ' AND emp_region LIKE '. $req->par1;
                $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';
                
                if($req->memis_division > 0){ // division
                    $join .= ' JOIN new_dbskms.tbldivisions ON div_id = mem_div_id ';
        
                    if($req->memis_division > 0 && $req->memis_division != '999'){
                        $where .= ' AND mem_div_id LIKE "' . $req->memis_division . '" ';
                    }else{
                        $where .= ' AND mem_div_id > 0 ';
                    }
                }
                
                if($req->memis_sex > 0){ // sex

                    if($req->memis_sex > 0 && $req->memis_sex != '999'){
                        $where .= ' AND pp_sex LIKE "' . $req->memis_sex . '" ';
                    }else{
                        $where .= ' AND pp_sex > 0 ';
                    }
                }

                if($req->memis_category > 0){ // category


                    $join .= ' JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';
    
                    if($req->memis_category > 0 && $req->memis_category != '999'){
                        $where .= ' AND membership_type_id LIKE "' . $req->memis_category . '" ';
                    }else{
                        $where .= ' AND membership_type_id > 0 ';
                    }

                }

                if($req->memis_status > 0){ // status
                  
                    $join .= '  JOIN new_dbskms.tblmembership_status ON mem_status = membership_status_id ';
    
                    if($req->memis_status > 0 && $req->memis_status != '999'){
                        $where .= ' AND membership_status_id LIKE ' . $req->memis_status . ' ';
                    }else{
                        $where .= ' AND membership_status_id > 0 ';
                    }
                    
                    $deceased = '';
                }

                if($req->memis_educ > 0){ // educ 

                    $key = ($having != '') ? 'AND' : ' HAVING ';
                    $field .= ' , MIN(adp_highest) as minadp ';
                    $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
                    $having .= ' '.$key.' minadp = ' . $req->memis_educ . ' ';

                }

                if($req->memis_age > 0){ // age 

                        $key = ($having != '') ? 'AND' : 'HAVING';
                        $field .= ' ,(YEAR(CURDATE()) - YEAR(pp_date_of_birth)) AS age ';
                        $where .= ' AND pp_date_of_birth like \'%-%\' and pp_date_of_birth > 0 ';
                        
                        if($req->memis_age == 1){
                            $having .= ' '.$key.' age > 20 AND age < 31 ';
                        }else if($req->memis_age == 2){
                            $having .= ' '.$key.' age > 30 AND age < 41 ';
                        }else if($req->memis_age == 3){
                            $having .= ' '.$key.' age > 40 AND age < 51 ';
                        }else if($req->memis_age == 4){
                            $having .= ' '.$key.' age > 50 AND age < 61 ';
                        }else if($req->memis_age == 5){
                            $having .= ' '.$key.' age > 60 AND age < 71 ';
                        }else if($req->memis_age == 6){
                            $having .= ' '.$key.' age > 70 ';
                        }else{
                            $having .= ' '.$key.' age > 0 ';
                        }
                }
           
                if($req->memis_start_year > 0 && $req->memis_end_year){
                    
                    $key = ($having != '') ? 'AND' : ' HAVING ';
                    $field .= ' ,YEAR(mem_date_elected) AS year ';
                    $having .= ' '.$key. ' year BETWEEN '. $req->memis_start_year .' AND '. $req->memis_end_year .' '; 
                }

                $group_by .= ' GROUP BY pp_usr_id ';
                $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
                $join .= ' LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';
                $join .= ' JOIN new_dbskms.tblprovinces on province_id = emp_province ';
                $join .= ' LEFT JOIN new_dbskms.tblregions on region_id = province_region_id ';
                                
                $sub_q .= 'SELECT province_id, count(mem_usr_id) as total, province_name AS label , province_region_id, region_name '. $field .
                'FROM new_dbskms.tblmembers '. 
                'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
                'WHERE usr_grp_id LIKE 3 '. $where . $deceased . $group_by . $having;

                $select .= 'SELECT count(total) as total, label, province_region_id, region_name FROM '. 
                '( ' . $sub_q . ') as tmp GROUP BY province_id';

                return DB::select($select);  
            
            
        }


    }

    // bar/pie graph
    static function do_bar_graph($req, $all){
     
        $query = DB::connection('dbskms');
        $select = '';
        $where = '';
        $join = '';
        $group_by = '';
        $order_by = '';
        $having = '';
        $deceased = ' AND mem_status != 3 ';
        $key = '';
        $field = '';
        $sub_q = '';

        if($req->radio_default == 'memis_division'){ // division default
                if($req->memis_division == '999'){         

                    if($req->memis_region > 0 && $req->memis_region != '999'){
       
                        if($req->memis_province > 0 && $req->memis_province != '999'){
                            
                            if($req->memis_city > 0 && $req->memis_city != '999'){
                                $where .= ' AND emp_city LIKE '. $req->memis_city .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';
                            }else{
                                $where .= ' AND emp_province LIKE '. $req->memis_province .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';
                            }
                        }
                        else{
                            $where .= ' AND emp_region LIKE '. $req->memis_region .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';    
                        }

                    }

                    if($req->memis_country > 0 && $req->memis_country != '999'){

                        $where .= ' AND emp_country LIKE '. $req->memis_country .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';    
                    
                    }
                    
                    if($req->memis_sex > 0){ // sex

                        if($req->memis_sex > 0 && $req->memis_sex != '999'){
                            $where .= ' AND pp_sex LIKE "' . $req->memis_sex . '" ';
                        }else{
                            $where .= ' AND pp_sex > 0 ';
                        }
                    }

                    if($req->memis_category > 0){ // category


                        $join .= ' JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';
        
                        if($req->memis_category > 0 && $req->memis_category != '999'){
                            $where .= ' AND membership_type_id LIKE "' . $req->memis_category . '" ';
                        }else{
                            $where .= ' AND membership_type_id > 0 ';
                        }

                    }

                    if($req->memis_status > 0){ // status
                      
                        $join .= '  JOIN new_dbskms.tblmembership_status ON mem_status = membership_status_id ';
        
                        if($req->memis_status > 0 && $req->memis_status != '999'){
                            $where .= ' AND membership_status_id LIKE ' . $req->memis_status . ' ';
                        }else{
                            $where .= ' AND membership_status_id > 0 ';
                        }
                        
                        $deceased = '';
                    }

                    if($req->memis_educ > 0){ // educ 

                        $key = ($having != '') ? 'AND' : ' HAVING ';
                        $field .= ' , MIN(adp_highest) as minadp ';
                        $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
                        $having .= ' '.$key.' minadp = ' . $req->memis_educ . ' ';

                    }

                    if($req->memis_age > 0){ // age 

                            $key = ($having != '') ? 'AND' : 'HAVING';
                            $field .= ' ,(YEAR(CURDATE()) - YEAR(pp_date_of_birth)) AS age ';
                            $where .= ' AND pp_date_of_birth like \'%-%\' and pp_date_of_birth > 0 ';
                            
                            if($req->memis_age == 1){
                                $having .= ' '.$key.' age > 20 AND age < 31 ';
                            }else if($req->memis_age == 2){
                                $having .= ' '.$key.' age > 30 AND age < 41 ';
                            }else if($req->memis_age == 3){
                                $having .= ' '.$key.' age > 40 AND age < 51 ';
                            }else if($req->memis_age == 4){
                                $having .= ' '.$key.' age > 50 AND age < 61 ';
                            }else if($req->memis_age == 5){
                                $having .= ' '.$key.' age > 60 AND age < 71 ';
                            }else if($req->memis_age == 6){
                                $having .= ' '.$key.' age > 70 ';
                            }else{
                                $having .= ' '.$key.' age > 0 ';
                            }
                    }
               
                    if($req->memis_start_year > 0 && $req->memis_end_year){
                        
                        $key = ($having != '') ? 'AND' : ' HAVING ';
                        $field .= ' ,YEAR(mem_date_elected) AS year ';
                        $having .= ' '.$key. ' year BETWEEN '. $req->memis_start_year .' AND '. $req->memis_end_year .' '; 
                    }

                    $group_by .= ' GROUP BY pp_usr_id ';
                    $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
                    $join .= ' JOIN new_dbskms.tbldivisions on div_id = mem_div_id ';
                    $join .= ' LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';
                                    
                    $sub_q .= 'SELECT div_id, count(mem_usr_id) as total, CONCAT("Division ",div_number) AS label '. $field .
                    'FROM new_dbskms.tblmembers '. 
                    'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
                    'WHERE usr_grp_id LIKE 3 '. $where . $deceased . $group_by . $having;
    
                    $select .= 'SELECT count(total) as total, label, div_id as bar_id FROM '. 
                    '( ' . $sub_q . ') as tmp GROUP BY div_id';
    
                    return DB::select($select);
                 
                }
        }else if($req->radio_default == 'memis_region'){ // region default

            if($req->memis_region == '999' && $all > 1){ 
                
                if($req->memis_division > 0){ // division

                    $join .= ' JOIN new_dbskms.tbldivisions ON div_id = mem_div_id ';
        
                    if($req->memis_division > 0 && $req->memis_division != '999'){
                        $where .= ' AND mem_div_id LIKE "' . $req->memis_division . '" ';
                    }else{
                        $where .= ' AND mem_div_id > 0 ';
                    }
                }

                if($req->memis_country > 0 && $req->memis_country != '999'){

                    $where .= ' AND emp_country LIKE '. $req->memis_country .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';    
                
                }
                
                if($req->memis_sex > 0){ // sex

                    if($req->memis_sex > 0 && $req->memis_sex != '999'){
                        $where .= ' AND pp_sex LIKE "' . $req->memis_sex . '" ';
                    }else{
                        $where .= ' AND pp_sex > 0 ';
                    }
                }

                if($req->memis_category > 0){ // category


                    $join .= ' JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';
    
                    if($req->memis_category > 0 && $req->memis_category != '999'){
                        $where .= ' AND membership_type_id LIKE "' . $req->memis_category . '" ';
                    }else{
                        $where .= ' AND membership_type_id > 0 ';
                    }

                }

                if($req->memis_status > 0){ // status
                  
                    $join .= '  JOIN new_dbskms.tblmembership_status ON mem_status = membership_status_id ';
    
                    if($req->memis_status > 0 && $req->memis_status != '999'){
                        $where .= ' AND membership_status_id LIKE ' . $req->memis_status . ' ';
                    }else{
                        $where .= ' AND membership_status_id > 0 ';
                    }
                    
                    $deceased = '';
                }

                if($req->memis_educ > 0){ // educ 

                    $key = ($having != '') ? 'AND' : ' HAVING ';
                    $field .= ' , MIN(adp_highest) as minadp ';
                    $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
                    $having .= ' '.$key.' minadp = ' . $req->memis_educ . ' ';

                }

                if($req->memis_age > 0){ // age 

                        $key = ($having != '') ? 'AND' : 'HAVING';
                        $field .= ' ,(YEAR(CURDATE()) - YEAR(pp_date_of_birth)) AS age ';
                        $where .= ' AND pp_date_of_birth like \'%-%\' and pp_date_of_birth > 0 ';
                        
                        if($req->memis_age == 1){
                            $having .= ' '.$key.' age > 20 AND age < 31 ';
                        }else if($req->memis_age == 2){
                            $having .= ' '.$key.' age > 30 AND age < 41 ';
                        }else if($req->memis_age == 3){
                            $having .= ' '.$key.' age > 40 AND age < 51 ';
                        }else if($req->memis_age == 4){
                            $having .= ' '.$key.' age > 50 AND age < 61 ';
                        }else if($req->memis_age == 5){
                            $having .= ' '.$key.' age > 60 AND age < 71 ';
                        }else if($req->memis_age == 6){
                            $having .= ' '.$key.' age > 70 ';
                        }else{
                            $having .= ' '.$key.' age > 0 ';
                        }
                }
           
                if($req->memis_start_year > 0 && $req->memis_end_year){
                    
                    $key = ($having != '') ? 'AND' : ' HAVING ';
                    $field .= ' ,YEAR(mem_date_elected) AS year ';
                    $having .= ' '.$key. ' year BETWEEN '. $req->memis_start_year .' AND '. $req->memis_end_year .' '; 
                }

                $group_by .= ' GROUP BY pp_usr_id ';
                $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
                $join .= ' LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';
                $join .= ' JOIN new_dbskms.tblregions on region_id = emp_region ';
                                
                $sub_q .= 'SELECT region_id, count(mem_usr_id) as total, region_name AS label '. $field .
                'FROM new_dbskms.tblmembers '. 
                'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
                'WHERE usr_grp_id LIKE 3 '. $where . $deceased . $group_by . $having;

                $select .= 'SELECT count(total) as total, label, region_id as bar_id FROM '. 
                '( ' . $sub_q . ') as tmp GROUP BY region_id';

                return DB::select($select);
                
            }else{

                $where = ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id) '; 
 
                $join .= 'LEFT JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
                $join .= 'LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';
                $join .= ' JOIN new_dbskms.tblregions on region_id = emp_region ';

                if($req->memis_start_year > 0 && $req->memis_end_year > 0){

                    $key = ($having != '') ? 'AND' : ' HAVING ';
                    $field .= ' ,YEAR(mem_date_elected) AS year ';
                    $having .= ' '.$key. ' year BETWEEN '. $req->memis_start_year .' AND '. $req->memis_end_year .' '; 
                }

                $sub_q .= 'SELECT count(emp_usr_id) as total, region_name as label, region_id '. $field .  
                'FROM new_dbskms.tblmembers '. 
                'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
                'WHERE usr_grp_id LIKE 3'. $where . $deceased .
                'GROUP BY emp_usr_id'. $having . '';

                $select .= 'SELECT count(total) as total, label, region_id as bar_id FROM '. 
                '( ' . $sub_q . ') as tmp GROUP BY region_id';

                return  DB::select($select);
            }

        }else if($req->radio_default == 'memis_province'){ // province default

            if($req->memis_province == '999'){ 

                $where .= ' AND emp_region LIKE '. $req->memis_region;
                $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';
                
                if($req->memis_division > 0){ // division
                    $join .= ' JOIN new_dbskms.tbldivisions ON div_id = mem_div_id ';
        
                    if($req->memis_division > 0 && $req->memis_division != '999'){
                        $where .= ' AND mem_div_id LIKE "' . $req->memis_division . '" ';
                    }else{
                        $where .= ' AND mem_div_id > 0 ';
                    }
                }
                
                if($req->memis_sex > 0){ // sex

                    if($req->memis_sex > 0 && $req->memis_sex != '999'){
                        $where .= ' AND pp_sex LIKE "' . $req->memis_sex . '" ';
                    }else{
                        $where .= ' AND pp_sex > 0 ';
                    }
                }

                if($req->memis_category > 0){ // category


                    $join .= ' JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';
    
                    if($req->memis_category > 0 && $req->memis_category != '999'){
                        $where .= ' AND membership_type_id LIKE "' . $req->memis_category . '" ';
                    }else{
                        $where .= ' AND membership_type_id > 0 ';
                    }

                }

                if($req->memis_status > 0){ // status
                  
                    $join .= '  JOIN new_dbskms.tblmembership_status ON mem_status = membership_status_id ';
    
                    if($req->memis_status > 0 && $req->memis_status != '999'){
                        $where .= ' AND membership_status_id LIKE ' . $req->memis_status . ' ';
                    }else{
                        $where .= ' AND membership_status_id > 0 ';
                    }
                    
                    $deceased = '';
                }

                if($req->memis_educ > 0){ // educ 

                    $key = ($having != '') ? 'AND' : ' HAVING ';
                    $field .= ' , MIN(adp_highest) as minadp ';
                    $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
                    $having .= ' '.$key.' minadp = ' . $req->memis_educ . ' ';

                }

                if($req->memis_age > 0){ // age 

                        $key = ($having != '') ? 'AND' : 'HAVING';
                        $field .= ' ,(YEAR(CURDATE()) - YEAR(pp_date_of_birth)) AS age ';
                        $where .= ' AND pp_date_of_birth like \'%-%\' and pp_date_of_birth > 0 ';
                        
                        if($req->memis_age == 1){
                            $having .= ' '.$key.' age > 20 AND age < 31 ';
                        }else if($req->memis_age == 2){
                            $having .= ' '.$key.' age > 30 AND age < 41 ';
                        }else if($req->memis_age == 3){
                            $having .= ' '.$key.' age > 40 AND age < 51 ';
                        }else if($req->memis_age == 4){
                            $having .= ' '.$key.' age > 50 AND age < 61 ';
                        }else if($req->memis_age == 5){
                            $having .= ' '.$key.' age > 60 AND age < 71 ';
                        }else if($req->memis_age == 6){
                            $having .= ' '.$key.' age > 70 ';
                        }else{
                            $having .= ' '.$key.' age > 0 ';
                        }
                }
           
                if($req->memis_start_year > 0 && $req->memis_end_year){
                    
                    $key = ($having != '') ? 'AND' : ' HAVING ';
                    $field .= ' ,YEAR(mem_date_elected) AS year ';
                    $having .= ' '.$key. ' year BETWEEN '. $req->memis_start_year .' AND '. $req->memis_end_year .' '; 
                }

                $group_by .= ' GROUP BY pp_usr_id ';
                $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
                $join .= ' LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';
                $join .= ' JOIN new_dbskms.tblprovinces on province_id = emp_province ';
                                
                $sub_q .= 'SELECT province_id, count(mem_usr_id) as total, province_name AS label '. $field .
                'FROM new_dbskms.tblmembers '. 
                'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
                'WHERE usr_grp_id LIKE 3 '. $where . $deceased . $group_by . $having;

                $select .= 'SELECT count(total) as total, label, province_id as bar_id FROM '. 
                '( ' . $sub_q . ') as tmp GROUP BY province_id';

                return DB::select($select);  
            }
            
        }else if($req->radio_default == 'memis_city'){ // city default

            if($req->memis_city == '999' && $all > 1){ 

                $where .= ' AND emp_province LIKE '. $req->memis_province;
                $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';
                
                if($req->memis_division > 0){ // division
                    $join .= ' JOIN new_dbskms.tbldivisions ON div_id = mem_div_id ';
        
                    if($req->memis_division > 0 && $req->memis_division != '999'){
                        $where .= ' AND mem_div_id LIKE "' . $req->memis_division . '" ';
                    }else{
                        $where .= ' AND mem_div_id > 0 ';
                    }
                }
                
                if($req->memis_sex > 0){ // sex

                    if($req->memis_sex > 0 && $req->memis_sex != '999'){
                        $where .= ' AND pp_sex LIKE "' . $req->memis_sex . '" ';
                    }else{
                        $where .= ' AND pp_sex > 0 ';
                    }
                }

                if($req->memis_category > 0){ // category


                    $join .= ' JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';
    
                    if($req->memis_category > 0 && $req->memis_category != '999'){
                        $where .= ' AND membership_type_id LIKE "' . $req->memis_category . '" ';
                    }else{
                        $where .= ' AND membership_type_id > 0 ';
                    }

                }

                if($req->memis_status > 0){ // status
                  
                    $join .= '  JOIN new_dbskms.tblmembership_status ON mem_status = membership_status_id ';
    
                    if($req->memis_status > 0 && $req->memis_status != '999'){
                        $where .= ' AND membership_status_id LIKE ' . $req->memis_status . ' ';
                    }else{
                        $where .= ' AND membership_status_id > 0 ';
                    }
                    
                    $deceased = '';
                }

                if($req->memis_educ > 0){ // educ 

                    $key = ($having != '') ? 'AND' : ' HAVING ';
                    $field .= ' , MIN(adp_highest) as minadp ';
                    $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
                    $having .= ' '.$key.' minadp = ' . $req->memis_educ . ' ';

                }

                if($req->memis_age > 0){ // age 

                        $key = ($having != '') ? 'AND' : 'HAVING';
                        $field .= ' ,(YEAR(CURDATE()) - YEAR(pp_date_of_birth)) AS age ';
                        $where .= ' AND pp_date_of_birth like \'%-%\' and pp_date_of_birth > 0 ';
                        
                        if($req->memis_age == 1){
                            $having .= ' '.$key.' age > 20 AND age < 31 ';
                        }else if($req->memis_age == 2){
                            $having .= ' '.$key.' age > 30 AND age < 41 ';
                        }else if($req->memis_age == 3){
                            $having .= ' '.$key.' age > 40 AND age < 51 ';
                        }else if($req->memis_age == 4){
                            $having .= ' '.$key.' age > 50 AND age < 61 ';
                        }else if($req->memis_age == 5){
                            $having .= ' '.$key.' age > 60 AND age < 71 ';
                        }else if($req->memis_age == 6){
                            $having .= ' '.$key.' age > 70 ';
                        }else{
                            $having .= ' '.$key.' age > 0 ';
                        }
                }
           
                if($req->memis_start_year > 0 && $req->memis_end_year){
                    
                    $key = ($having != '') ? 'AND' : ' HAVING ';
                    $field .= ' ,YEAR(mem_date_elected) AS year ';
                    $having .= ' '.$key. ' year BETWEEN '. $req->memis_start_year .' AND '. $req->memis_end_year .' '; 
                }

                $group_by .= ' GROUP BY pp_usr_id ';
                $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
                $join .= ' LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';
                $join .= ' JOIN new_dbskms.tblcities on city_id = emp_city ';
                                
                $sub_q .= 'SELECT city_id, count(mem_usr_id) as total, city_name AS label '. $field .
                'FROM new_dbskms.tblmembers '. 
                'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
                'WHERE usr_grp_id LIKE 3 '. $where . $deceased . $group_by . $having;

                $select .= 'SELECT count(total) as total, label, city_id as bar_id FROM '. 
                '( ' . $sub_q . ') as tmp GROUP BY city_id';

                return DB::select($select); 
                
            }
        }else if($req->radio_default == 'memis_sex'){ // sex default

            if($req->memis_sex == '999'){ 

                $where .= ' AND pp_sex > 0 ';

                if($req->memis_division > 0){ // division

                    $join .= ' JOIN new_dbskms.tbldivisions ON div_id = mem_div_id ';
        
                    if($req->memis_division > 0 && $req->memis_division != '999'){
                        $where .= ' AND mem_div_id LIKE "' . $req->memis_division . '" ';
                    }else{
                        $where .= ' AND mem_div_id > 0 ';
                    }
                }

                if($req->memis_region > 0 && $req->memis_region != '999'){
       
                    if($req->memis_province > 0 && $req->memis_province != '999'){
                        
                        if($req->memis_city > 0 && $req->memis_city != '999'){
                            $where .= ' AND emp_city LIKE '. $req->memis_city .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';
                        }else{
                            $where .= ' AND emp_province LIKE '. $req->memis_province .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';
                        }
                    }
                    else{
                        $where .= ' AND emp_region LIKE '. $req->memis_region .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';    
                    }

                }

                if($req->memis_country > 0 && $req->memis_country != '999'){

                    $where .= ' AND emp_country LIKE '. $req->memis_country .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';    
                
                }              

                if($req->memis_category > 0){ // category


                    $join .= ' JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';
    
                    if($req->memis_category > 0 && $req->memis_category != '999'){
                        $where .= ' AND membership_type_id LIKE "' . $req->memis_category . '" ';
                    }else{
                        $where .= ' AND membership_type_id > 0 ';
                    }

                }

                if($req->memis_status > 0){ // status
                  
                    $join .= '  JOIN new_dbskms.tblmembership_status ON mem_status = membership_status_id ';
    
                    if($req->memis_status > 0 && $req->memis_status != '999'){
                        $where .= ' AND membership_status_id LIKE ' . $req->memis_status . ' ';
                    }else{
                        $where .= ' AND membership_status_id > 0 ';
                    }
                    
                    $deceased = '';
                }

                if($req->memis_educ > 0){ // educ 

                    $key = ($having != '') ? 'AND' : ' HAVING ';
                    $field .= ' , MIN(adp_highest) as minadp ';
                    $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
                    $having .= ' '.$key.' minadp = ' . $req->memis_educ . ' ';

                }

                if($req->memis_age > 0){ // age 

                        $key = ($having != '') ? 'AND' : 'HAVING';
                        $field .= ' ,(YEAR(CURDATE()) - YEAR(pp_date_of_birth)) AS age ';
                        $where .= ' AND pp_date_of_birth like \'%-%\' and pp_date_of_birth > 0 ';
                        
                        if($req->memis_age == 1){
                            $having .= ' '.$key.' age > 20 AND age < 31 ';
                        }else if($req->memis_age == 2){
                            $having .= ' '.$key.' age > 30 AND age < 41 ';
                        }else if($req->memis_age == 3){
                            $having .= ' '.$key.' age > 40 AND age < 51 ';
                        }else if($req->memis_age == 4){
                            $having .= ' '.$key.' age > 50 AND age < 61 ';
                        }else if($req->memis_age == 5){
                            $having .= ' '.$key.' age > 60 AND age < 71 ';
                        }else if($req->memis_age == 6){
                            $having .= ' '.$key.' age > 70 ';
                        }else{
                            $having .= ' '.$key.' age > 0 ';
                        }
                }
           
                if($req->memis_start_year > 0 && $req->memis_end_year){
                    
                    $key = ($having != '') ? 'AND' : ' HAVING ';
                    $field .= ' ,YEAR(mem_date_elected) AS year ';
                    $having .= ' '.$key. ' year BETWEEN '. $req->memis_start_year .' AND '. $req->memis_end_year .' '; 
                }

                $group_by .= ' GROUP BY pp_usr_id ';
                $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
                $join .= ' JOIN new_dbskms.tblsex on s_id = pp_sex ';
                $join .= ' LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';
                                
                $sub_q .= 'SELECT s_id, count(mem_usr_id) as total, sex AS label '. $field .
                'FROM new_dbskms.tblmembers '. 
                'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
                'WHERE usr_grp_id LIKE 3 '. $where . $deceased . $group_by . $having;

                $select .= 'SELECT count(total) as total, label, s_id as bar_id FROM '. 
                '( ' . $sub_q . ') as tmp GROUP BY s_id';

                return DB::select($select);
                
            }
        }else if($req->radio_default == 'memis_category'){ // category default

            if($req->memis_category == '999'){ 

                $where .= ' AND membership_type_id > 0 ';

                if($req->memis_division > 0){ // division

                    $join .= ' JOIN new_dbskms.tbldivisions ON div_id = mem_div_id ';
        
                    if($req->memis_division > 0 && $req->memis_division != '999'){
                        $where .= ' AND mem_div_id LIKE "' . $req->memis_division . '" ';
                    }else{
                        $where .= ' AND mem_div_id > 0 ';
                    }
                }

                if($req->memis_region > 0 && $req->memis_region != '999'){
       
                    if($req->memis_province > 0 && $req->memis_province != '999'){
                        
                        if($req->memis_city > 0 && $req->memis_city != '999'){
                            $where .= ' AND emp_city LIKE '. $req->memis_city .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';
                        }else{
                            $where .= ' AND emp_province LIKE '. $req->memis_province .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';
                        }
                    }
                    else{
                        $where .= ' AND emp_region LIKE '. $req->memis_region .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';    
                    }

                }

                if($req->memis_country > 0 && $req->memis_country != '999'){

                    $where .= ' AND emp_country LIKE '. $req->memis_country .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';    
                
                }
                
                if($req->memis_sex > 0){ // sex

                    if($req->memis_sex > 0 && $req->memis_sex != '999'){
                        $where .= ' AND pp_sex LIKE "' . $req->memis_sex . '" ';
                    }else{
                        $where .= ' AND pp_sex > 0 ';
                    }
                }

                if($req->memis_status > 0){ // status
                  
                    $join .= '  JOIN new_dbskms.tblmembership_status ON mem_status = membership_status_id ';
    
                    if($req->memis_status > 0 && $req->memis_status != '999'){
                        $where .= ' AND membership_status_id LIKE ' . $req->memis_status . ' ';
                    }else{
                        $where .= ' AND membership_status_id > 0 ';
                    }
                    
                    $deceased = '';
                }

                if($req->memis_educ > 0){ // educ 

                    $key = ($having != '') ? 'AND' : ' HAVING ';
                    $field .= ' , MIN(adp_highest) as minadp ';
                    $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
                    $having .= ' '.$key.' minadp = ' . $req->memis_educ . ' ';

                }

                if($req->memis_age > 0){ // age 

                        $key = ($having != '') ? 'AND' : 'HAVING';
                        $field .= ' ,(YEAR(CURDATE()) - YEAR(pp_date_of_birth)) AS age ';
                        $where .= ' AND pp_date_of_birth like \'%-%\' and pp_date_of_birth > 0 ';
                        
                        if($req->memis_age == 1){
                            $having .= ' '.$key.' age > 20 AND age < 31 ';
                        }else if($req->memis_age == 2){
                            $having .= ' '.$key.' age > 30 AND age < 41 ';
                        }else if($req->memis_age == 3){
                            $having .= ' '.$key.' age > 40 AND age < 51 ';
                        }else if($req->memis_age == 4){
                            $having .= ' '.$key.' age > 50 AND age < 61 ';
                        }else if($req->memis_age == 5){
                            $having .= ' '.$key.' age > 60 AND age < 71 ';
                        }else if($req->memis_age == 6){
                            $having .= ' '.$key.' age > 70 ';
                        }else{
                            $having .= ' '.$key.' age > 0 ';
                        }
                }
           
                if($req->memis_start_year > 0 && $req->memis_end_year){
                    
                    $key = ($having != '') ? 'AND' : ' HAVING ';
                    $field .= ' ,YEAR(mem_date_elected) AS year ';
                    $having .= ' '.$key. ' year BETWEEN '. $req->memis_start_year .' AND '. $req->memis_end_year .' '; 
                }

                $group_by .= ' GROUP BY pp_usr_id ';
                $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
                $join .= ' JOIN new_dbskms.tblmembership_types on membership_type_id = mem_type ';
                $join .= ' LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';
                                
                $sub_q .= 'SELECT membership_type_id, count(mem_usr_id) as total, membership_type_name AS label '. $field .
                'FROM new_dbskms.tblmembers '. 
                'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
                'WHERE usr_grp_id LIKE 3 '. $where . $deceased . $group_by . $having;

                $select .= 'SELECT count(total) as total, label, membership_type_id as bar_id FROM '. 
                '( ' . $sub_q . ') as tmp GROUP BY membership_type_id';

                return DB::select($select); 
            }
        }else if($req->radio_default == 'memis_status'){ // status default

            if($req->memis_status == '999'){ 

                $deceased = '';
                $where .= ' AND membership_status_id > 0 ';

                if($req->memis_division > 0){ // division

                    $join .= ' JOIN new_dbskms.tbldivisions ON div_id = mem_div_id ';
        
                    if($req->memis_division > 0 && $req->memis_division != '999'){
                        $where .= ' AND mem_div_id LIKE "' . $req->memis_division . '" ';
                    }else{
                        $where .= ' AND mem_div_id > 0 ';
                    }
                }
                  
                if($req->memis_region > 0 && $req->memis_region != '999'){
       
                    if($req->memis_province > 0 && $req->memis_province != '999'){
                        
                        if($req->memis_city > 0 && $req->memis_city != '999'){
                            $where .= ' AND emp_city LIKE '. $req->memis_city .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';
                        }else{
                            $where .= ' AND emp_province LIKE '. $req->memis_province .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';
                        }
                    }
                    else{
                        $where .= ' AND emp_region LIKE '. $req->memis_region .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';    
                    }

                }

                if($req->memis_country > 0 && $req->memis_country != '999'){

                    $where .= ' AND emp_country LIKE '. $req->memis_country .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';    
                
                }
                
                if($req->memis_sex > 0){ // sex

                    if($req->memis_sex > 0 && $req->memis_sex != '999'){
                        $where .= ' AND pp_sex LIKE "' . $req->memis_sex . '" ';
                    }else{
                        $where .= ' AND pp_sex > 0 ';
                    }
                }

                if($req->memis_category > 0){ // category


                    $join .= ' JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';
    
                    if($req->memis_category > 0 && $req->memis_category != '999'){
                        $where .= ' AND membership_type_id LIKE "' . $req->memis_category . '" ';
                    }else{
                        $where .= ' AND membership_type_id > 0 ';
                    }

                }

                if($req->memis_educ > 0){ // educ 

                    $key = ($having != '') ? 'AND' : ' HAVING ';
                    $field .= ' , MIN(adp_highest) as minadp ';
                    $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
                    $having .= ' '.$key.' minadp = ' . $req->memis_educ . ' ';

                }

                if($req->memis_age > 0){ // age 

                        $key = ($having != '') ? 'AND' : 'HAVING';
                        $field .= ' ,(YEAR(CURDATE()) - YEAR(pp_date_of_birth)) AS age ';
                        $where .= ' AND pp_date_of_birth like \'%-%\' and pp_date_of_birth > 0 ';
                        
                        if($req->memis_age == 1){
                            $having .= ' '.$key.' age > 20 AND age < 31 ';
                        }else if($req->memis_age == 2){
                            $having .= ' '.$key.' age > 30 AND age < 41 ';
                        }else if($req->memis_age == 3){
                            $having .= ' '.$key.' age > 40 AND age < 51 ';
                        }else if($req->memis_age == 4){
                            $having .= ' '.$key.' age > 50 AND age < 61 ';
                        }else if($req->memis_age == 5){
                            $having .= ' '.$key.' age > 60 AND age < 71 ';
                        }else if($req->memis_age == 6){
                            $having .= ' '.$key.' age > 70 ';
                        }else{
                            $having .= ' '.$key.' age > 0 ';
                        }
                }
           
                if($req->memis_start_year > 0 && $req->memis_end_year){
                    
                    $key = ($having != '') ? 'AND' : ' HAVING ';
                    $field .= ' ,YEAR(mem_date_elected) AS year ';
                    $having .= ' '.$key. ' year BETWEEN '. $req->memis_start_year .' AND '. $req->memis_end_year .' '; 
                }

                $group_by .= ' GROUP BY pp_usr_id ';
                $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
                $join .= ' JOIN new_dbskms.tblmembership_status on membership_status_id = mem_status ';
                $join .= ' LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';
                                
                $sub_q .= 'SELECT membership_status_id, count(mem_usr_id) as total, membership_status_name AS label '. $field .
                'FROM new_dbskms.tblmembers '. 
                'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
                'WHERE usr_grp_id LIKE 3 '. $where . $deceased . $group_by . $having;

                $select .= 'SELECT count(total) as total, label, membership_status_id as bar_id FROM '. 
                '( ' . $sub_q . ') as tmp GROUP BY membership_status_id';

                return DB::select($select);

                 
            }
        }else if($req->radio_default == 'memis_educ'){ // educ default

            if($req->memis_educ == '999'){ 

                
                $field .= ' , MIN(adp_highest) as minadp ';

                if($req->memis_division > 0){ // division

                    $join .= ' JOIN new_dbskms.tbldivisions ON div_id = mem_div_id ';
        
                    if($req->memis_division > 0 && $req->memis_division != '999'){
                        $where .= ' AND mem_div_id LIKE "' . $req->memis_division . '" ';
                    }else{
                        $where .= ' AND mem_div_id > 0 ';
                    }
                }
                    
                if($req->memis_region > 0 && $req->memis_region != '999'){
       
                    if($req->memis_province > 0 && $req->memis_province != '999'){
                        
                        if($req->memis_city > 0 && $req->memis_city != '999'){
                            $where .= ' AND emp_city LIKE '. $req->memis_city .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';
                        }else{
                            $where .= ' AND emp_province LIKE '. $req->memis_province .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';
                        }
                    }
                    else{
                        $where .= ' AND emp_region LIKE '. $req->memis_region .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';    
                    }

                }

                if($req->memis_country > 0 && $req->memis_country != '999'){

                    $where .= ' AND emp_country LIKE '. $req->memis_country .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';    
                
                }
                
                if($req->memis_sex > 0){ // sex

                    if($req->memis_sex > 0 && $req->memis_sex != '999'){
                        $where .= ' AND pp_sex LIKE "' . $req->memis_sex . '" ';
                    }else{
                        $where .= ' AND pp_sex > 0 ';
                    }
                }

                if($req->memis_category > 0){ // category


                    $join .= ' JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';
    
                    if($req->memis_category > 0 && $req->memis_category != '999'){
                        $where .= ' AND membership_type_id LIKE "' . $req->memis_category . '" ';
                    }else{
                        $where .= ' AND membership_type_id > 0 ';
                    }

                }

                if($req->memis_status > 0){ // status
                  
                    $join .= '  JOIN new_dbskms.tblmembership_status ON mem_status = membership_status_id ';
    
                    if($req->memis_status > 0 && $req->memis_status != '999'){
                        $where .= ' AND membership_status_id LIKE ' . $req->memis_status . ' ';
                    }else{
                        $where .= ' AND membership_status_id > 0 ';
                    }
                    
                    $deceased = '';
                }

                if($req->memis_age > 0){ // age 

                        $key = ($having != '') ? 'AND' : 'HAVING';
                        $field .= ' ,(YEAR(CURDATE()) - YEAR(pp_date_of_birth)) AS age ';
                        $where .= ' AND pp_date_of_birth like \'%-%\' and pp_date_of_birth > 0 ';
                        
                        if($req->memis_age == 1){
                            $having .= ' '.$key.' age > 20 AND age < 31 ';
                        }else if($req->memis_age == 2){
                            $having .= ' '.$key.' age > 30 AND age < 41 ';
                        }else if($req->memis_age == 3){
                            $having .= ' '.$key.' age > 40 AND age < 51 ';
                        }else if($req->memis_age == 4){
                            $having .= ' '.$key.' age > 50 AND age < 61 ';
                        }else if($req->memis_age == 5){
                            $having .= ' '.$key.' age > 60 AND age < 71 ';
                        }else if($req->memis_age == 6){
                            $having .= ' '.$key.' age > 70 ';
                        }else{
                            $having .= ' '.$key.' age > 0 ';
                        }
                }
           
                if($req->memis_start_year > 0 && $req->memis_end_year){
                    
                    $key = ($having != '') ? 'AND' : ' HAVING ';
                    $field .= ' ,YEAR(mem_date_elected) AS year ';
                    $having .= ' '.$key. ' year BETWEEN '. $req->memis_start_year .' AND '. $req->memis_end_year .' '; 
                }

                $group_by .= ' GROUP BY pp_usr_id ';
                $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
                $join .= ' JOIN new_dbskms.tblacademic_degree_profiles on adp_usr_id = usr_id ';
                $join .= ' JOIN new_dbskms.tbldegree_types on deg_id = adp_highest ';
                $join .= ' LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';
                                
                $sub_q .= 'SELECT deg_id, count(mem_usr_id) as total, deg_name AS label '. $field .
                'FROM new_dbskms.tblmembers '. 
                'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
                'WHERE usr_grp_id LIKE 3 '. $where . $deceased . $group_by . $having;

                $select .= 'SELECT count(total) as total, label, deg_id as bar_id FROM '. 
                '( ' . $sub_q . ') as tmp GROUP BY deg_id';

                return DB::select($select);
            }
        }else if($req->radio_default == 'memis_country'){ // country default

            if($req->memis_country == '999' ){ 

                $where .= 'AND emp_id IN (SELECT MAX(emp_id) AS emp_id '.
                'FROM new_dbskms.tblemployments '.
                'GROUP by emp_usr_id '.
                'ORDER by emp_id DESC)';

                if($req->memis_region > 0 && $req->memis_region != '999'){
       
                    if($req->memis_province > 0 && $req->memis_province != '999'){
                        
                        if($req->memis_city > 0 && $req->memis_city != '999'){
                            $where .= ' AND emp_city LIKE '. $req->memis_city .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';
                        }else{
                            $where .= ' AND emp_province LIKE '. $req->memis_province .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';
                        }
                    }
                    else{
                        $where .= ' AND emp_region LIKE '. $req->memis_region .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';    
                    }

                }

                if($req->memis_sex > 0){ // sex

                    if($req->memis_sex > 0 && $req->memis_sex != '999'){
                        $where .= ' AND pp_sex LIKE "' . $req->memis_sex . '" ';
                    }else{
                        $where .= ' AND pp_sex > 0 ';
                    }
                }

                if($req->memis_category > 0){ // category


                    $join .= ' JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';
    
                    if($req->memis_category > 0 && $req->memis_category != '999'){
                        $where .= ' AND membership_type_id LIKE "' . $req->memis_category . '" ';
                    }else{
                        $where .= ' AND membership_type_id > 0 ';
                    }

                }

                if($req->memis_status > 0){ // status
                  
                    $join .= '  JOIN new_dbskms.tblmembership_status ON mem_status = membership_status_id ';
    
                    if($req->memis_status > 0 && $req->memis_status != '999'){
                        $where .= ' AND membership_status_id LIKE ' . $req->memis_status . ' ';
                    }else{
                        $where .= ' AND membership_status_id > 0 ';
                    }
                    
                    $deceased = '';
                }

                if($req->memis_educ > 0){ // educ 

                    $key = ($having != '') ? 'AND' : ' HAVING ';
                    $field .= ' , MIN(adp_highest) as minadp ';
                    $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
                    $having .= ' '.$key.' minadp = ' . $req->memis_educ . ' ';

                }

                if($req->memis_age > 0){ // age 

                        $key = ($having != '') ? 'AND' : 'HAVING';
                        $field .= ' ,(YEAR(CURDATE()) - YEAR(pp_date_of_birth)) AS age ';
                        $where .= ' AND pp_date_of_birth like \'%-%\' and pp_date_of_birth > 0 ';
                        
                        if($req->memis_age == 1){
                            $having .= ' '.$key.' age > 20 AND age < 31 ';
                        }else if($req->memis_age == 2){
                            $having .= ' '.$key.' age > 30 AND age < 41 ';
                        }else if($req->memis_age == 3){
                            $having .= ' '.$key.' age > 40 AND age < 51 ';
                        }else if($req->memis_age == 4){
                            $having .= ' '.$key.' age > 50 AND age < 61 ';
                        }else if($req->memis_age == 5){
                            $having .= ' '.$key.' age > 60 AND age < 71 ';
                        }else if($req->memis_age == 6){
                            $having .= ' '.$key.' age > 70 ';
                        }else{
                            $having .= ' '.$key.' age > 0 ';
                        }
                }
           
                if($req->memis_start_year > 0 && $req->memis_end_year){
                    
                    $key = ($having != '') ? 'AND' : ' HAVING ';
                    $field .= ' ,YEAR(mem_date_elected) AS year ';
                    $having .= ' '.$key. ' year BETWEEN '. $req->memis_start_year .' AND '. $req->memis_end_year .' '; 
                }

                $group_by .= ' GROUP BY pp_usr_id ';
                                
                $sub_q .= 'SELECT country_id, count(mem_usr_id) as total, country_name AS label '. $field .
                'FROM new_dbskms.tblpersonal_profiles '. 
                'INNER JOIN new_dbskms.tblusers ON usr_id = pp_usr_id '.
                'LEFT JOIN new_dbskms.tblemployments as pp ON emp_usr_id = pp_usr_id '.
                'LEFT JOIN new_dbskms.tblmembers ON mem_usr_id = pp_usr_id '.
                'LEFT JOIN new_dbskms.tblcountries on country_id = emp_country '.
                'WHERE usr_grp_id LIKE 3 AND mem_status IS NOT NULL AND emp_country > 0 '. 
                $where . $deceased . $group_by . $having;

                $select .= 'SELECT count(total) as total, label, country_id as bar_id FROM '. 
                '( ' . $sub_q . ') as tmp GROUP BY country_id';

                return DB::select($select);
                
            }
        }else if($req->radio_default == 'memis_age'){ // age default + no bar id 

            if($req->memis_age == '999'){     
                
                if($req->memis_division > 0){ // division

                    $join .= ' JOIN new_dbskms.tbldivisions ON div_id = mem_div_id ';
        
                    if($req->memis_division > 0 && $req->memis_division != '999'){
                        $where .= ' AND mem_div_id LIKE "' . $req->memis_division . '" ';
                    }else{
                        $where .= ' AND mem_div_id > 0 ';
                    }
                }

                if($req->memis_region > 0 && $req->memis_region != '999'){
   
                    if($req->memis_province > 0 && $req->memis_province != '999'){
                        
                        if($req->memis_city > 0 && $req->memis_city != '999'){
                            $where .= ' AND emp_city LIKE '. $req->memis_city .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';
                        }else{
                            $where .= ' AND emp_province LIKE '. $req->memis_province .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';
                        }
                    }
                    else{
                        $where .= ' AND emp_region LIKE '. $req->memis_region .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';    
                    }

                }

                if($req->memis_country > 0 && $req->memis_country != '999'){

                    $where .= ' AND emp_country LIKE '. $req->memis_country .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';    
                
                }
                
                if($req->memis_sex > 0){ // sex

                    if($req->memis_sex > 0 && $req->memis_sex != '999'){
                        $where .= ' AND pp_sex LIKE "' . $req->memis_sex . '" ';
                    }else{
                        $where .= ' AND pp_sex > 0 ';
                    }
                }

                if($req->memis_category > 0){ // category


                    $join .= ' JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';
    
                    if($req->memis_category > 0 && $req->memis_category != '999'){
                        $where .= ' AND membership_type_id LIKE "' . $req->memis_category . '" ';
                    }else{
                        $where .= ' AND membership_type_id > 0 ';
                    }

                }

                if($req->memis_status > 0){ // status
                  
                    $join .= '  JOIN new_dbskms.tblmembership_status ON mem_status = membership_status_id ';
    
                    if($req->memis_status > 0 && $req->memis_status != '999'){
                        $where .= ' AND membership_status_id LIKE ' . $req->memis_status . ' ';
                    }else{
                        $where .= ' AND membership_status_id > 0 ';
                    }
                    
                    $deceased = '';
                }

                if($req->memis_educ > 0){ // educ 

                    $key = ($having != '') ? 'AND' : ' HAVING ';
                    $field .= ' , MIN(adp_highest) as minadp ';
                    $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
                    $having .= ' '.$key.' minadp = ' . $req->memis_educ . ' ';

                }
       
                if($req->memis_start_year > 0 && $req->memis_end_year){
                    
                    $key = ($having != '') ? 'AND' : ' HAVING ';
                    $field .= ' ,YEAR(mem_date_elected) AS year ';
                    $having .= ' '.$key. ' year BETWEEN '. $req->memis_start_year .' AND '. $req->memis_end_year .' '; 
                }

                $group_by .= ' GROUP BY pp_usr_id ';
                $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
                $join .= ' LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';
    
                $sub_q .= 'SELECT count(pp_usr_id) AS total, '.
                'CASE  '.
                'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 20 AND 31 then "1" '.
                'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 30 AND 41 then "2" '.
                'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 40 AND 51 then "3" '.
                'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 50 AND 61 then "4" '.
                'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 60 AND 71 then "5" '.
                'ELSE "6" END AS "range" '. $field .
                'FROM new_dbskms.tblpersonal_profiles '.
                'INNER JOIN new_dbskms.tblusers ON usr_id = pp_usr_id '.
                'LEFT JOIN new_dbskms.tblmembers ON mem_usr_id = usr_id '. 
                'WHERE pp_date_of_birth LIKE \'%-%\' '. 
                'AND pp_date_of_birth > 0 '. 
                'AND usr_grp_id LIKE 3 '. $where . $deceased . $group_by . $having;
    

                 $select .= 'SELECT count(total) AS total, tmp.range as bar_id, '. 
                 '(SELECT age_range FROM new_dbskms.tblage_ranges WHERE age_id = tmp.range) AS label FROM '. 
                 '( ' . $sub_q . ') AS tmp GROUP BY tmp.range';
 
                 return DB::select($select);
    
                
            }
        }else if($req->radio_default == 'memis_island'){ // island default + no bar id
            if($req->memis_island == '999'){

                if($req->memis_division > 0){ // division

                    $join .= ' JOIN new_dbskms.tbldivisions ON div_id = mem_div_id ';
        
                    if($req->memis_division > 0 && $req->memis_division != '999'){
                        $where .= ' AND mem_div_id LIKE "' . $req->memis_division . '" ';
                    }else{
                        $where .= ' AND mem_div_id > 0 ';
                    }
                }
                
                if($req->memis_sex > 0){ // sex

                    if($req->memis_sex > 0 && $req->memis_sex != '999'){
                        $where .= ' AND pp_sex LIKE "' . $req->memis_sex . '" ';
                    }else{
                        $where .= ' AND pp_sex > 0 ';
                    }
                }

                if($req->memis_category > 0){ // category


                    $join .= ' JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';
    
                    if($req->memis_category > 0 && $req->memis_category != '999'){
                        $where .= ' AND membership_type_id LIKE "' . $req->memis_category . '" ';
                    }else{
                        $where .= ' AND membership_type_id > 0 ';
                    }

                }

                if($req->memis_status > 0){ // status
                  
                    $join .= '  JOIN new_dbskms.tblmembership_status ON mem_status = membership_status_id ';
    
                    if($req->memis_status > 0 && $req->memis_status != '999'){
                        $where .= ' AND membership_status_id LIKE ' . $req->memis_status . ' ';
                    }else{
                        $where .= ' AND membership_status_id > 0 ';
                    }
                    
                    $deceased = '';
                }

                if($req->memis_educ > 0){ // educ 

                    $key = ($having != '') ? 'AND' : ' HAVING ';
                    $field .= ' , MIN(adp_highest) as minadp ';
                    $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
                    $having .= ' '.$key.' minadp = ' . $req->memis_educ . ' ';

                }

                if($req->memis_age > 0){ // age 

                        $key = ($having != '') ? 'AND' : 'HAVING';
                        $field .= ' ,(YEAR(CURDATE()) - YEAR(pp_date_of_birth)) AS age ';
                        $where .= ' AND pp_date_of_birth like \'%-%\' and pp_date_of_birth > 0 ';
                        
                        if($req->memis_age == 1){
                            $having .= ' '.$key.' age > 20 AND age < 31 ';
                        }else if($req->memis_age == 2){
                            $having .= ' '.$key.' age > 30 AND age < 41 ';
                        }else if($req->memis_age == 3){
                            $having .= ' '.$key.' age > 40 AND age < 51 ';
                        }else if($req->memis_age == 4){
                            $having .= ' '.$key.' age > 50 AND age < 61 ';
                        }else if($req->memis_age == 5){
                            $having .= ' '.$key.' age > 60 AND age < 71 ';
                        }else if($req->memis_age == 6){
                            $having .= ' '.$key.' age > 70 ';
                        }else{
                            $having .= ' '.$key.' age > 0 ';
                        }
                }
           
                if($req->memis_start_year > 0 && $req->memis_end_year){
                    
                    $key = ($having != '') ? 'AND' : ' HAVING ';
                    $field .= ' ,YEAR(mem_date_elected) AS year ';
                    $having .= ' '.$key. ' year BETWEEN '. $req->memis_start_year .' AND '. $req->memis_end_year .' '; 
                }

                $group_by .= ' GROUP BY pp_usr_id ';
                $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
                $join .= ' LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';
                $join .= ' JOIN new_dbskms.tblregions ON region_id = emp_region  ';
                                
                $sub_q .= 'SELECT region_id, count(mem_usr_id) AS total, region_group, '. 
                'CASE WHEN region_group = "1" THEN "Luzon"  '.
                'WHEN region_group ="2" THEN "Visayas" '.
                'ELSE "Mindanao" END as island '. $field .
                'FROM new_dbskms.tblmembers '. 
                'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
                'WHERE usr_grp_id LIKE 3 '. $where . $deceased . $group_by . $having;

                $select .= 'SELECT count(total) AS total, tmp.island AS label, region_group as bar_id FROM '. 
                '( ' . $sub_q . ') as tmp GROUP BY bar_id';

                return DB::select($select);
      
            }
        }
    }

    // stack graph
    static function do_stack_graph($req){
        $result_array = array();

        if($req->radio_default == 'memis_region'){ // region default
            
            if($req->memis_division == '999'){

                $query = DB::connection('dbskms')->table('tbldivisions')
                ->select('div_id','div_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->div_id => array($row->div_name => Member::get_all_region($row->div_id, $req)));
                }
            }else if($req->memis_sex == '999'){

                $query = DB::connection('dbskms')->table('tblsex')
                ->select('s_id','sex')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->s_id => array($row->sex => Member::get_all_region($row->s_id, $req)));
                }
            }else if($req->memis_category == '999'){

                $query = DB::connection('dbskms')->table('tblmembership_types')
                ->select('membership_type_id','membership_type_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->membership_type_id => array($row->membership_type_name => Member::get_all_region($row->membership_type_id, $req)));
                }
            }else if($req->memis_status == '999'){

                $query = DB::connection('dbskms')->table('tblmembership_status')
                ->select('membership_status_id','membership_status_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->membership_status_id => array($row->membership_status_name => Member::get_all_region($row->membership_status_id, $req)));
                }
            }else if($req->memis_educ == '999'){

                $query = DB::connection('dbskms')->table('tbldegree_types')
                ->select('deg_id','deg_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->deg_id => array($row->deg_name => Member::get_all_region($row->deg_id, $req)));
                }
            }
            else if($req->memis_age == '999'){ 

                $query = DB::connection('dbskms')->table('tblage_ranges')
                ->select('age_id','age_range')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->age_id => array($row->age_range => Member::get_all_region($row->age_id, $req)));
                }
            }
        }

        if($req->radio_default == 'memis_province'){ // province default
            
            if($req->memis_division == '999'){

                $query = DB::connection('dbskms')->table('tbldivisions')
                ->select('div_id','div_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->div_id => array($row->div_name => Member::get_all_province($row->div_id, $req)));
                }
            }else if($req->memis_sex == '999'){

                $query = DB::connection('dbskms')->table('tblsex')
                ->select('s_id','sex')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->s_id => array($row->sex => Member::get_all_province($row->s_id, $req)));
                }
            }else if($req->memis_category == '999'){

                $query = DB::connection('dbskms')->table('tblmembership_types')
                ->select('membership_type_id','membership_type_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->membership_type_id => array($row->membership_type_name => Member::get_all_province($row->membership_type_id, $req)));
                }
            }else if($req->memis_status == '999'){

                $query = DB::connection('dbskms')->table('tblmembership_status')
                ->select('membership_status_id','membership_status_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->membership_status_id => array($row->membership_status_name => Member::get_all_province($row->membership_status_id, $req)));
                }
            }else if($req->memis_educ == '999'){

                $query = DB::connection('dbskms')->table('tbldegree_types')
                ->select('deg_id','deg_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->deg_id => array($row->deg_name => Member::get_all_province($row->deg_id, $req)));
                }
            }
            else if($req->memis_age == '999'){ 

                $query = DB::connection('dbskms')->table('tblage_ranges')
                ->select('age_id','age_range')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->age_id => array($row->age_range => Member::get_all_province($row->age_id, $req)));
                }
            }
        }

        if($req->radio_default == 'memis_city'){ // city default
            
            if($req->memis_division == '999'){

                $query = DB::connection('dbskms')->table('tbldivisions')
                ->select('div_id','div_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->div_id => array($row->div_name => Member::get_all_city($row->div_id, $req)));
                }
            }else if($req->memis_sex == '999'){

                $query = DB::connection('dbskms')->table('tblsex')
                ->select('s_id','sex')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->s_id => array($row->sex => Member::get_all_city($row->s_id, $req)));
                }
            }else if($req->memis_category == '999'){

                $query = DB::connection('dbskms')->table('tblmembership_types')
                ->select('membership_type_id','membership_type_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->membership_type_id => array($row->membership_type_name => Member::get_all_city($row->membership_type_id, $req)));
                }
            }else if($req->memis_status == '999'){

                $query = DB::connection('dbskms')->table('tblmembership_status')
                ->select('membership_status_id','membership_status_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->membership_status_id => array($row->membership_status_name => Member::get_all_city($row->membership_status_id, $req)));
                }
            }else if($req->memis_educ == '999'){

                $query = DB::connection('dbskms')->table('tbldegree_types')
                ->select('deg_id','deg_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->deg_id => array($row->deg_name => Member::get_all_city($row->deg_id, $req)));
                }
            }
            else if($req->memis_age == '999'){ 

                $query = DB::connection('dbskms')->table('tblage_ranges')
                ->select('age_id','age_range')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->age_id => array($row->age_range => Member::get_all_city($row->age_id, $req)));
                }
            }
        }

        if($req->radio_default == 'memis_division'){ // division default

            if($req->memis_region == '999'){

                $query = DB::connection('dbskms')->table('tblregions')
                ->select('region_id','region_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->region_id => array($row->region_name => Member::get_all_division($row->region_id, $req)));
                }

             
            }else if($req->memis_province == '999'){

                $query = DB::connection('dbskms')->table('tblprovinces')
                ->select('province_id','province_name')
                ->where('province_region_id', $req->memis_region)
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->province_id => array($row->province_name => Member::get_all_division($row->province_id, $req)));
                }

            }else if($req->memis_city == '999'){

                $query = DB::connection('dbskms')->table('tblcities')
                ->select('city_id','city_name')
                ->where('city_province_id', $req->memis_province)
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->city_id => array($row->city_name => Member::get_all_division($row->city_id, $req)));
                }

            }else if($req->memis_sex == '999'){

                $query = DB::connection('dbskms')->table('tblsex')
                ->select('s_id','sex')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->s_id => array($row->sex => Member::get_all_division($row->s_id, $req)));
                }
            }else if($req->memis_category == '999'){

                $query = DB::connection('dbskms')->table('tblmembership_types')
                ->select('membership_type_id','membership_type_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->membership_type_id => array($row->membership_type_name => Member::get_all_division($row->membership_type_id, $req)));
                }

            }else if($req->memis_status == '999'){

                $query = DB::connection('dbskms')->table('tblmembership_status')
                ->select('membership_status_id','membership_status_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->membership_status_id => array($row->membership_status_name => Member::get_all_division($row->membership_status_id, $req)));
                }
            }else if($req->memis_country == '999'){

                // $query = DB::connection('dbskms')->table('tblcountries')
                // ->select('country_id','country_name')
                // ->get();
                
                // foreach($query as $row){  
                //     $result_array[] = array($row->country_id => array($row->country_name => Member::get_all_division($row->country_id, $req)));
                // }


            }else if($req->memis_educ == '999'){

                $query = DB::connection('dbskms')->table('tbldegree_types')
                ->select('deg_id','deg_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->deg_id => array($row->deg_name => Member::get_all_division($row->deg_id, $req)));
                }
            }
            else if($req->memis_age == '999'){ 

                $query = DB::connection('dbskms')->table('tblage_ranges')
                ->select('age_id','age_range')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->age_id => array($row->age_range => Member::get_all_division($row->age_id, $req)));
                }
            }

        }

        if($req->radio_default == 'memis_category'){ // category default

            if($req->memis_division == '999'){

                $query = DB::connection('dbskms')->table('tbldivisions')
                ->select('div_id','div_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->div_id => array($row->div_name => Member::get_all_category($row->div_id, $req)));
                }
            }else if($req->memis_region == '999'){

                $query = DB::connection('dbskms')->table('tblregions')
                ->select('region_id','region_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->region_id => array($row->region_name => Member::get_all_category($row->region_id, $req)));
                }

            }else if($req->memis_province == '999'){

                $query = DB::connection('dbskms')->table('tblprovinces')
                ->select('province_id','province_name')
                ->where('province_region_id', $req->memis_region)
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->province_id => array($row->province_name => Member::get_all_category($row->province_id, $req)));
                }

            }else if($req->memis_city == '999'){

                $query = DB::connection('dbskms')->table('tblcities')
                ->select('city_id','city_name')
                ->where('city_province_id', $req->memis_province)
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->city_id => array($row->city_name => Member::get_all_category($row->city_id, $req)));
                }

            }else if($req->memis_sex == '999'){

                $query = DB::connection('dbskms')->table('tblsex')
                ->select('s_id','sex')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->s_id => array($row->sex => Member::get_all_category($row->s_id, $req)));
                }
            }else if($req->memis_status == '999'){

                $query = DB::connection('dbskms')->table('tblmembership_status')
                ->select('membership_status_id','membership_status_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->membership_status_id => array($row->membership_status_name => Member::get_all_category($row->membership_status_id, $req)));
                }
            }else if($req->memis_educ == '999'){

                $query = DB::connection('dbskms')->table('tbldegree_types')
                ->select('deg_id','deg_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->deg_id => array($row->deg_name => Member::get_all_category($row->deg_id, $req)));
                }
            }
            else if($req->memis_age == '999'){

                $query = DB::connection('dbskms')->table('tblage_ranges')
                ->select('age_id','age_range')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->age_id => array($row->age_range => Member::get_all_category($row->age_id, $req)));
                }
            }

        }

        if($req->radio_default == 'memis_status'){ // status default

            if($req->memis_category == '999'){

                $query = DB::connection('dbskms')->table('tblmembership_types')
                ->select('membership_type_id','membership_type_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->membership_type_id => array($row->membership_type_name => Member::get_all_status($row->membership_type_id, $req)));
                }
            }else if($req->memis_division == '999'){

                $query = DB::connection('dbskms')->table('tbldivisions')
                ->select('div_id','div_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->div_id => array($row->div_name => Member::get_all_status($row->div_id, $req)));
                }
            }else if($req->memis_region == '999'){

                $query = DB::connection('dbskms')->table('tblregions')
                ->select('region_id','region_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->region_id => array($row->region_name => Member::get_all_status($row->region_id, $req)));
                }

            }else if($req->memis_province == '999'){

                $query = DB::connection('dbskms')->table('tblprovinces')
                ->select('province_id','province_name')
                ->where('province_region_id', $req->memis_region)
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->province_id => array($row->province_name => Member::get_all_status($row->province_id, $req)));
                }

            }else if($req->memis_city == '999'){

                $query = DB::connection('dbskms')->table('tblcities')
                ->select('city_id','city_name')
                ->where('city_province_id', $req->memis_province)
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->city_id => array($row->city_name => Member::get_all_status($row->city_id, $req)));
                }

            }else if($req->memis_sex == '999'){

                $query = DB::connection('dbskms')->table('tblsex')
                ->select('s_id','sex')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->s_id => array($row->sex => Member::get_all_status($row->s_id, $req)));
                }
            }else if($req->memis_category == '999'){

                $query = DB::connection('dbskms')->table('tblmembership_types')
                ->select('membership_type_id','membership_type_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->membership_type_id => array($row->membership_type_name => Member::get_all_status($row->membership_type_id, $req)));
                }
            }else if($req->memis_educ == '999'){

                $query = DB::connection('dbskms')->table('tbldegree_types')
                ->select('deg_id','deg_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->deg_id => array($row->deg_name => Member::get_all_status($row->deg_id, $req)));
                }
            }
            else if($req->memis_age == '999'){

                $query = DB::connection('dbskms')->table('tblage_ranges')
                ->select('age_id','age_range')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->age_id => array($row->age_range => Member::get_all_status($row->age_id, $req)));
                }
            }
        }

        if($req->radio_default == 'memis_sex'){ // sex default

            if($req->memis_category == '999'){

                $query = DB::connection('dbskms')->table('tblmembership_types')
                ->select('membership_type_id','membership_type_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->membership_type_id => array($row->membership_type_name => Member::get_all_sex($row->membership_type_id, $req)));
                }
            }else if($req->memis_status == '999'){

                $query = DB::connection('dbskms')->table('tblmembership_status')
                ->select('membership_status_id','membership_status_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->membership_status_id => array($row->membership_status_name => Member::get_all_sex($row->membership_status_id, $req)));
                }
            }else if($req->memis_division == '999'){

                $query = DB::connection('dbskms')->table('tbldivisions')
                ->select('div_id','div_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->div_id => array($row->div_name => Member::get_all_sex($row->div_id, $req)));
                }
            }else if($req->memis_region == '999'){

                $query = DB::connection('dbskms')->table('tblregions')
                ->select('region_id','region_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->region_id => array($row->region_name => Member::get_all_sex($row->region_id, $req)));
                }

            }else if($req->memis_province == '999'){

                $query = DB::connection('dbskms')->table('tblprovinces')
                ->select('province_id','province_name')
                ->where('province_region_id', $req->memis_region)
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->province_id => array($row->province_name => Member::get_all_sex($row->province_id, $req)));
                }

            }else if($req->memis_city == '999'){

                $query = DB::connection('dbskms')->table('tblcities')
                ->select('city_id','city_name')
                ->where('city_province_id', $req->memis_province)
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->city_id => array($row->city_name => Member::get_all_sex($row->city_id, $req)));
                }

            }else if($req->memis_educ == '999'){

                $query = DB::connection('dbskms')->table('tbldegree_types')
                ->select('deg_id','deg_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->deg_id => array($row->deg_name => Member::get_all_sex($row->deg_id, $req)));
                }
            }
            else if($req->memis_age == '999'){

                $query = DB::connection('dbskms')->table('tblage_ranges')
                ->select('age_id','age_range')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->age_id => array($row->age_range => Member::get_all_sex($row->age_id, $req)));
                }
            }
        }

        if($req->radio_default == 'memis_educ'){ // educ default
            if($req->memis_category == '999'){

                $query = DB::connection('dbskms')->table('tblmembership_types')
                ->select('membership_type_id','membership_type_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->membership_type_id => array($row->membership_type_name => Member::get_all_educ($row->membership_type_id, $req)));
                }
            }else if($req->memis_status == '999'){

                $query = DB::connection('dbskms')->table('tblmembership_status')
                ->select('membership_status_id','membership_status_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->membership_status_id => array($row->membership_status_name => Member::get_all_educ($row->membership_status_id, $req)));
                }
            }if($req->memis_division == '999'){

                $query = DB::connection('dbskms')->table('tbldivisions')
                ->select('div_id','div_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->div_id => array($row->div_name => Member::get_all_educ($row->div_id, $req)));
                }
            }else if($req->memis_region == '999'){

                $query = DB::connection('dbskms')->table('tblregions')
                ->select('region_id','region_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->region_id => array($row->region_name => Member::get_all_educ($row->region_id, $req)));
                }

            }else if($req->memis_province == '999'){

                $query = DB::connection('dbskms')->table('tblprovinces')
                ->select('province_id','province_name')
                ->where('province_region_id', $req->memis_region)
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->province_id => array($row->province_name => Member::get_all_educ($row->province_id, $req)));
                }

            }else if($req->memis_city == '999'){

                $query = DB::connection('dbskms')->table('tblcities')
                ->select('city_id','city_name')
                ->where('city_province_id', $req->memis_province)
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->city_id => array($row->city_name => Member::get_all_educ($row->city_id, $req)));
                }

            }else if($req->memis_sex == '999'){

                $query = DB::connection('dbskms')->table('tblsex')
                ->select('s_id','sex')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->s_id => array($row->sex => Member::get_all_educ($row->s_id, $req)));
                }
            }else if($req->memis_age == '999'){

                $query = DB::connection('dbskms')->table('tblage_ranges')
                ->select('age_id','age_range')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->age_id => array($row->age_range => Member::get_all_educ($row->age_id, $req)));
                }
            }
        }

        if($req->radio_default == 'memis_age'){ // age default
            if($req->memis_category == '999'){

                $query = DB::connection('dbskms')->table('tblmembership_types')
                ->select('membership_type_id','membership_type_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->membership_type_id => array($row->membership_type_name => Member::get_all_age($row->membership_type_id, $req)));
                }
            }else if($req->memis_status == '999'){

                $query = DB::connection('dbskms')->table('tblmembership_status')
                ->select('membership_status_id','membership_status_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->membership_status_id => array($row->membership_status_name => Member::get_all_age($row->membership_status_id, $req)));
                }
            }if($req->memis_division == '999'){

                $query = DB::connection('dbskms')->table('tbldivisions')
                ->select('div_id','div_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->div_id => array($row->div_name => Member::get_all_age($row->div_id, $req)));
                }
            }else if($req->memis_region == '999'){

                $query = DB::connection('dbskms')->table('tblregions')
                ->select('region_id','region_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->region_id => array($row->region_name => Member::get_all_age($row->region_id, $req)));
                }

            }else if($req->memis_province == '999'){

                $query = DB::connection('dbskms')->table('tblprovinces')
                ->select('province_id','province_name')
                ->where('province_region_id', $req->memis_region)
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->province_id => array($row->province_name => Member::get_all_age($row->province_id, $req)));
                }

            }else if($req->memis_city == '999'){

                $query = DB::connection('dbskms')->table('tblcities')
                ->select('city_id','city_name')
                ->where('city_province_id', $req->memis_province)
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->city_id => array($row->city_name => Member::get_all_age($row->city_id, $req)));
                }

            }else if($req->memis_sex == '999'){

                $query = DB::connection('dbskms')->table('tblsex')
                ->select('s_id','sex')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->s_id => array($row->sex => Member::get_all_age($row->s_id, $req)));
                }
            }else if($req->memis_educ == '999'){

                $query = DB::connection('dbskms')->table('tbldegree_types')
                ->select('deg_id','deg_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->deg_id => array($row->deg_name => Member::get_all_age($row->deg_id, $req)));
                }
            }
        }

        if($req->radio_default == 'memis_island'){ // island default
            
            if($req->memis_division == '999'){

                $query = DB::connection('dbskms')->table('tbldivisions')
                ->select('div_id','div_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->div_id => array($row->div_name => Member::get_all_island($row->div_id, $req)));
                }
                // to do next cutoff
            }else if($req->memis_sex == '999'){

                $query = DB::connection('dbskms')->table('tblsex')
                ->select('s_id','sex')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->s_id => array($row->sex => Member::get_all_island($row->s_id, $req)));
                }
            }else if($req->memis_category == '999'){

                $query = DB::connection('dbskms')->table('tblmembership_types')
                ->select('membership_type_id','membership_type_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->membership_type_id => array($row->membership_type_name => Member::get_all_island($row->membership_type_id, $req)));
                }
            }else if($req->memis_status == '999'){

                $query = DB::connection('dbskms')->table('tblmembership_status')
                ->select('membership_status_id','membership_status_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->membership_status_id => array($row->membership_status_name => Member::get_all_island($row->membership_status_id, $req)));
                }
            }else if($req->memis_educ == '999'){

                $query = DB::connection('dbskms')->table('tbldegree_types')
                ->select('deg_id','deg_name')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->deg_id => array($row->deg_name => Member::get_all_island($row->deg_id, $req)));
                }
            }
            else if($req->memis_age == '999'){ 

                $query = DB::connection('dbskms')->table('tblage_ranges')
                ->select('age_id','age_range')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->age_id => array($row->age_range => Member::get_all_island($row->age_id, $req)));
                }
            }
        }

        if($req->radio_default == 'memis_country'){ // country default

            if($req->memis_sex == '999'){

                $query = DB::connection('dbskms')->table('tblsex')
                ->select('s_id','sex')
                ->get();
                
                foreach($query as $row){  
                    $result_array[] = array($row->s_id => array($row->sex => Member::get_all_country($row->s_id, $req)));
                }
            }
        }

        return $result_array;
    }

    // all sex for stack
    public static function get_all_sex($id, $req){

        $select = '';
        $where = '';
        $join = '';
        $group_by = '';
        $order_by = '';
        $having = '';
        $deceased = ' AND mem_status != 3 ';
        $key = '';
        $field = '';
        $sub_q = '';

        $group_by .= ' GROUP BY pp_usr_id ';
        $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
        $join .= ' LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';
        $join .= ' JOIN new_dbskms.tblsex on s_id = pp_sex ';


        if($req->memis_region > 0){
       
            $where .= ' AND emp_region LIKE '. $id; 
            $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id) ';   
        }

        if($req->memis_province > 0){
       
            $where .= ' AND emp_province LIKE '. $id; 
            $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id) ';   
        }

        if($req->memis_city > 0){
       
            $where .= ' AND emp_city LIKE '. $id; 
            $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id) ';   
        }

        if($req->memis_country > 0){

            $where .= ' AND emp_country LIKE '. $req->memis_country;
            $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';    
        
        }
        
        if($req->memis_division > 0){ // division

            $where .= ' AND mem_div_id LIKE '. $id;

        }

        if($req->memis_category > 0){ // category

            $where .= ' AND mem_type LIKE '. $id;

        }

        if($req->memis_status > 0){ // status

                $where .= ' AND mem_status LIKE ' . $id;
        
        }

        if($req->memis_educ > 0){ // educ 

            $key = ($having != '') ? 'AND' : ' HAVING ';
            $field .= ' , MIN(adp_highest) as minadp ';
            $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
            $having .= ' '.$key.' minadp = ' . $id . ' ';

        }

        if($req->memis_age > 0){ // age 

                $key = ($having != '') ? 'AND' : 'HAVING';
                $field .= ' ,(YEAR(CURDATE()) - YEAR(pp_date_of_birth)) AS age ';
                $where .= ' AND pp_date_of_birth like \'%-%\' and pp_date_of_birth > 0 ';
                
                if($id == 1){
                    $having .= ' '.$key.' age > 20 AND age < 31 ';
                }else if($id == 2){
                    $having .= ' '.$key.' age > 30 AND age < 41 ';
                }else if($id == 3){
                    $having .= ' '.$key.' age > 40 AND age < 51 ';
                }else if($id == 4){
                    $having .= ' '.$key.' age > 50 AND age < 61 ';
                }else if($id == 5){
                    $having .= ' '.$key.' age > 60 AND age < 71 ';
                }else if($id == 6){
                    $having .= ' '.$key.' age > 70 ';
                }else{
                    $having .= ' '.$key.' age > 0 ';
                }
        }
   
        if($req->memis_start_year > 0 && $req->memis_end_year){
            
            $key = ($having != '') ? 'AND' : ' HAVING ';
            $field .= ' ,YEAR(mem_date_elected) AS year ';
            $having .= ' '.$key. ' year BETWEEN '. $req->memis_start_year .' AND '. $req->memis_end_year .' '; 
        }
                        
        $sub_q .= 'SELECT s_id, count(mem_usr_id) as total '. $field .
        'FROM new_dbskms.tblmembers '. 
        'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
        'WHERE usr_grp_id LIKE 3 '. $where . $deceased . $group_by . $having;

        $select .= 'SELECT count(total) as total FROM '. 
        '( ' . $sub_q . ') as tmp GROUP BY s_id';

        return DB::select($select);

       
    }

    // all status for stack
    public static function get_all_status($id, $req){

        $select = '';
        $where = '';
        $join = '';
        $group_by = '';
        $order_by = '';
        $having = '';
        $deceased = ' AND mem_status != 3 ';
        $key = '';
        $field = '';
        $sub_q = '';

        $group_by .= ' GROUP BY pp_usr_id ';
        $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
        $join .= ' LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';
        $join .= ' JOIN new_dbskms.tblmembership_status ON membership_status_id = mem_status ';


        if($req->memis_region > 0){
       
            $where .= ' AND emp_region LIKE '. $id; 
            $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id) ';   
        }

        if($req->memis_province > 0){
       
            $where .= ' AND emp_province LIKE '. $id; 
            $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id) ';   
        }

        if($req->memis_city > 0){
       
            $where .= ' AND emp_city LIKE '. $id; 
            $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id) ';   
        }

        if($req->memis_country > 0){

            $where .= ' AND emp_country LIKE '. $req->memis_country;
            $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';    
        
        }
        
        if($req->memis_sex > 0){ // sex

            $where .= ' AND pp_sex LIKE '. $id;

        }

        if($req->memis_category > 0){ // category

            $where .= ' AND mem_type LIKE '. $id;

        }

        if($req->memis_division > 0){ // status

                $where .= ' AND mem_div_id LIKE ' . $id;
        
        }

        if($req->memis_educ > 0){ // educ 

            $key = ($having != '') ? 'AND' : ' HAVING ';
            $field .= ' , MIN(adp_highest) as minadp ';
            $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
            $having .= ' '.$key.' minadp = ' . $id . ' ';

        }

        if($req->memis_age > 0){ // age 

                $key = ($having != '') ? 'AND' : 'HAVING';
                $field .= ' ,(YEAR(CURDATE()) - YEAR(pp_date_of_birth)) AS age ';
                $where .= ' AND pp_date_of_birth like \'%-%\' and pp_date_of_birth > 0 ';
                
                if($id == 1){
                    $having .= ' '.$key.' age > 20 AND age < 31 ';
                }else if($id == 2){
                    $having .= ' '.$key.' age > 30 AND age < 41 ';
                }else if($id == 3){
                    $having .= ' '.$key.' age > 40 AND age < 51 ';
                }else if($id == 4){
                    $having .= ' '.$key.' age > 50 AND age < 61 ';
                }else if($id == 5){
                    $having .= ' '.$key.' age > 60 AND age < 71 ';
                }else if($id == 6){
                    $having .= ' '.$key.' age > 70 ';
                }else{
                    $having .= ' '.$key.' age > 0 ';
                }
        }
   
        if($req->memis_start_year > 0 && $req->memis_end_year){
            
            $key = ($having != '') ? 'AND' : ' HAVING ';
            $field .= ' ,YEAR(mem_date_elected) AS year ';
            $having .= ' '.$key. ' year BETWEEN '. $req->memis_start_year .' AND '. $req->memis_end_year .' '; 
        }
                        
        $sub_q .= 'SELECT membership_status_id, count(mem_usr_id) as total '. $field .
        'FROM new_dbskms.tblmembers '. 
        'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
        'WHERE usr_grp_id LIKE 3 '. $where . $deceased . $group_by . $having;

        $select .= 'SELECT count(total) as total FROM '. 
        '( ' . $sub_q . ') as tmp GROUP BY membership_status_id';

        return DB::select($select);
    }
    
    // all category for stack
    public static function get_all_category($id, $req){

        $select = '';
        $where = '';
        $join = '';
        $group_by = '';
        $order_by = '';
        $having = '';
        $deceased = ' AND mem_status != 3 ';
        $key = '';
        $field = '';
        $sub_q = '';

        $group_by .= ' GROUP BY pp_usr_id ';
        $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
        $join .= ' LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';
        $join .= '  JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';

        if($req->memis_region > 0){
       
            $where .= ' AND emp_region LIKE '. $id; 
            $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id) ';   
        }

        if($req->memis_province > 0){
       
            $where .= ' AND emp_province LIKE '. $id; 
            $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id) ';   
        }

        if($req->memis_city > 0){
       
            $where .= ' AND emp_city LIKE '. $id; 
            $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id) ';   
        }

        if($req->memis_country > 0){

            $where .= ' AND emp_country LIKE '. $req->memis_country;
            $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';    
        
        }
        
        if($req->memis_sex > 0){ // sex

            $where .= ' AND pp_sex LIKE '. $id;

        }

        if($req->memis_division > 0){ // category

            $where .= ' AND mem_div_id LIKE '. $id;
        }

        if($req->memis_status > 0){ // status

                $where .= ' AND mem_status LIKE ' . $id;
        
        }

        if($req->memis_educ > 0){ // educ 

            $key = ($having != '') ? 'AND' : ' HAVING ';
            $field .= ' , MIN(adp_highest) as minadp ';
            $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
            $having .= ' '.$key.' minadp = ' . $id . ' ';

        }

        if($req->memis_age > 0){ // age 

                $key = ($having != '') ? 'AND' : 'HAVING';
                $field .= ' ,(YEAR(CURDATE()) - YEAR(pp_date_of_birth)) AS age ';
                $where .= ' AND pp_date_of_birth like \'%-%\' and pp_date_of_birth > 0 ';
                
                if($id == 1){
                    $having .= ' '.$key.' age > 20 AND age < 31 ';
                }else if($id == 2){
                    $having .= ' '.$key.' age > 30 AND age < 41 ';
                }else if($id == 3){
                    $having .= ' '.$key.' age > 40 AND age < 51 ';
                }else if($id == 4){
                    $having .= ' '.$key.' age > 50 AND age < 61 ';
                }else if($id == 5){
                    $having .= ' '.$key.' age > 60 AND age < 71 ';
                }else if($id == 6){
                    $having .= ' '.$key.' age > 70 ';
                }else{
                    $having .= ' '.$key.' age > 0 ';
                }
        }
   
        if($req->memis_start_year > 0 && $req->memis_end_year){
            
            $key = ($having != '') ? 'AND' : ' HAVING ';
            $field .= ' ,YEAR(mem_date_elected) AS year ';
            $having .= ' '.$key. ' year BETWEEN '. $req->memis_start_year .' AND '. $req->memis_end_year .' '; 
        }
                        
        $sub_q .= 'SELECT membership_type_id, count(mem_usr_id) as total '. $field .
        'FROM new_dbskms.tblmembers '. 
        'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
        'WHERE usr_grp_id LIKE 3 '. $where . $deceased . $group_by . $having;

        $select .= 'SELECT count(total) as total FROM '. 
        '( ' . $sub_q . ') as tmp GROUP BY membership_type_id';

        return DB::select($select);

    }

    // all region for stack
    public static function get_all_region($id, $req){

        $select = '';
        $where = '';
        $join = '';
        $group_by = '';
        $order_by = '';
        $having = '';
        $deceased = ' AND mem_status != 3 ';
        $key = '';
        $field = '';
        $sub_q = '';

        $group_by .= ' GROUP BY pp_usr_id ';

        // $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id) '; 
        
        $where .= 'AND emp_id IN (SELECT MAX(emp_id) AS emp_id '.
        'FROM new_dbskms.tblemployments '.
        'GROUP by emp_usr_id '.
        'ORDER by emp_id DESC)';

        if($req->memis_division > 0){
            
            $where .= ' AND mem_div_id LIKE '. $id;  
        }

        if($req->memis_province > 0){
       
            $where .= ' AND emp_province LIKE '. $id; 
            $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id) ';   
        }

        if($req->memis_city > 0){
       
            $where .= ' AND emp_city LIKE '. $id; 
            $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id) ';   
        }

        if($req->memis_country > 0){

            $where .= ' AND emp_country LIKE '. $req->memis_country;
            $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';    
        
        }
        
        if($req->memis_sex > 0){ // sex

            $where .= ' AND pp_sex LIKE '. $id;

        }

        if($req->memis_category > 0){ // category

            $where .= ' AND mem_type LIKE '. $id;

        }

        if($req->memis_status > 0){ // status

                $where .= ' AND mem_status LIKE ' . $id;
        
        }

        if($req->memis_educ > 0){ // educ 

            $key = ($having != '') ? 'AND' : ' HAVING ';
            $field .= ' , MIN(adp_highest) as minadp ';
            $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
            $having .= ' '.$key.' minadp = ' . $id . ' ';

        }

        if($req->memis_age > 0){ // age 

                $key = ($having != '') ? 'AND' : 'HAVING';
                $field .= ' ,(YEAR(CURDATE()) - YEAR(pp_date_of_birth)) AS age ';
                $where .= ' AND pp_date_of_birth like \'%-%\' and pp_date_of_birth > 0 ';
                
                if($id == 1){
                    $having .= ' '.$key.' age > 20 AND age < 31 ';
                }else if($id == 2){
                    $having .= ' '.$key.' age > 30 AND age < 41 ';
                }else if($id == 3){
                    $having .= ' '.$key.' age > 40 AND age < 51 ';
                }else if($id == 4){
                    $having .= ' '.$key.' age > 50 AND age < 61 ';
                }else if($id == 5){
                    $having .= ' '.$key.' age > 60 AND age < 71 ';
                }else if($id == 6){
                    $having .= ' '.$key.' age > 70 ';
                }else{
                    $having .= ' '.$key.' age > 0 ';
                }
        }
   
        if($req->memis_start_year > 0 && $req->memis_end_year){
            
            $key = ($having != '') ? 'AND' : ' HAVING ';
            $field .= ' ,YEAR(mem_date_elected) AS year ';
            $having .= ' '.$key. ' year BETWEEN '. $req->memis_start_year .' AND '. $req->memis_end_year .' '; 
        }
                        

        $sub_q .= 'SELECT region_id, count(mem_usr_id) as total '. $field .
        'FROM new_dbskms.tblpersonal_profiles '. 
        'INNER JOIN new_dbskms.tblusers ON usr_id = pp_usr_id '.
        'LEFT JOIN new_dbskms.tblemployments as pp ON emp_usr_id = pp_usr_id '.
        'LEFT JOIN new_dbskms.tblmembers ON mem_usr_id = pp_usr_id '.
        'LEFT JOIN new_dbskms.tblregions ON region_id = emp_region '.
        'WHERE usr_grp_id like 3  AND mem_status != 3 AND mem_status IS NOT NULL AND emp_country = 175 '.
        $where . $deceased . $group_by . $having;

        $select .= 'SELECT count(total) as total FROM '. 
        '( ' . $sub_q . ') as tmp GROUP BY region_id';

        return DB::select($select);

    }

    // all country for stack
    public static function get_all_country($id, $req){

        $select = '';
        $where = '';
        $join = '';
        $group_by = '';
        $order_by = '';
        $having = '';
        $deceased = ' AND mem_status != 3 ';
        $key = '';
        $field = '';
        $sub_q = '';

        $group_by .= ' GROUP BY pp_usr_id ';
        
        $where .= 'AND emp_id IN (SELECT MAX(emp_id) AS emp_id '.
        'FROM new_dbskms.tblemployments '.
        'GROUP by emp_usr_id '.
        'ORDER by emp_id DESC)';

        if($req->memis_sex > 0){ // sex

            $where .= ' AND pp_sex LIKE '. $id;

        }
                        
        $sub_q .= 'SELECT country_id, count(mem_usr_id) as total, country_name '. $field .
        'FROM new_dbskms.tblpersonal_profiles '. 
        'INNER JOIN new_dbskms.tblusers ON usr_id = pp_usr_id '.
        'LEFT JOIN new_dbskms.tblemployments as pp ON emp_usr_id = pp_usr_id '.
        'LEFT JOIN new_dbskms.tblmembers ON mem_usr_id = pp_usr_id '.
        'JOIN new_dbskms.tblcountries on country_id = emp_country '.
        'WHERE usr_grp_id LIKE 3 AND mem_status IS NOT NULL AND emp_country > 0 '. 
        $where . $deceased . $group_by . $having;

        $select .= 'SELECT count(total) as total, country_name FROM '. 
        '( ' . $sub_q . ') as tmp GROUP BY country_id';

        return DB::select($select);

    }

    // all province for stack (todo allow stacked chart)
    public static function get_all_province($id, $req){

        $join = '';
        $where = '';
        $deceased = ' AND mem_status != 3 ';
        $select = '';
        $having = '';


        if($req->memis_division == '999'){

            $join .= ' JOIN new_dbskms.tbldivisions ON div_id = mem_div_id ';
            $where .= ' AND div_id like '. $id;

        }else if($req->memis_sex == '999'){

            $join .= ' LEFT JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
            $join .= ' LEFT JOIN new_dbskms.tblsex ON s_id = pp_sex ';
            $where .= ' AND pp_sex like '. $id;
            
        }else if($req->memis_category == '999'){

                $join .= ' JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';
                $where .= ' AND mem_type like '. $id;
            
        }else if($req->memis_status == '999'){

                $join .= '  JOIN new_dbskms.tblmembership_status ON membership_status_id = mem_status ';
                $where .= ' AND mem_status like '. $id;
                $deceased = '';
        }else if($req->memis_educ == '999'){
                
                $join .= ' LEFT JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
                $join .= ' LEFT JOIN new_dbskms.tbldegree_types ON deg_id = adp_highest ';
                $select .= ' ,CASE WHEN MAX(adp_highest) = 1 THEN "Doctor of Philosopy (PhD)" '.
                'WHEN MAX(adp_highest) = 2 THEN "Master of Degree (MS)" '. 
                'WHEN MAX(adp_highest) = 3 THEN "Bachelor of Science (BS)" END ';
                $where .= ' AND adp_highest like '. $id;
        }else if($req->memis_age == '999'){
            
            $where .= ' AND province_region_id LIKE '. $req->memis_region;
            $query = DB::select('SELECT COUNT(pp_usr_id) as total, province_id, tmp.range '. 
            'FROM (SELECT pp_usr_id, province_id, '. 
            'COUNT(CASE WHEN emp_period_to = "Present" THEN emp_period_to END) AS PRESENT, '.
            'CASE WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 20 AND 31 then "1" '. 
            'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 30 AND 41 then "2" '. 
            'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 40 AND 51 then "3" '. 
            'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 50 AND 61 then "4" '. 
            'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 60 AND 71 then "5" '. 
            'ELSE "6" END AS "range" FROM new_dbskms.tblpersonal_profiles '. 
            'INNER JOIN new_dbskms.tblusers ON usr_id = pp_usr_id '. 
            'LEFT JOIN new_dbskms.tblmembers ON mem_usr_id = usr_id '. 
            'LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id '.
            'LEFT JOIN new_dbskms.tblprovinces ON province_id = emp_province '. 
            'WHERE usr_grp_id LIKE 3 AND mem_status != 3 AND emp_region > 0 '. $where . 
            ' GROUP BY emp_usr_id HAVING PRESENT > 0 '. 
            'UNION ALL '.
            'SELECT pp_usr_id, province_id, '. 
            'COUNT(CASE WHEN emp_period_to = "Present" THEN emp_period_to END) AS PRESENT, '.
            'CASE WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 20 AND 31 then "1" '. 
            'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 30 AND 41 then "2" '. 
            'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 40 AND 51 then "3" '. 
            'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 50 AND 61 then "4" '. 
            'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 60 AND 71 then "5" '. 
            'ELSE "6" END AS "range" FROM new_dbskms.tblpersonal_profiles '. 
            'INNER JOIN new_dbskms.tblusers ON usr_id = pp_usr_id '. 
            'LEFT JOIN new_dbskms.tblmembers ON mem_usr_id = usr_id '. 
            'LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id '.
            'LEFT JOIN new_dbskms.tblprovinces ON province_id = emp_province '. 
            'WHERE usr_grp_id LIKE 3 AND mem_status != 3 AND emp_region > 0  '. $where . 
            ' GROUP BY emp_usr_id HAVING PRESENT = 0 '. 
            ') AS tmp '. 
            'WHERE tmp.range = '. $id . ' '. 
            'GROUP BY province_id');

            return $query;
        }

        $where .= ' AND province_region_id LIKE '. $req->memis_region;
        $query = DB::select('SELECT province_id, COUNT(emp_usr_id) as total '.
            'FROM ( (SELECT emp_usr_id, province_id, '.
            'COUNT(case when emp_period_to = "Present" then emp_period_to end) as PRESENT '. $select .
            'FROM new_dbskms.tblemployments '.
            'INNER JOIN new_dbskms.tblusers ON usr_id = emp_usr_id '.
            'LEFT JOIN new_dbskms.tblmembers ON mem_usr_id = usr_id '.
            'LEFT JOIN new_dbskms.tblprovinces ON province_id = emp_province '. $join .
            'WHERE usr_grp_id like 3 '. $deceased . $where .
            ' GROUP BY emp_usr_id having PRESENT > 0) '.
            'UNION ALL '.
            '(SELECT emp_usr_id, province_id, '.
            'COUNT(CASE WHEN emp_period_to = "Present" THEN emp_period_to END) as PRESENT '. $select .
            'FROM new_dbskms.tblemployments '.
            'INNER JOIN new_dbskms.tblusers ON usr_id = emp_usr_id '.
            'LEFT JOIN new_dbskms.tblmembers ON mem_usr_id = usr_id '.
            'LEFT JOIN new_dbskms.tblprovinces ON province_id = emp_province '. $join .
            'WHERE usr_grp_id like 3  '. $deceased . $where .
            ' GROUP BY emp_usr_id having PRESENT = 0) '.
            ') as q '.
            'GROUP BY province_id');
       

        return $query;
    }

    // all city for stack (todo allow stacked chart)
    public static function get_all_city($id, $req){

        $join = '';
        $where = '';
        $deceased = ' AND mem_status != 3 ';
        $select = '';
        $having = '';


        if($req->memis_division == '999'){

            $join .= ' JOIN new_dbskms.tbldivisions ON div_id = mem_div_id ';
            $where .= ' AND div_id like '. $id;

        }else if($req->memis_sex == '999'){

            $join .= ' LEFT JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
            $join .= ' LEFT JOIN new_dbskms.tblsex ON s_id = pp_sex ';
            $where .= ' AND pp_sex like '. $id;
            
        }else if($req->memis_category == '999'){

                $join .= '  JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';
                $where .= ' AND mem_type like '. $id;
            
        }else if($req->memis_status == '999'){

                $join .= ' JOIN new_dbskms.tblmembership_status ON membership_status_id = mem_status ';
                $where .= ' AND mem_status like '. $id;
                $deceased = '';
        }else if($req->memis_educ == '999'){
                
                $join .= ' LEFT JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
                $join .= ' LEFT JOIN new_dbskms.tbldegree_types ON deg_id = adp_highest ';
                $select .= ' ,CASE WHEN MAX(adp_highest) = 1 THEN "Doctor of Philosopy (PhD)" '.
                'WHEN MAX(adp_highest) = 2 THEN "Master of Degree (MS)" '. 
                'WHEN MAX(adp_highest) = 3 THEN "Bachelor of Science (BS)" END ';
                $where .= ' AND adp_highest like '. $id;
        }else if($req->memis_age == '999'){
            
            $where .= ' AND city_province_id LIKE '. $req->memis_province;
            $query = DB::select('SELECT COUNT(pp_usr_id) as total, city_id, tmp.range '. 
            'FROM (SELECT pp_usr_id, city_id, '. 
            'COUNT(CASE WHEN emp_period_to = "Present" THEN emp_period_to END) AS PRESENT, '.
            'CASE WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 20 AND 31 then "1" '. 
            'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 30 AND 41 then "2" '. 
            'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 40 AND 51 then "3" '. 
            'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 50 AND 61 then "4" '. 
            'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 60 AND 71 then "5" '. 
            'ELSE "6" END AS "range" FROM new_dbskms.tblpersonal_profiles '. 
            'INNER JOIN new_dbskms.tblusers ON usr_id = pp_usr_id '. 
            'LEFT JOIN new_dbskms.tblmembers ON mem_usr_id = usr_id '. 
            'LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id '.
            'LEFT JOIN new_dbskms.tblcities ON city_id = emp_city '. 
            'WHERE usr_grp_id LIKE 3 AND mem_status != 3 AND emp_region > 0 '. $where . 
            ' GROUP BY emp_usr_id HAVING PRESENT > 0 '. 
            'UNION ALL '.
            'SELECT pp_usr_id, city_id, '. 
            'COUNT(CASE WHEN emp_period_to = "Present" THEN emp_period_to END) AS PRESENT, '.
            'CASE WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 20 AND 31 then "1" '. 
            'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 30 AND 41 then "2" '. 
            'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 40 AND 51 then "3" '. 
            'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 50 AND 61 then "4" '. 
            'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 60 AND 71 then "5" '. 
            'ELSE "6" END AS "range" FROM new_dbskms.tblpersonal_profiles '. 
            'INNER JOIN new_dbskms.tblusers ON usr_id = pp_usr_id '. 
            'LEFT JOIN new_dbskms.tblmembers ON mem_usr_id = usr_id '. 
            'LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id '.
            'LEFT JOIN new_dbskms.tblcities ON city_id = emp_city '. 
            'WHERE usr_grp_id LIKE 3 AND mem_status != 3 AND emp_region > 0  '. $where . 
            ' GROUP BY emp_usr_id HAVING PRESENT = 0 '. 
            ') AS tmp '. 
            'WHERE tmp.range = '. $id . ' '. 
            'GROUP BY city_id');

            return $query;
        }

        $where .= ' AND city_province_id LIKE '. $req->memis_province;
        $query = DB::select('SELECT city_id, COUNT(emp_usr_id) as total '.
            'FROM ( (SELECT emp_usr_id, city_id, '.
            'COUNT(case when emp_period_to = "Present" then emp_period_to end) as PRESENT '. $select .
            'FROM new_dbskms.tblemployments '.
            'INNER JOIN new_dbskms.tblusers ON usr_id = emp_usr_id '.
            'LEFT JOIN new_dbskms.tblmembers ON mem_usr_id = usr_id '.
            'LEFT JOIN new_dbskms.tblcities ON city_id = emp_city '. $join .
            'WHERE usr_grp_id like 3 '. $deceased . $where .
            ' GROUP BY emp_usr_id having PRESENT > 0) '.
            'UNION ALL '.
            '(SELECT emp_usr_id, city_id, '.
            'COUNT(CASE WHEN emp_period_to = "Present" THEN emp_period_to END) as PRESENT '. $select .
            'FROM new_dbskms.tblemployments '.
            'INNER JOIN new_dbskms.tblusers ON usr_id = emp_usr_id '.
            'LEFT JOIN new_dbskms.tblmembers ON mem_usr_id = usr_id '.
            'LEFT JOIN new_dbskms.tblcities ON city_id = emp_city '. $join .
            'WHERE usr_grp_id like 3  '. $deceased . $where .
            ' GROUP BY emp_usr_id having PRESENT = 0) '.
            ') as q '.
            'GROUP BY city_id');
       

        return $query;
    }

    // all division for stack
    public static function get_all_division($id, $req){

        $select = '';
        $where = '';
        $join = '';
        $group_by = '';
        $order_by = '';
        $having = '';
        $deceased = ' AND mem_status != 3 ';
        $key = '';
        $field = '';
        $sub_q = '';

        $group_by .= ' GROUP BY pp_usr_id ';
        $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
        $join .= ' JOIN new_dbskms.tbldivisions on div_id = mem_div_id ';
        $join .= ' LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';

        // ALL == 999
        if($req->memis_region == '999'){
       
            $where .= ' AND emp_region LIKE '. $id; 
            $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id) ';   
        }

        if($req->memis_province == '999'){
       
            $where .= ' AND emp_province LIKE '. $id; 
            $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id) ';   
        }

        if($req->memis_city == '999'){
       
            $where .= ' AND emp_city LIKE '. $id; 
            $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id) ';   
        }

        if($req->memis_country == '999'){

            $where .= ' AND emp_country LIKE '. $id;
            $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';    
        
        }
        
        if($req->memis_sex == '999'){ // sex

            $where .= ' AND pp_sex LIKE '. $id;

        }

        if($req->memis_category == '999'){ // category

            $where .= ' AND mem_type LIKE '. $id;

        }

        if($req->memis_status == '999'){ // status

                $where .= ' AND mem_status LIKE ' . $id;
        
        }

        if($req->memis_educ == '999'){ // educ 

            $key = ($having != '') ? 'AND' : ' HAVING ';
            $field .= ' , MIN(adp_highest) as minadp ';
            $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
            $having .= ' '.$key.' minadp = ' . $id . ' ';

        }

        if($req->memis_age == '999'){ // age 

                $key = ($having != '') ? 'AND' : 'HAVING';
                $field .= ' ,(YEAR(CURDATE()) - YEAR(pp_date_of_birth)) AS age ';
                $where .= ' AND pp_date_of_birth like \'%-%\' and pp_date_of_birth > 0 ';
                
                if($id == 1){
                    $having .= ' '.$key.' age > 20 AND age < 31 ';
                }else if($id == 2){
                    $having .= ' '.$key.' age > 30 AND age < 41 ';
                }else if($id == 3){
                    $having .= ' '.$key.' age > 40 AND age < 51 ';
                }else if($id == 4){
                    $having .= ' '.$key.' age > 50 AND age < 61 ';
                }else if($id == 5){
                    $having .= ' '.$key.' age > 60 AND age < 71 ';
                }else if($id == 6){
                    $having .= ' '.$key.' age > 70 ';
                }else{
                    $having .= ' '.$key.' age > 0 ';
                }
        }
   

        if($req->memis_age > 0 && $req->memos_region != '999'){ // age 

                $key = ($having != '') ? 'AND' : 'HAVING';
                $field .= ' ,(YEAR(CURDATE()) - YEAR(pp_date_of_birth)) AS age ';
                $where .= ' AND pp_date_of_birth like \'%-%\' and pp_date_of_birth > 0 ';
                
                if($req->memis_age == 1){
                    $having .= ' '.$key.' age > 20 AND age < 31 ';
                }else if($req->memis_age == 2){
                    $having .= ' '.$key.' age > 30 AND age < 41 ';
                }else if($req->memis_age == 3){
                    $having .= ' '.$key.' age > 40 AND age < 51 ';
                }else if($req->memis_age == 4){
                    $having .= ' '.$key.' age > 50 AND age < 61 ';
                }else if($req->memis_age == 5){
                    $having .= ' '.$key.' age > 60 AND age < 71 ';
                }else if($req->memis_age == 6){
                    $having .= ' '.$key.' age > 70 ';
                }else{
                    $having .= ' '.$key.' age > 0 ';
                }
        }

        if($req->memis_start_year > 0 && $req->memis_end_year){
            
            $key = ($having != '') ? 'AND' : ' HAVING ';
            $field .= ' ,YEAR(mem_date_elected) AS year ';
            $having .= ' '.$key. ' year BETWEEN '. $req->memis_start_year .' AND '. $req->memis_end_year .' '; 
        }
                        
        $sub_q .= 'SELECT div_id, count(mem_usr_id) as total '. $field .
        'FROM new_dbskms.tblmembers '. 
        'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
        'WHERE usr_grp_id LIKE 3 '. $where . $deceased . $group_by . $having;

        $select .= 'SELECT count(total) as total FROM '. 
        '( ' . $sub_q . ') as tmp GROUP BY div_id';

        return DB::select($select);



    }

    // all educ for stack
    public static function get_all_educ($id, $req){

        $select = '';
        $where = '';
        $join = '';
        $group_by = '';
        $order_by = '';
        $having = '';
        $deceased = ' AND mem_status != 3 ';
        $key = '';
        $field = '';
        $sub_q = '';

        $group_by .= ' GROUP BY pp_usr_id ';
        $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
        $join .= ' LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';

        
        $field .= ' , MIN(adp_highest) as minadp ';
        $join .= ' LEFT JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
        $join .= ' JOIN new_dbskms.tbldegree_types on deg_id = adp_highest ';
        

        if($req->memis_division > 0){

            $where .= ' AND mem_div_id like '. $id;

        }

        if($req->memis_region > 0){
       
            $where .= ' AND emp_region LIKE '. $id; 
            $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id) ';   
        }

        if($req->memis_province > 0){
       
            $where .= ' AND emp_province LIKE '. $id; 
            $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id) ';   
        }

        if($req->memis_city > 0){
       
            $where .= ' AND emp_city LIKE '. $id; 
            $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id) ';   
        }

        if($req->memis_country > 0){

            $where .= ' AND emp_country LIKE '. $req->memis_country;
            $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';    
        
        }
        
        if($req->memis_sex > 0){ // sex

            $where .= ' AND pp_sex LIKE '. $id;

        }

        if($req->memis_category > 0){ // category

            $where .= ' AND mem_type LIKE '. $id;

        }

        if($req->memis_status > 0){ // status

                $where .= ' AND mem_status LIKE ' . $id;
        
        }


        if($req->memis_age > 0){ // age 

                $key = ($having != '') ? 'AND' : 'HAVING';
                $field .= ' ,(YEAR(CURDATE()) - YEAR(pp_date_of_birth)) AS age ';
                $where .= ' AND pp_date_of_birth like \'%-%\' and pp_date_of_birth > 0 ';
                
                if($id == 1){
                    $having .= ' '.$key.' age > 20 AND age < 31 ';
                }else if($id == 2){
                    $having .= ' '.$key.' age > 30 AND age < 41 ';
                }else if($id == 3){
                    $having .= ' '.$key.' age > 40 AND age < 51 ';
                }else if($id == 4){
                    $having .= ' '.$key.' age > 50 AND age < 61 ';
                }else if($id == 5){
                    $having .= ' '.$key.' age > 60 AND age < 71 ';
                }else if($id == 6){
                    $having .= ' '.$key.' age > 70 ';
                }else{
                    $having .= ' '.$key.' age > 0 ';
                }
        }
   
        if($req->memis_start_year > 0 && $req->memis_end_year){
            
            $key = ($having != '') ? 'AND' : ' HAVING ';
            $field .= ' ,YEAR(mem_date_elected) AS year ';
            $having .= ' '.$key. ' year BETWEEN '. $req->memis_start_year .' AND '. $req->memis_end_year .' '; 
        }
                        
        $sub_q .= 'SELECT deg_id, count(mem_usr_id) as total '. $field .
        'FROM new_dbskms.tblmembers '. 
        'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
        'WHERE usr_grp_id LIKE 3 '. $where . $deceased . $group_by . $having;

        $select .= 'SELECT count(total) as total FROM '. 
        '( ' . $sub_q . ') as tmp GROUP BY deg_id';

        return DB::select($select);

    }

    // all age for stack
    public static function get_all_age($id, $req){ 

        $select = '';
        $where = '';
        $join = '';
        $group_by = '';
        $order_by = '';
        $having = '';
        $deceased = ' AND mem_status != 3 ';
        $key = '';
        $field = '';
        $sub_q = '';

        $group_by .= ' GROUP BY pp_usr_id ';
        $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
        $join .= ' LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';


        if($req->memis_division > 0){

            $where .= ' AND mem_div_id like '. $id;

        }

        if($req->memis_region > 0){
       
            $where .= ' AND emp_region LIKE '. $id; 
            $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id) ';   
        }

        if($req->memis_province > 0){
       
            $where .= ' AND emp_province LIKE '. $id; 
            $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id) ';   
        }

        if($req->memis_city > 0){
       
            $where .= ' AND emp_city LIKE '. $id; 
            $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id) ';   
        }

        if($req->memis_country > 0){

            $where .= ' AND emp_country LIKE '. $req->memis_country;
            $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';    
        
        }
        
        if($req->memis_sex > 0){ // sex

            $where .= ' AND pp_sex LIKE '. $id;

        }

        if($req->memis_category > 0){ // category

            $where .= ' AND mem_type LIKE '. $id;

        }

        if($req->memis_status > 0){ // status

                $where .= ' AND mem_status LIKE ' . $id;
        
        }

        if($req->memis_educ > 0){ // educ 

            $key = ($having != '') ? 'AND' : ' HAVING ';
            $field .= ' , MIN(adp_highest) as minadp ';
            $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
            $having .= ' '.$key.' minadp = ' . $id . ' ';

        }

        if($req->memis_start_year > 0 && $req->memis_end_year){
            
            $key = ($having != '') ? 'AND' : ' HAVING ';
            $field .= ' ,YEAR(mem_date_elected) AS year ';
            $having .= ' '.$key. ' year BETWEEN '. $req->memis_start_year .' AND '. $req->memis_end_year .' '; 
        }

        $sub_q .= 'SELECT count(pp_usr_id) AS total, '.
        'CASE  '.
        'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 20 AND 31 then "1" '.
        'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 30 AND 41 then "2" '.
        'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 40 AND 51 then "3" '.
        'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 50 AND 61 then "4" '.
        'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 60 AND 71 then "5" '.
        'ELSE "6" END AS "range" '. $field .
        'FROM new_dbskms.tblpersonal_profiles '.
        'INNER JOIN new_dbskms.tblusers ON usr_id = pp_usr_id '.
        'LEFT JOIN new_dbskms.tblmembers ON mem_usr_id = usr_id '. 
        'WHERE pp_date_of_birth LIKE \'%-%\' '. 
        'AND pp_date_of_birth > 0 '. 
        'AND usr_grp_id LIKE 3 '. $where . $deceased . $group_by . $having;


        $select .= 'SELECT count(total) AS total, '. 
        '(SELECT age_range FROM new_dbskms.tblage_ranges WHERE age_id = tmp.range) AS label FROM '. 
        '( ' . $sub_q . ') AS tmp GROUP BY tmp.range';

        return DB::select($select);
    }

    // all island for stacj
    public static function get_all_island($id, $req){

        $select = '';
        $where = '';
        $join = '';
        $group_by = '';
        $order_by = '';
        $having = '';
        $deceased = ' AND mem_status != 3 ';
        $key = '';
        $field = '';
        $sub_q = '';

        $group_by .= ' GROUP BY pp_usr_id ';
        $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
        $join .= ' LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';
        $join .= ' JOIN new_dbskms.tblregions ON region_id = emp_region ';

        $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id) '; 

        if($req->memis_division > 0){
            
            $where .= ' AND mem_div_id LIKE '. $id;  
        }

        if($req->memis_province > 0){
       
            $where .= ' AND emp_province LIKE '. $id; 
            $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id) ';   
        }

        if($req->memis_city > 0){
       
            $where .= ' AND emp_city LIKE '. $id; 
            $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id) ';   
        }

        if($req->memis_country > 0){

            $where .= ' AND emp_country LIKE '. $req->memis_country;
            $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = usr_id)';    
        
        }
        
        if($req->memis_sex > 0){ // sex

            $where .= ' AND pp_sex LIKE '. $id;

        }

        if($req->memis_category > 0){ // category

            $where .= ' AND mem_type LIKE '. $id;

        }

        if($req->memis_status > 0){ // status

                $where .= ' AND mem_status LIKE ' . $id;
        
        }

        if($req->memis_educ > 0){ // educ 

            $key = ($having != '') ? 'AND' : ' HAVING ';
            $field .= ' , MIN(adp_highest) as minadp ';
            $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
            $having .= ' '.$key.' minadp = ' . $id . ' ';

        }

        if($req->memis_age > 0){ // age 

                $key = ($having != '') ? 'AND' : 'HAVING';
                $field .= ' ,(YEAR(CURDATE()) - YEAR(pp_date_of_birth)) AS age ';
                $where .= ' AND pp_date_of_birth like \'%-%\' and pp_date_of_birth > 0 ';
                
                if($id == 1){
                    $having .= ' '.$key.' age > 20 AND age < 31 ';
                }else if($id == 2){
                    $having .= ' '.$key.' age > 30 AND age < 41 ';
                }else if($id == 3){
                    $having .= ' '.$key.' age > 40 AND age < 51 ';
                }else if($id == 4){
                    $having .= ' '.$key.' age > 50 AND age < 61 ';
                }else if($id == 5){
                    $having .= ' '.$key.' age > 60 AND age < 71 ';
                }else if($id == 6){
                    $having .= ' '.$key.' age > 70 ';
                }else{
                    $having .= ' '.$key.' age > 0 ';
                }
        }
   
        if($req->memis_start_year > 0 && $req->memis_end_year){
            
            $key = ($having != '') ? 'AND' : ' HAVING ';
            $field .= ' ,YEAR(mem_date_elected) AS year ';
            $having .= ' '.$key. ' year BETWEEN '. $req->memis_start_year .' AND '. $req->memis_end_year .' '; 
        }

        $sub_q .= 'SELECT region_id, count(mem_usr_id) AS total, region_group as bar_id, '. 
                'CASE WHEN region_group = "1" THEN "Luzon"  '.
                'WHEN region_group ="2" THEN "Visayas" '.
                'ELSE "Mindanao" END as island '. $field .
                'FROM new_dbskms.tblmembers '. 
                'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
                'WHERE usr_grp_id LIKE 3 '. $where . $deceased . $group_by . $having;

        $select .= 'SELECT count(total) AS total, tmp.island AS label FROM '. 
        '( ' . $sub_q . ') as tmp GROUP BY bar_id';
                

        return DB::select($select);
    }

    static function do_advance_stack_column_graph(){
        $result_array = array();

        $output = DB::connection('dbskms')->table('tbldivisions')
            ->select('div_id')
            ->get();

            
        foreach($output as $row){  
            $result_array[] =  array($row->div_id => array('Male' => Member::get_advance_region($row->div_id, '1')));
            // $result_array[] =  array($row->div_id => array('Female' => Member::get_advance_region($row->div_id, '2')));
        }
        
        foreach($output as $row){  
            // $result_array[] =  array($row->div_id => array('Male' => Member::get_advancec_region($row->div_id, '1')));
            $result_array[] =  array($row->div_id => array('Female' => Member::get_advance_region($row->div_id, '2')));
        }


      
        return $result_array;

    }

    // column graph
    static function do_column_graph($req){ 
        $result_array = array();

        if($req->memis_division == '999'){
            
            $output = DB::connection('dbskms')->table('tbldivisions')
            ->select('div_id','div_name')
            ->get();

            foreach($output as $row){  
                $result_array[] = array($row->div_id => array($row->div_name => Member::get_spec_division($row->div_id, $req)));
            }
        }

        if($req->memis_category == '999'){

            $query = DB::connection('dbskms')->table('tblmembership_types')
            ->select('membership_type_id','membership_type_name');

                
            if($req->memis_category == '999'){
                $output =  $query->get();
            }else{
                $output = $query->where('membership_type_id', $req->memis_category)
                ->get();
            }

            foreach($output as $row){  
                $result_array[] = array($row->membership_type_id => array($row->membership_type_name => Member::get_spec_category($row->membership_type_id, $req)));
            }
            
        }

        if($req->memis_status == '999'){
            
            $query = DB::connection('dbskms')->table('tblmembership_status')
            ->select('membership_status_id','membership_status_name');

            if($req->memis_status == '999'){
                $output = $query->get();
            }else{
                $output = $query->where('membership_status_id', $req->memis_status)
                ->get();
            }
            
            foreach($output as $row){  
                $result_array[] = array($row->membership_status_id => array($row->membership_status_name => Member::get_spec_status($row->membership_status_id, $req)));
            }
        }

        if($req->memis_sex == '999'){

            $query = DB::connection('dbskms')->table('tblsex')
            ->select('s_id','sex');

            if($req->memis_sex == '999'){
                $output = $query->get();
            }else{
                $output = $query->where('s_id', $req->memis_sex)
                ->get();

            }   
            
            foreach($output as $row){  
                $result_array[] = array($row->s_id => array($row->sex => Member::get_spec_sex($row->s_id, $req)));
            }
        }

        if($req->memis_country == '999'){

            $query = DB::connection('dbskms')->table('tblcountries')
            ->select('country_id','country_name');

            if($req->memis_country == '999'){
                $output = $query->get();
            }else{
                $output = $query->where('country_id', $req->memis_country)
                ->get();

            }   
            
            foreach($output as $row){  
                $result_array[] = array($row->country_id => array($row->country_name => Member::get_spec_country($row->country_id, $req)));
            }
        }

        if($req->memis_region == '999'){

            $query = DB::connection('dbskms')->table('tblregions')
            ->select('region_id','region_name');

            if($req->memis_region == '999'){
                $output = $query->get();
            }else{
                $output = $query->where('region_id', $req->memis_region)
                ->get();

            }   

            foreach($output as $row){  
                $result_array[] = array($row->region_id => array($row->region_name => Member::get_spec_region($row->region_id, $req)));
            }
        }

        if($req->memis_province == '999'){

            $query = DB::connection('dbskms')->table('tblprovinces')
            ->select('province_id','province_name');

            if($req->memis_province == '999'){
                $output = $query->where('province_region_id', $req->memis_region)->get();
            }else{
                $output = $query->where('province_id', $req->memis_province)
                ->get();

            }   

            foreach($output as $row){  
                $result_array[] = array($row->province_id => array($row->province_name => Member::get_spec_province($row->province_id, $req)));
            }
        }

        if($req->memis_city == '999'){
            $query = DB::connection('dbskms')->table('tblcities')
            ->select('city_id','city_name');

            if($req->memis_city == '999'){
                $output = $query->where('city_province_id', $req->memis_province)->get();
            }else{
                $output = $query->where('city_id', $req->memis_city)
                ->get();

            }   

            foreach($output as $row){  
                $result_array[] = array($row->city_id => array($row->city_name => Member::get_spec_city($row->city_id, $req)));
            }
        }

        if($req->memis_educ == '999'){

            $query = DB::connection('dbskms')->table('tbldegree_types')
            ->select('deg_id','deg_name');

            if($req->memis_educ == '999'){
                $output = $query->get();
            }else{
                $output = $query->where('deg_id', $req->memis_educ)
                ->get();

            }   
            
            foreach($output as $row){  
                $result_array[] = array($row->deg_id => array($row->deg_name => Member::get_spec_educ($row->deg_id, $req)));
            }
        }

        if($req->memis_age == '999'){
            $query = DB::connection('dbskms')->table('tblage_ranges')
            ->select('age_id','age_range')
            ->orderBy('age_id', 'asc');


            if($req->memis_age == '999'){
                $output = $query->get();
            }else{
                $output = $query->where('age_id', $req->memis_age)
                ->get();

            }   
            
            foreach($output as $row){  
                $result_array[] = array($row->age_id => array($row->age_range => Member::get_spec_age($row->age_id, $req)));
            }
        }

        if($req->memis_island == '999'){
            $query = DB::connection('dbskms')->table('tblregions')
            ->select('region_group',
            DB::raw('CASE WHEN region_group = 1 then "Luzon" WHEN region_group = 2 then "Visayas" else "Mindanao" end as island'))
            ->groupBy('region_group')
            ->orderBy('region_group', 'asc');


            if($req->memis_island == '999'){
                $output = $query->get();
            }else{
                $output = $query->where('age_id', $req->memis_age)
                ->get();

            }   
            
            foreach($output as $row){  
                $result_array[] = array($row->region_group => array($row->island => Member::get_spec_island($row->region_group, $req)));
            }
        }


        return $result_array;
    } 

    // line graph
    static function do_line_graph($req){ 
        $result_array = array();

        if($req->memis_division == '999'){
            
            $output = DB::connection('dbskms')->table('tbldivisions')
            ->select('div_id','div_name')
            ->get();

            foreach($output as $row){  
                $result_array[] = array($row->div_id => array($row->div_name => Member::get_line_division($row->div_id, $req)));
            }
        }

        if($req->memis_region == '999'){

            $query = DB::connection('dbskms')->table('tblregions')
            ->select('region_id','region_name');

            if($req->memis_region == '999'){
                $output = $query->get();
            }else{
                $output = $query->where('region_id', $req->memis_region)
                ->get();

            }   

            foreach($output as $row){  
                $result_array[] = array($row->region_id => array($row->region_name => Member::get_line_region($row->region_id, $req)));
            }
        }


        return $result_array;
    } 

    public static function get_advance_region($id, $flag){
        
        $deceased = ' AND mem_status != 3 ';

        // 1st
        // $select = 'SELECT COUNT(*) AS total, concat("Division ",div_number) AS division, sex '.
        // 'FROM new_dbskms.tblpersonal_profiles '.
        // 'INNER JOIN new_dbskms.tblusers ON usr_id = pp_usr_id '.
        // 'LEFT JOIN new_dbskms.tblmembers ON mem_usr_id = usr_id '. 
        // 'LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id '. 
        // 'LEFT JOIN new_dbskms.tblsex ON s_id = pp_sex '. 
        // 'LEFT JOIN new_dbskms.tbldivisions ON div_id = mem_div_id '. 
        // 'WHERE pp_sex = '. $flag . 
        // ' AND emp_region LIKE '. $id . 
        // ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments) '.
        // 'AND usr_grp_id LIKE 3 '. $deceased .
        // 'GROUP BY mem_div_id ';

        // //2nd

        // $select = 'SELECT CONCAT("Division ",div_number) as division, '.  
        // '(SELECT count(*) '. 
        // 'FROM new_dbskms.tblpersonal_profiles '.  
        // 'INNER JOIN new_dbskms.tblusers ON usr_id = pp_usr_id '.  
        // 'LEFT JOIN new_dbskms.tblmembers ON mem_usr_id = usr_id '.   
        // 'LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id '.  
        // 'WHERE pp_sex LIKE '. $flag .  
        // ' AND emp_region LIKE '. $id .
        // ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments) '. $deceased .
        // ' AND usr_grp_id LIKE 3  AND mem_status != 3 AND mem_div_id LIKE div_id) AS total '. 
        // 'from new_dbskms.tbldivisions';

        //3rd
        $select = 'SELECT region_id, '.
        '(SELECT count(*) '.
        'FROM new_dbskms.tblpersonal_profiles '.
        'INNER JOIN new_dbskms.tblusers ON usr_id = pp_usr_id '.
        'LEFT JOIN new_dbskms.tblmembers ON mem_usr_id = usr_id '. 
        'LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id '.
        'WHERE pp_sex LIKE '. $flag .
        ' AND mem_div_id LIKE '. $id .
        ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments) '. $deceased .
        ' AND usr_grp_id LIKE 3  AND mem_status != 3 and emp_region like region_id) as total '.
        'FROM new_dbskms.tblregions';
        
        return DB::select($select);
    
    }

    // specific age for column with year
    public static function get_spec_age($id, $req){

        $select = '';
        $years = $req->memis_end_year - $req->memis_start_year;
        $year = $req->memis_start_year;
        $union = '';
        $deceased = ' AND mem_status != 3 ';
        // $where = ' AND mem_div_id LIKE '. $id;
        $where = '';
        $join = '';
        $case = '';
        $field = '';
        $having = '';
        $group_by = '';

        
        $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
        $join .= ' JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';

        if($req->memis_category > 0 && $req->memis_category != '999'){
            // $join .= ' JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';
            $where .= ' AND mem_type LIKE ' . $req->memis_category . ' ';
        }
        
        if($req->memis_status > 0 && $req->memis_status != '999'){
            // $join .= ' JOIN new_dbskms.tblmembership_status ON membership_status_id = mem_status ';
            $where .= ' AND mem_status LIKE ' . $req->memis_status . ' ';
            $deceased = '';
        }
        
        if($req->memis_sex > 0 && $req->memis_sex != '999'){
            $where .= ' AND pp_sex LIKE ' . $req->memis_sex . ' ';
        }
        
        if($req->memis_educ > 0 && $req->memis_educ != '999'){

            $key = ($having != '') ? 'AND' : 'HAVING';
            $field .= ' , MIN(adp_highest) as minadp ';
            $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
            $having .= ' '.$key.' minadp = ' . $req->memis_educ . ' ';
        }
        
        
        if($req->memis_region > 0 && $req->memis_region != '999'){

            
            if($req->memis_province > 0 && $req->memis_province != '999'){
                
                if($req->memis_city > 0 && $req->memis_city != '999'){
                    $where .= ' AND emp_city LIKE '. $req->memis_city .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments)';
                }else{
                    $where .= ' AND emp_province LIKE '. $req->memis_province .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments)';
                }
            }
            else{
                $where .= ' AND emp_region LIKE '. $req->memis_region .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments)';    
            }

        }

        if($req->memis_country > 0 && $req->memis_country != '999'){

            $where .= ' AND emp_country LIKE '. $req->memis_country .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments)';    
          
        }

        $select .= 'SELECT year as label, COUNT(pp_usr_id) as total '. $field . 
            'FROM( ';


        for($i=0; $i<=$years;$i++){

            if($i < $years){
                $union = 'UNION';
            }else{
                $union = '';
            }


            $select .= 'SELECT pp_usr_id, YEAR(mem_date_elected) AS year,'.
            'CASE  '.
            'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 20 AND 31 then "1" '.
            'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 30 AND 41 then "2" '.
            'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 40 AND 51 then "3" '.
            'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 50 AND 61 then "4" '.
            'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 60 AND 71 then "5" '.
            'ELSE "6" END AS "range" '. $field .
            'FROM new_dbskms.tblpersonal_profiles '.
            'INNER JOIN new_dbskms.tblusers ON usr_id = pp_usr_id '.
            'LEFT JOIN new_dbskms.tblmembers ON mem_usr_id = usr_id '. 
            'WHERE pp_date_of_birth like \'%-%\' '. 
            'AND pp_date_of_birth > 0 '. 
            'AND usr_grp_id LIKE 3 '. $where . $deceased .
            'AND YEAR(mem_date_elected) = '. $year . ' ' . $having . ' ' . $group_by . ' ' . $union . ' ';


            $year++;
        }

        $select .= ') AS tmp WHERE tmp.range = '. $id. ' GROUP BY year ORDER BY year ';

        return DB::select($select);
        
    }

    // specific education for column with year
    public static function get_spec_educ($id, $req){

        $select = '';
        $years = $req->memis_end_year - $req->memis_start_year;
        $year = $req->memis_start_year;
        $union = '';
        $deceased = ' AND mem_status != 3 ';
        // $where = ' AND mem_div_id LIKE '. $id;
        $where = '';
        $join = '';
        $case = '';
        $field = '';
        $having = ' HAVING minadp = ' . $id . ' ';
        $group_by = '';

        
        $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
        
        $join .= ' JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';

        if($req->memis_category > 0 && $req->memis_category != '999'){
            // $join .= ' JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';
            $where .= ' AND mem_type LIKE ' . $req->memis_category . ' ';
        }
        
        if($req->memis_status > 0 && $req->memis_status != '999'){
            // $join .= ' JOIN new_dbskms.tblmembership_status ON membership_status_id = mem_status ';
            $where .= ' AND mem_status LIKE ' . $req->memis_status . ' ';
            $deceased = '';
        }
        
        if($req->memis_sex > 0 && $req->memis_sex != '999'){
            $where .= ' AND pp_sex LIKE ' . $req->memis_sex . ' ';
        }

        if($req->memis_age > 0 && $req->memis_age != '999'){

            $field .= ' , (YEAR(CURDATE()) - YEAR(pp_date_of_birth)) as age ';

            $key = ($having != '') ? 'AND' : 'HAVING';

            if($req->memis_age == 1){
                $having .= ' '.$key.' age > 20 AND age < 31 ';
            }else if($req->memis_age == 2){
                $having .= ' '.$key.'  age > 30 AND age < 41 ';
            }else if($req->memis_age == 3){
                $having .= ' '.$key.'  age > 40 AND age < 51 ';
            }else if($req->memis_age == 4){
                $having .= ' '.$key.'  age > 50 AND age < 61 ';
            }else if($req->memis_age == 5){
                 $having .= ' '.$key.'  age > 60 AND age < 71 ';
            }else if($req->memis_age == 6){
                $having .= ' '.$key.'  age > 70 ';
            }else{
                $having .= ' '.$key.'  age > 0 ';
            }

        }
        
        if($req->memis_region > 0 && $req->memis_region != '999'){

            
            if($req->memis_province > 0 && $req->memis_province != '999'){
                
                if($req->memis_city > 0 && $req->memis_city != '999'){
                    $where .= ' AND emp_city LIKE '. $req->memis_city .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments)';
                }else{
                    $where .= ' AND emp_province LIKE '. $req->memis_province .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments)';
                }
            }
            else{
                $where .= ' AND emp_region LIKE '. $req->memis_region .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments)';    
            }

        }

        if($req->memis_country > 0 && $req->memis_country != '999'){

            $where .= ' AND emp_country LIKE '. $req->memis_country .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments)';    
          
        }

        
        
        // if($req->memis_educ > 0 && $req->memis_educ != '999'){

           
        // }
    
        $field .= ' , MIN(adp_highest) as minadp ';
        $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
        $group_by .= ' GROUP BY adp_highest ';
    
        for($i=0; $i<=$years;$i++){
            if($i < $years){
                $union = 'UNION';
            }else{
                $union = '';
            }

            $select .= 'SELECT count(mem_usr_id) as total, IFNULL(YEAR(mem_date_elected),'. $year .') AS label '. $field . 
            'FROM new_dbskms.tblmembers '. 
            'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
            'WHERE usr_grp_id LIKE 3 '. $where . $deceased .
            'AND YEAR(mem_date_elected) = '. $year . ' ' . $group_by . ' ' . $having . ' ' . $union . ' ';

            $year++;
        }

        return DB::select($select);

        // $deceased = ' AND mem_status != 3 ';
        // $where = ' AND adp_highest LIKE '. $id;


        // return DB::select('SELECT year as label, COUNT(adp_usr_id) as total '. 
        // 'FROM( '.  
        // 'SELECT adp_usr_id, adp_highest,  YEAR(mem_date_elected) AS year, '. 
        // 'CASE WHEN MIN(adp_highest) = 1 THEN "1" '.
        // 'WHEN MIN(adp_highest) = 2 THEN "2" '. 
        // 'WHEN MIN(adp_highest) = 3 THEN "3" '. 
        // 'ELSE "4" END AS "educ" '. 
        // 'FROM new_dbskms.tblacademic_degree_profiles '. 
        // 'INNER JOIN new_dbskms.tblusers ON usr_id = adp_usr_id '. 
        // 'LEFT JOIN new_dbskms.tblmembers ON mem_usr_id = usr_id '.
        // 'WHERE usr_grp_id LIKE 3 '. $deceased .
        // 'GROUP BY adp_usr_id HAVING year BETWEEN '. $req->memis_start_year .' AND '. $req->memis_end_year .''. 
        // ') AS tmp WHERE tmp.educ = '. $id .' GROUP BY year ORDER BY year');
    }

    
    // specific island for column with year
    public static function get_spec_island($id, $req){

        $select = '';
        $years = $req->memis_end_year - $req->memis_start_year;
        $year = $req->memis_start_year;
        $union = '';
        $deceased = ' AND mem_status != 3 ';
        $where = ' AND region_group LIKE '. $id;
        $join = '';
        $case = '';
        $field = '';
        $having = '';
        $group_by = '';

        
        $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
        $join .= ' JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';
        $join .= ' JOIN new_dbskms.tblregions ON region_id = emp_region ';

        $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments)';   

        if($req->memis_division > 0 && $req->memis_division != '999'){
            // $join .= ' JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';
            $where .= ' AND mem_div_id LIKE ' . $req->memis_division . ' ';
        }

        if($req->memis_status > 0 && $req->memis_status != '999'){
            // $join .= ' JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';
            $where .= ' AND mem_status LIKE ' . $req->memis_status . ' ';
            $deceased = '';
        }
        
        if($req->memis_category > 0 && $req->memis_category != '999'){
            // $join .= ' JOIN new_dbskms.tblmembership_status ON membership_status_id = mem_status ';
            $where .= ' AND mem_type LIKE ' . $req->memis_category . ' ';
            $deceased = '';
        }
        
        if($req->memis_sex > 0 && $req->memis_sex != '999'){
            $where .= ' AND pp_sex LIKE ' . $req->memis_sex . ' ';
        }

        if($req->memis_age > 0 && $req->memis_age != '999'){

            $field .= ' , (YEAR(CURDATE()) - YEAR(pp_date_of_birth)) as age ';

            $key = ($having != '') ? 'AND' : 'HAVING';

            if($req->memis_age == 1){
                $having .= ' '.$key.' age > 20 AND age < 31 ';
            }else if($req->memis_age == 2){
                $having .= ' '.$key.'  age > 30 AND age < 41 ';
            }else if($req->memis_age == 3){
                $having .= ' '.$key.'  age > 40 AND age < 51 ';
            }else if($req->memis_age == 4){
                $having .= ' '.$key.'  age > 50 AND age < 61 ';
            }else if($req->memis_age == 5){
                 $having .= ' '.$key.'  age > 60 AND age < 71 ';
            }else if($req->memis_age == 6){
                $having .= ' '.$key.'  age > 70 ';
            }else{
                $having .= ' '.$key.'  age > 0 ';
            }

        }
        
        if($req->memis_educ > 0 && $req->memis_educ != '999'){

            $key = ($having != '') ? 'AND' : 'HAVING';
            $field .= ' , MIN(adp_highest) as minadp ';
            $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
            $having .= ' '.$key.' minadp = ' . $req->memis_educ . ' ';
        }

        for($i=0; $i<=$years;$i++){
            if($i < $years){
                $union = 'UNION';
            }else{
                $union = '';
            }

            $select .= 'SELECT count(mem_usr_id) as total, IFNULL(YEAR(mem_date_elected),'. $year .') AS label '. $field . 
            'FROM new_dbskms.tblmembers '. 
            'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
            'WHERE usr_grp_id LIKE 3 '. $where . $deceased .
            ' AND YEAR(mem_date_elected) = '. $year . ' ' . $having . ' ' . $group_by . ' ' . $union . ' ';

            $year++;
        }

        return DB::select($select);  
    }

    // specific region for column with year
    public static function get_spec_region($id, $req){

        $select = '';
        $years = $req->memis_end_year - $req->memis_start_year;
        $year = $req->memis_start_year;
        $union = '';
        $deceased = ' AND mem_status != 3 ';
        $where = ' AND emp_region LIKE '. $id;
        $join = '';
        $case = '';
        $field = '';
        $having = '';
        $group_by = '';

        
        $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
        $join .= ' JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';
        $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments)';   

        if($req->memis_division > 0 && $req->memis_division != '999'){
            // $join .= ' JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';
            $where .= ' AND mem_div_id LIKE ' . $req->memis_division . ' ';
        }

        if($req->memis_status > 0 && $req->memis_status != '999'){
            // $join .= ' JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';
            $where .= ' AND mem_status LIKE ' . $req->memis_status . ' ';
            $deceased = '';
        }
        
        if($req->memis_category > 0 && $req->memis_category != '999'){
            // $join .= ' JOIN new_dbskms.tblmembership_status ON membership_status_id = mem_status ';
            $where .= ' AND mem_type LIKE ' . $req->memis_category . ' ';
            $deceased = '';
        }
        
        if($req->memis_sex > 0 && $req->memis_sex != '999'){
            $where .= ' AND pp_sex LIKE ' . $req->memis_sex . ' ';
        }

        if($req->memis_age > 0 && $req->memis_age != '999'){

            $field .= ' , (YEAR(CURDATE()) - YEAR(pp_date_of_birth)) as age ';

            $key = ($having != '') ? 'AND' : 'HAVING';

            if($req->memis_age == 1){
                $having .= ' '.$key.' age > 20 AND age < 31 ';
            }else if($req->memis_age == 2){
                $having .= ' '.$key.'  age > 30 AND age < 41 ';
            }else if($req->memis_age == 3){
                $having .= ' '.$key.'  age > 40 AND age < 51 ';
            }else if($req->memis_age == 4){
                $having .= ' '.$key.'  age > 50 AND age < 61 ';
            }else if($req->memis_age == 5){
                 $having .= ' '.$key.'  age > 60 AND age < 71 ';
            }else if($req->memis_age == 6){
                $having .= ' '.$key.'  age > 70 ';
            }else{
                $having .= ' '.$key.'  age > 0 ';
            }

        }
        
        if($req->memis_educ > 0 && $req->memis_educ != '999'){

            $key = ($having != '') ? 'AND' : 'HAVING';
            $field .= ' , MIN(adp_highest) as minadp ';
            $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
            $having .= ' '.$key.' minadp = ' . $req->memis_educ . ' ';
        }

        for($i=0; $i<=$years;$i++){
            if($i < $years){
                $union = 'UNION';
            }else{
                $union = '';
            }

            $select .= 'SELECT count(mem_usr_id) as total, IFNULL(YEAR(mem_date_elected),'. $year .') AS label '. $field . 
            'FROM new_dbskms.tblmembers '. 
            'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
            'WHERE usr_grp_id LIKE 3 '. $where . $deceased .
            ' AND YEAR(mem_date_elected) = '. $year . ' ' . $having . ' ' . $group_by . ' ' . $union . ' ';

            $year++;
        }

        return DB::select($select);  
    }

    // specific province for column with year
    public static function get_spec_province($id, $req){

        $select = '';
        $years = $req->memis_end_year - $req->memis_start_year;
        $year = $req->memis_start_year;
        $union = '';
        $deceased = ' AND mem_status != 3 ';
        $where = ' AND emp_province LIKE '. $id;
        $join = '';
        $case = '';
        $field = '';
        $having = '';
        $group_by = '';

        
        $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
        $join .= ' JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';
        $join .= ' JOIN new_dbskms.tblprovinces on province_region_id = emp_region ';
        $where .= ' AND emp_region LIKE '. $req->memis_region;

        if($req->memis_division > 0 && $req->memis_division != '999'){
            // $join .= ' JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';
            $where .= ' AND mem_div_id LIKE ' . $req->memis_division . ' ';
        }
        
        if($req->memis_status > 0 && $req->memis_status != '999'){
            // $join .= ' JOIN new_dbskms.tblmembership_status ON membership_status_id = mem_status ';
            $where .= ' AND mem_status LIKE ' . $req->memis_status . ' ';
            $deceased = '';
        }
        
        if($req->memis_sex > 0 && $req->memis_sex != '999'){
            $where .= ' AND pp_sex LIKE ' . $req->memis_sex . ' ';
        }

        if($req->memis_age > 0 && $req->memis_age != '999'){

            $field .= ' , (YEAR(CURDATE()) - YEAR(pp_date_of_birth)) as age ';

            $key = ($having != '') ? 'AND' : 'HAVING';

            if($req->memis_age == 1){
                $having .= ' '.$key.' age > 20 AND age < 31 ';
            }else if($req->memis_age == 2){
                $having .= ' '.$key.'  age > 30 AND age < 41 ';
            }else if($req->memis_age == 3){
                $having .= ' '.$key.'  age > 40 AND age < 51 ';
            }else if($req->memis_age == 4){
                $having .= ' '.$key.'  age > 50 AND age < 61 ';
            }else if($req->memis_age == 5){
                 $having .= ' '.$key.'  age > 60 AND age < 71 ';
            }else if($req->memis_age == 6){
                $having .= ' '.$key.'  age > 70 ';
            }else{
                $having .= ' '.$key.'  age > 0 ';
            }

        }
        
        if($req->memis_educ > 0 && $req->memis_educ != '999'){

            $key = ($having != '') ? 'AND' : 'HAVING';
            $field .= ' , MIN(adp_highest) as minadp ';
            $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
            $having .= ' '.$key.' minadp = ' . $req->memis_educ . ' ';
        }

        if($req->memis_country > 0 && $req->memis_country != '999'){

            $where .= ' AND emp_country LIKE '. $req->memis_country .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments)';    
          
        }

        for($i=0; $i<=$years;$i++){
            if($i < $years){
                $union = 'UNION';
            }else{
                $union = '';
            }

            $select .= 'SELECT count(mem_usr_id) as total, IFNULL(YEAR(mem_date_elected),'. $year .') AS label '. $field . 
            'FROM new_dbskms.tblmembers '. 
            'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
            'WHERE usr_grp_id LIKE 3 '. $where . $deceased .
            'AND YEAR(mem_date_elected) = '. $year . ' ' . $having . ' ' . $group_by . ' ' . $union . ' ';

            $year++;
        }

        return DB::select($select);
    }

    // specific city for column with year
    public static function get_spec_city($id, $req){
        
        $select = '';
        $years = $req->memis_end_year - $req->memis_start_year;
        $year = $req->memis_start_year;
        $union = '';
        $deceased = ' AND mem_status != 3 ';
        $where = ' AND emp_city LIKE '. $id;
        $join = '';
        $case = '';
        $field = '';
        $having = '';
        $group_by = '';

        
        $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
        $join .= ' JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';
        $join .= ' JOIN new_dbskms.tblcities on city_province_id = emp_province ';
        $where .= ' AND emp_province LIKE '. $req->memis_province;

        if($req->memis_division > 0 && $req->memis_division != '999'){
            // $join .= ' JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';
            $where .= ' AND mem_div_id LIKE ' . $req->memis_division . ' ';
        }
        
        if($req->memis_status > 0 && $req->memis_status != '999'){
            // $join .= ' JOIN new_dbskms.tblmembership_status ON membership_status_id = mem_status ';
            $where .= ' AND mem_status LIKE ' . $req->memis_status . ' ';
            $deceased = '';
        }

        if($req->memis_category > 0 && $req->memis_category != '999'){
            // $join .= ' JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';
            $where .= ' AND mem_type LIKE ' . $req->memis_category . ' ';
        }
        
        if($req->memis_sex > 0 && $req->memis_sex != '999'){
            $where .= ' AND pp_sex LIKE ' . $req->memis_sex . ' ';
        }

        if($req->memis_age > 0 && $req->memis_age != '999'){

            $field .= ' , (YEAR(CURDATE()) - YEAR(pp_date_of_birth)) as age ';

            $key = ($having != '') ? 'AND' : 'HAVING';

            if($req->memis_age == 1){
                $having .= ' '.$key.' age > 20 AND age < 31 ';
            }else if($req->memis_age == 2){
                $having .= ' '.$key.'  age > 30 AND age < 41 ';
            }else if($req->memis_age == 3){
                $having .= ' '.$key.'  age > 40 AND age < 51 ';
            }else if($req->memis_age == 4){
                $having .= ' '.$key.'  age > 50 AND age < 61 ';
            }else if($req->memis_age == 5){
                 $having .= ' '.$key.'  age > 60 AND age < 71 ';
            }else if($req->memis_age == 6){
                $having .= ' '.$key.'  age > 70 ';
            }else{
                $having .= ' '.$key.'  age > 0 ';
            }

        }
        
        if($req->memis_educ > 0 && $req->memis_educ != '999'){

            $key = ($having != '') ? 'AND' : 'HAVING';
            $field .= ' , MIN(adp_highest) as minadp ';
            $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
            $having .= ' '.$key.' minadp = ' . $req->memis_educ . ' ';
        }

        if($req->memis_country > 0 && $req->memis_country != '999'){

            $where .= ' AND emp_country LIKE '. $req->memis_country .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments)';    
          
        }

        for($i=0; $i<=$years;$i++){
            if($i < $years){
                $union = 'UNION';
            }else{
                $union = '';
            }

            $select .= 'SELECT count(mem_usr_id) as total, IFNULL(YEAR(mem_date_elected),'. $year .') AS label '. $field . 
            'FROM new_dbskms.tblmembers '. 
            'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
            'WHERE usr_grp_id LIKE 3 '. $where . $deceased .
            'AND YEAR(mem_date_elected) = '. $year . ' ' . $having . ' ' . $group_by . ' ' . $union . ' ';

            $year++;
        }

        return DB::select($select);
     
    }

    // specific country for column with year
    public static function get_spec_country($id, $req){

        $select = '';
        $years = $req->memis_end_year - $req->memis_start_year;
        $year = $req->memis_start_year;
        $union = '';
        $deceased = ' AND mem_status != 3 ';
        $where = ' AND emp_country LIKE '. $id;
        $join = '';
        $case = '';
        $field = '';
        $having = '';
        $group_by = '';

        
        $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
        $join .= ' JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';
        $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments)';    

        if($req->memis_division > 0 && $req->memis_division != '999'){
            // $join .= ' JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';
            $where .= ' AND mem_div_id LIKE ' . $req->memis_division . ' ';
        }
        
        if($req->memis_status > 0 && $req->memis_status != '999'){
            // $join .= ' JOIN new_dbskms.tblmembership_status ON membership_status_id = mem_status ';
            $where .= ' AND mem_status LIKE ' . $req->memis_status . ' ';
            $deceased = '';
        }
        
        if($req->memis_sex > 0 && $req->memis_sex != '999'){
            $where .= ' AND pp_sex LIKE ' . $req->memis_sex . ' ';
        }

        if($req->memis_age > 0 && $req->memis_age != '999'){

            $field .= ' , (YEAR(CURDATE()) - YEAR(pp_date_of_birth)) as age ';

            $key = ($having != '') ? 'AND' : 'HAVING';

            if($req->memis_age == 1){
                $having .= ' '.$key.' age > 20 AND age < 31 ';
            }else if($req->memis_age == 2){
                $having .= ' '.$key.'  age > 30 AND age < 41 ';
            }else if($req->memis_age == 3){
                $having .= ' '.$key.'  age > 40 AND age < 51 ';
            }else if($req->memis_age == 4){
                $having .= ' '.$key.'  age > 50 AND age < 61 ';
            }else if($req->memis_age == 5){
                 $having .= ' '.$key.'  age > 60 AND age < 71 ';
            }else if($req->memis_age == 6){
                $having .= ' '.$key.'  age > 70 ';
            }else{
                $having .= ' '.$key.'  age > 0 ';
            }

        }
        
        if($req->memis_educ > 0 && $req->memis_educ != '999'){

            $key = ($having != '') ? 'AND' : 'HAVING';
            $field .= ' , MIN(adp_highest) as minadp ';
            $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
            $having .= ' '.$key.' minadp = ' . $req->memis_educ . ' ';
        }
        
        for($i=0; $i<=$years;$i++){
            if($i < $years){
                $union = 'UNION';
            }else{
                $union = '';
            }

            $select .= 'SELECT count(mem_usr_id) as total, IFNULL(YEAR(mem_date_elected),'. $year .') AS label '. $field . 
            'FROM new_dbskms.tblmembers '. 
            'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
            'WHERE usr_grp_id LIKE 3 '. $where . $deceased .
            'AND YEAR(mem_date_elected) = '. $year . ' ' . $having . ' ' . $group_by . ' ' . $union . ' ';

            $year++;
        }

        return DB::select($select);
  
    }

    // specific sex for column with year
    public static function get_spec_sex($id, $req){

        $select = '';
        $years = $req->memis_end_year - $req->memis_start_year;
        $year = $req->memis_start_year;
        $union = '';
        $deceased = ' AND mem_status != 3 ';
        $where = ' AND pp_sex LIKE '. $id;
        $join = '';
        $case = '';
        $field = '';
        $having = '';
        $group_by = '';

        
        $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
        $join .= ' JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';

        if($req->memis_division > 0 && $req->memis_division != '999'){
            // $join .= ' JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';
            $where .= ' AND mem_div_id LIKE ' . $req->memis_division . ' ';
        }
        
        if($req->memis_category > 0 && $req->memis_category != '999'){
            // $join .= ' JOIN new_dbskms.tblmembership_status ON membership_status_id = mem_status ';
            $where .= ' AND mem_type LIKE ' . $req->memis_category . ' ';
        }
        
        if($req->memis_status > 0 && $req->memis_status != '999'){
            $where .= ' AND mem_status LIKE ' . $req->memis_status . ' ';
            $deceased = '';
        }

        if($req->memis_age > 0 && $req->memis_age != '999'){

            $field .= ' , (YEAR(CURDATE()) - YEAR(pp_date_of_birth)) as age ';

            $key = ($having != '') ? 'AND' : 'HAVING';

            if($req->memis_age == 1){
                $having .= ' '.$key.' age > 20 AND age < 31 ';
            }else if($req->memis_age == 2){
                $having .= ' '.$key.'  age > 30 AND age < 41 ';
            }else if($req->memis_age == 3){
                $having .= ' '.$key.'  age > 40 AND age < 51 ';
            }else if($req->memis_age == 4){
                $having .= ' '.$key.'  age > 50 AND age < 61 ';
            }else if($req->memis_age == 5){
                 $having .= ' '.$key.'  age > 60 AND age < 71 ';
            }else if($req->memis_age == 6){
                $having .= ' '.$key.'  age > 70 ';
            }else{
                $having .= ' '.$key.'  age > 0 ';
            }

        }
        
        if($req->memis_educ > 0 && $req->memis_educ != '999'){

            $key = ($having != '') ? 'AND' : 'HAVING';
            $field .= ' , MIN(adp_highest) as minadp ';
            $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
            $having .= ' '.$key.' minadp = ' . $req->memis_educ . ' ';
        }
        
        
        if($req->memis_region > 0 && $req->memis_region != '999'){

            
            if($req->memis_province > 0 && $req->memis_province != '999'){
                
                if($req->memis_city > 0 && $req->memis_city != '999'){
                    $where .= ' AND emp_city LIKE '. $req->memis_city .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments)';
                }else{
                    $where .= ' AND emp_province LIKE '. $req->memis_province .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments)';
                }
            }
            else{
                $where .= ' AND emp_region LIKE '. $req->memis_region .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments)';    
            }

        }

        if($req->memis_country > 0 && $req->memis_country != '999'){

            $where .= ' AND emp_country LIKE '. $req->memis_country .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments)';    
          
        }

        for($i=0; $i<=$years;$i++){
            if($i < $years){
                $union = 'UNION';
            }else{
                $union = '';
            }

            $select .= 'SELECT count(mem_usr_id) as total, IFNULL(YEAR(mem_date_elected),'. $year .') AS label '. $field . 
            'FROM new_dbskms.tblmembers '. 
            'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
            'WHERE usr_grp_id LIKE 3 '. $where . $deceased .
            ' AND YEAR(mem_date_elected) = '. $year . ' ' . $having . ' ' . $group_by . ' ' . $union . ' ';

            $year++;
        }

        return DB::select($select);
    }

    // specific statuss for column with year
    public static function get_spec_status($id, $req){

        $select = '';
        $years = $req->memis_end_year - $req->memis_start_year;
        $year = $req->memis_start_year;
        $union = '';
        $deceased = '';
        $where = ' AND mem_status LIKE '. $id;
        $join = '';
        $case = '';
        $field = '';
        $having = '';
        $group_by = '';

        
        $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
        $join .= ' JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';

        if($req->memis_division > 0 && $req->memis_division != '999'){
            // $join .= ' JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';
            $where .= ' AND mem_div_id LIKE ' . $req->memis_division . ' ';
        }
        
        if($req->memis_category > 0 && $req->memis_category != '999'){
            // $join .= ' JOIN new_dbskms.tblmembership_status ON membership_status_id = mem_status ';
            $where .= ' AND mem_type LIKE ' . $req->memis_category . ' ';
            $deceased = '';
        }
        
        if($req->memis_sex > 0 && $req->memis_sex != '999'){
            $where .= ' AND pp_sex LIKE ' . $req->memis_sex . ' ';
        }

        if($req->memis_age > 0 && $req->memis_age != '999'){

            $field .= ' , (YEAR(CURDATE()) - YEAR(pp_date_of_birth)) as age ';

            $key = ($having != '') ? 'AND' : 'HAVING';

            if($req->memis_age == 1){
                $having .= ' '.$key.' age > 20 AND age < 31 ';
            }else if($req->memis_age == 2){
                $having .= ' '.$key.'  age > 30 AND age < 41 ';
            }else if($req->memis_age == 3){
                $having .= ' '.$key.'  age > 40 AND age < 51 ';
            }else if($req->memis_age == 4){
                $having .= ' '.$key.'  age > 50 AND age < 61 ';
            }else if($req->memis_age == 5){
                 $having .= ' '.$key.'  age > 60 AND age < 71 ';
            }else if($req->memis_age == 6){
                $having .= ' '.$key.'  age > 70 ';
            }else{
                $having .= ' '.$key.'  age > 0 ';
            }

        }
        
        if($req->memis_educ > 0 && $req->memis_educ != '999'){

            $key = ($having != '') ? 'AND' : 'HAVING';
            $field .= ' , MIN(adp_highest) as minadp ';
            $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
            $having .= ' '.$key.' minadp = ' . $req->memis_educ . ' ';
        }
        
        
        if($req->memis_region > 0 && $req->memis_region != '999'){

            
            if($req->memis_province > 0 && $req->memis_province != '999'){
                
                if($req->memis_city > 0 && $req->memis_city != '999'){
                    $where .= ' AND emp_city LIKE '. $req->memis_city .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments)';
                }else{
                    $where .= ' AND emp_province LIKE '. $req->memis_province .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments)';
                }
            }
            else{
                $where .= ' AND emp_region LIKE '. $req->memis_region .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments)';    
            }

        }

        if($req->memis_country > 0 && $req->memis_country != '999'){

            $where .= ' AND emp_country LIKE '. $req->memis_country .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments)';    
          
        }

        for($i=0; $i<=$years;$i++){
            if($i < $years){
                $union = 'UNION';
            }else{
                $union = '';
            }

            $select .= 'SELECT count(mem_usr_id) as total, IFNULL(YEAR(mem_date_elected),'. $year .') AS label '. $field . 
            'FROM new_dbskms.tblmembers '. 
            'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
            'WHERE usr_grp_id LIKE 3 '. $where . $deceased .
            ' AND YEAR(mem_date_elected) = '. $year . ' ' . $having . ' ' . $group_by . ' ' . $union . ' ';

            $year++;
        }

        return DB::select($select);
    }
    
    // specific category for column with year
    public static function get_spec_category($id, $req){

        $select = '';
        $years = $req->memis_end_year - $req->memis_start_year;
        $year = $req->memis_start_year;
        $union = '';
        $deceased = ' AND mem_status != 3 ';
        $where = ' AND mem_type LIKE '. $id;
        $join = '';
        $case = '';
        $field = '';
        $having = '';
        $group_by = '';

        
        $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
        $join .= ' JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';

        if($req->memis_division > 0 && $req->memis_division != '999'){
            // $join .= ' JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';
            $where .= ' AND mem_div_id LIKE ' . $req->memis_division . ' ';
        }
        
        if($req->memis_status > 0 && $req->memis_status != '999'){
            // $join .= ' JOIN new_dbskms.tblmembership_status ON membership_status_id = mem_status ';
            $where .= ' AND mem_status LIKE ' . $req->memis_status . ' ';
            $deceased = '';
        }
        
        if($req->memis_sex > 0 && $req->memis_sex != '999'){
            $where .= ' AND pp_sex LIKE ' . $req->memis_sex . ' ';
        }

        if($req->memis_age > 0 && $req->memis_age != '999'){

            $field .= ' , (YEAR(CURDATE()) - YEAR(pp_date_of_birth)) as age ';

            $key = ($having != '') ? 'AND' : 'HAVING';

            if($req->memis_age == 1){
                $having .= ' '.$key.' age > 20 AND age < 31 ';
            }else if($req->memis_age == 2){
                $having .= ' '.$key.'  age > 30 AND age < 41 ';
            }else if($req->memis_age == 3){
                $having .= ' '.$key.'  age > 40 AND age < 51 ';
            }else if($req->memis_age == 4){
                $having .= ' '.$key.'  age > 50 AND age < 61 ';
            }else if($req->memis_age == 5){
                 $having .= ' '.$key.'  age > 60 AND age < 71 ';
            }else if($req->memis_age == 6){
                $having .= ' '.$key.'  age > 70 ';
            }else{
                $having .= ' '.$key.'  age > 0 ';
            }

        }
        
        if($req->memis_educ > 0 && $req->memis_educ != '999'){

            $key = ($having != '') ? 'AND' : 'HAVING';
            $field .= ' , MIN(adp_highest) as minadp ';
            $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
            $having .= ' '.$key.' minadp = ' . $req->memis_educ . ' ';
        }   
        
        if($req->memis_region > 0 && $req->memis_region != '999'){

            
            if($req->memis_province > 0 && $req->memis_province != '999'){
                
                if($req->memis_city > 0 && $req->memis_city != '999'){
                    $where .= ' AND emp_city LIKE '. $req->memis_city .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments)';
                }else{
                    $where .= ' AND emp_province LIKE '. $req->memis_province .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments)';
                }
            }
            else{
                $where .= ' AND emp_region LIKE '. $req->memis_region .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments)';    
            }

        }

        if($req->memis_country > 0 && $req->memis_country != '999'){

            $where .= ' AND emp_country LIKE '. $req->memis_country .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments)';    
          
        }

        // SELECT count(mem_usr_id) as total, MONTH(mem_date_elected) as month  FROM `tblmembers` having month > 0

        if($req->memis_period > 0){

            if($req->memis_year > 0){
                $where .= ' AND YEAR(mem_date_elected) = ' . $req->memis_year;
            }else{
                $where .= ' AND YEAR(mem_date_elected) = ' . date("Y");
            }


            if($req->memis_period == 1){ // monthly

                for($i=1; $i<=12;$i++){
                    if($i < 12){
                        $union = 'UNION';
                    }else{
                        $union = '';
                    }
        
                    $select .= 'SELECT count(mem_usr_id) as total, IFNULL(MONTH(mem_date_elected),'.$i.') AS label '.  
                    'FROM new_dbskms.tblmembers '. 
                    'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
                    'WHERE usr_grp_id LIKE 3 AND MONTH(mem_date_elected) = '. $i . ' ' . $where . $deceased . ' ' . $union . ' ';
                }  
            }else if($req->memis_period == 2){ // quarterly

                for($i=1; $i<=4;$i++){
                    if($i < 4){
                        $union = 'UNION';
                    }else{
                        $union = '';
                    }
        
                    $select .= 'SELECT count(mem_usr_id) as total, IFNULL(QUARTER(mem_date_elected),'.$i.') AS label '.  
                    'FROM new_dbskms.tblmembers '. 
                    'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
                    'WHERE usr_grp_id LIKE 3 AND QUARTER(mem_date_elected) = '. $i . ' ' . $where . $deceased . ' ' . $union . ' ';
                }  
            }else if($req->memis_period == 3){ // semestral 

                for($i=1; $i<=2;$i++){
                    if($i < 2){
                        $union = 'UNION';
                    }else{
                        $union = '';
                    }
                    
                    $select .= 'SELECT count(mem_usr_id) as total, IFNULL(IF(MONTH(mem_date_elected) < 7, 1, 2),'.$i.') AS label '.  
                    'FROM new_dbskms.tblmembers '. 
                    'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
                    'WHERE usr_grp_id LIKE 3 AND IF(MONTH(mem_date_elected) < 7, 1, 2) = '. $i . ' ' . $where . $deceased . ' ' . $union . ' ';
                } 
            }
        }else{

            for($i=0; $i<=$years;$i++){
                if($i < $years){
                    $union = 'UNION';
                }else{
                    $union = '';
                }
    
                $select .= 'SELECT count(mem_usr_id) as total, IFNULL(YEAR(mem_date_elected),'. $year .') AS label '. $field . 
                'FROM new_dbskms.tblmembers '. 
                'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
                'WHERE usr_grp_id LIKE 3 '. $where . $deceased .
                'AND YEAR(mem_date_elected) = '. $year . ' ' . $having . ' ' . $group_by . ' ' . $union . ' ';
    
                $year++;
            }
        }


        return DB::select($select);
        // return $select;

    }

    // specific division for column with year
    public static function get_spec_division($id, $req){

        $select = '';
        $years = $req->memis_end_year - $req->memis_start_year;
        $year = $req->memis_start_year;
        $union = '';
        $deceased = ' AND mem_status != 3 ';
        $where = ' AND mem_div_id LIKE '. $id;
        $join = '';
        $case = '';
        $field = '';
        $having = '';
        $group_by = ' ';

        
        $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';

        if($req->memis_category > 0 && $req->memis_category != '999'){
            // $join .= ' JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';
            $where .= ' AND mem_type LIKE ' . $req->memis_category . ' ';
        }
        
        if($req->memis_status > 0 && $req->memis_status != '999'){
            // $join .= ' JOIN new_dbskms.tblmembership_status ON membership_status_id = mem_status ';
            $where .= ' AND mem_status LIKE ' . $req->memis_status . ' ';
            $deceased = '';
        }
        
        if($req->memis_sex > 0 && $req->memis_sex != '999'){
            $where .= ' AND pp_sex LIKE ' . $req->memis_sex . ' ';
        }

        if($req->memis_age > 0 && $req->memis_age != '999'){

            $field .= ' , (YEAR(CURDATE()) - YEAR(pp_date_of_birth)) as age ';

            $key = ($having != '') ? 'AND' : 'HAVING';

            if($req->memis_age == 1){
                $having .= ' '.$key.' age > 20 AND age < 31 ';
            }else if($req->memis_age == 2){
                $having .= ' '.$key.'  age > 30 AND age < 41 ';
            }else if($req->memis_age == 3){
                $having .= ' '.$key.'  age > 40 AND age < 51 ';
            }else if($req->memis_age == 4){
                $having .= ' '.$key.'  age > 50 AND age < 61 ';
            }else if($req->memis_age == 5){
                 $having .= ' '.$key.'  age > 60 AND age < 71 ';
            }else if($req->memis_age == 6){
                $having .= ' '.$key.'  age > 70 ';
            }else{
                $having .= ' '.$key.'  age > 0 ';
            }

        }
        
        if($req->memis_educ > 0 && $req->memis_educ != '999'){

            $key = ($having != '') ? 'AND' : 'HAVING';
            $field .= ' , MIN(adp_highest) as minadp ';
            $join .= ' JOIN new_dbskms.tblacademic_degree_profiles ON adp_usr_id = usr_id ';
            $having .= ' '.$key.' minadp = ' . $req->memis_educ . ' ';
        }
        
        
        if($req->memis_region > 0 && $req->memis_region != '999'){

            
            $join .= ' LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';
            if($req->memis_province > 0 && $req->memis_province != '999'){
                
                if($req->memis_city > 0 && $req->memis_city != '999'){
                    $where .= ' AND emp_city LIKE '. $req->memis_city .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments)';
                }else{
                    $where .= ' AND emp_province LIKE '. $req->memis_province .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments)';
                }
            }
            else{
                $where .= ' AND emp_region LIKE '. $req->memis_region .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments)';    
            }

        }

        if($req->memis_country > 0 && $req->memis_country != '999'){
            
            $join .= ' LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';
            $where .= ' AND emp_country LIKE '. $req->memis_country .' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments)';    
          
        }

        if($req->memis_period > 0){

            if($req->memis_year > 0){
                $where .= ' AND YEAR(mem_date_elected) = ' . $req->memis_year;
            }else{
                $where .= ' AND YEAR(mem_date_elected) = ' . date("Y");
            }


            if($req->memis_period == 1){ // monthly

                for($i=1; $i<=12;$i++){
                    if($i < 12){
                        $union = 'UNION';
                    }else{
                        $union = '';
                    }
        
                    $select .= 'SELECT count(mem_usr_id) as total, IFNULL(MONTH(mem_date_elected),'.$i.') AS label '.  
                    'FROM new_dbskms.tblmembers '. 
                    'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
                    'WHERE usr_grp_id LIKE 3 AND MONTH(mem_date_elected) = '. $i . ' ' . $where . $deceased . ' ' . $union . ' ';
                }  
            }else if($req->memis_period == 2){ // quarterly

                for($i=1; $i<=4;$i++){
                    if($i < 4){
                        $union = 'UNION';
                    }else{
                        $union = '';
                    }
        
                    $select .= 'SELECT count(mem_usr_id) as total, IFNULL(QUARTER(mem_date_elected),'.$i.') AS label '.  
                    'FROM new_dbskms.tblmembers '. 
                    'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
                    'WHERE usr_grp_id LIKE 3 AND QUARTER(mem_date_elected) = '. $i . ' ' . $where . $deceased . ' ' . $union . ' ';
                }  
            }else if($req->memis_period == 3){ // semestral 

                for($i=1; $i<=2;$i++){
                    if($i < 2){
                        $union = 'UNION';
                    }else{
                        $union = '';
                    }
                    
                    $select .= 'SELECT count(mem_usr_id) as total, IFNULL(IF(MONTH(mem_date_elected) < 7, 1, 2),'.$i.') AS label '.  
                    'FROM new_dbskms.tblmembers '. 
                    'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
                    'WHERE usr_grp_id LIKE 3 AND IF(MONTH(mem_date_elected) < 7, 1, 2) = '. $i . ' ' . $where . $deceased . ' ' . $union . ' ';
                } 
            }
        }else{

            for($i=0; $i<=$years;$i++){
                if($i < $years){
                    $union = 'UNION';
                }else{
                    $union = '';
                }
    
                $select .= 'SELECT count(mem_usr_id) as total, IFNULL(YEAR(mem_date_elected),'. $year .') AS label '. $field . 
                'FROM new_dbskms.tblmembers '. 
                'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
                'WHERE usr_grp_id LIKE 3 '. $where . $deceased .
                'AND YEAR(mem_date_elected) = '. $year . ' ' . $having . ' ' . $group_by . ' ' . $union . ' ';
    
                $year++;
            }
        }
        

        return DB::select($select);
               
    }

    static function get_csf($id){
        
        if($id == 1){ // stacked bar category

            $result_array = array();

            $query = DB::connection('dbskms')->table('tblservice_feedback_ratings')
            ->select('svc_fdbk_rating_id','svc_fdbk_rating')
            ->where('svc_fdbk_rating', '!=', 'Yes')
            ->where('svc_fdbk_rating', '!=', 'No')
            ->orderBy('svc_fdbk_rating_id','asc')
            ->get();
            
            foreach($query as $row){  
                $result_array[] = array($row->svc_fdbk_rating_id => array($row->svc_fdbk_rating => Member::get_all_csf($row->svc_fdbk_rating_id)));
            }

            
            return $result_array;

        }else if($id == 2){ // pie sex 
            
            $where = ''; $group_by = ''; $join = ''; $sub_q = ''; $select = '';

            $where .= ' AND pp_sex > 0 ';
            $where .= ' AND svc_fdbk_q_code LIKE "CSF-V2022" ';
            $where .= ' AND svc_id LIKE 3 ';
            $group_by .= ' GROUP BY pp_usr_id ';
            $join .= ' JOIN new_dbskms.tblsex on s_id = pp_sex ';
                            
            $sub_q .= 'SELECT s_id, count(pp_usr_id) as total, sex AS label '. 
            'FROM new_dbskms.tblpersonal_profiles '. 
            'JOIN new_dbskms.tblservice_feedbacks ON svc_fdbk_usr_id = pp_usr_id '.
            'JOIN new_dbskms.tblservice_feedback_questions ON new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id = new_dbskms.tblservice_feedbacks.svc_fdbk_q_id '. $join . 
            ' '. $where . $group_by;

            $select .= 'SELECT count(total) as total, label FROM '. 
            '( ' . $sub_q . ') as tmp GROUP BY s_id ORDER BY s_id desc';

            return DB::select($select);

        }else if($id == 3){ // bar region 
            
            $where = ''; $group_by = ''; $join = ''; $sub_q = ''; $select = '';

            $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = pp_usr_id) '; 
            // $where .= ' AND emp_period_to = "Present" '; 
            $where .= ' AND svc_fdbk_q_code LIKE "CSF-V2022" ';
            $where .= ' AND svc_id LIKE 3 ';
            $group_by .= ' GROUP BY pp_usr_id ';
 
            $join .= 'LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = pp_usr_id ';
            $join .= 'JOIN new_dbskms.tblregions on region_id = emp_region ';

            $sub_q .= 'SELECT count(pp_usr_id) as total, region_name as label, region_id '.
            'FROM new_dbskms.tblpersonal_profiles '. 
            'JOIN new_dbskms.tblservice_feedbacks ON svc_fdbk_usr_id = pp_usr_id '.
            'JOIN new_dbskms.tblservice_feedback_questions ON new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id = new_dbskms.tblservice_feedbacks.svc_fdbk_q_id '. $join . 
            ' '. $where . $group_by;

            $select .= 'SELECT count(total) as total, label FROM '. 
            '( ' . $sub_q . ') as tmp GROUP BY region_id';

            return DB::select($select);

        }else if($id == 4){ // bar age 
            
            $where = ''; $group_by = ''; $join = ''; $sub_q = ''; $select = '';

            $where .= ' AND svc_fdbk_q_code LIKE "CSF-V2022" ';
            $where .= ' AND svc_id LIKE 3 ';
            $group_by .= ' GROUP BY pp_usr_id ';

            $sub_q .= 'SELECT count(pp_usr_id) AS total, '.
            'CASE  '.
            'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 20 AND 31 then "1" '.
            'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 30 AND 41 then "2" '.
            'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 40 AND 51 then "3" '.
            'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 50 AND 61 then "4" '.
            'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 60 AND 71 then "5" '.
            'ELSE "6" END AS "range" '. 
            'FROM new_dbskms.tblpersonal_profiles '.
            'JOIN new_dbskms.tblservice_feedbacks ON svc_fdbk_usr_id = pp_usr_id '.
            'JOIN new_dbskms.tblservice_feedback_questions ON new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id = new_dbskms.tblservice_feedbacks.svc_fdbk_q_id '. $join . 
            'WHERE pp_date_of_birth LIKE \'%-%\' '. 
            'AND pp_date_of_birth > 0 '. $where . $group_by;

            $select .= 'SELECT count(total) AS total, '. 
            '(SELECT age_range FROM new_dbskms.tblage_ranges WHERE age_id = tmp.range) AS label FROM '. 
            '( ' . $sub_q . ') AS tmp GROUP BY tmp.range';

            return DB::select($select);

        }else{ // bar affiliation 
            
            $where = ''; $group_by = ''; $join = ''; $sub_q = ''; $select = '';

            // $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = pp_usr_id) '; 
            // $where .= ' AND emp_period_to = "Present" '; 
            $where .= ' AND svc_fdbk_q_code LIKE "CSF-V2022" ';
            $where .= ' AND svc_id LIKE 3 ';
            $where .= ' AND svc_fdbk_q_order LIKE 1 ';
            $group_by .= ' GROUP BY pp_usr_id ';
 
            $join .= 'JOIN new_dbskms.tblaffiliation_type ON aff_type_id = svc_fdbk_q_answer ';
            // $join .= 'JOIN new_dbskms.tblregions on region_id = emp_region ';

            $sub_q .= 'SELECT count(pp_usr_id) as total, aff_type as label, aff_type_id '.
            'FROM new_dbskms.tblpersonal_profiles '. 
            'JOIN new_dbskms.tblservice_feedbacks ON svc_fdbk_usr_id = pp_usr_id '.
            'JOIN new_dbskms.tblservice_feedback_questions ON new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id = new_dbskms.tblservice_feedbacks.svc_fdbk_q_id '. $join . 
            ' '. $where . $group_by;

            $select .= 'SELECT count(total) as total, label, aff_type_id FROM '. 
            '( ' . $sub_q . ') as tmp GROUP BY aff_type_id';

            return DB::select($select);

        } 

        
        // 'INNER JOIN new_dbskms.tblusers ON usr_id = pp_usr_id '.
        // 'LEFT JOIN new_dbskms.tblmembers ON mem_usr_id = usr_id '. 

        
    }

    // all csf for graph
    static function get_all_csf($id){

        $query = DB::select('SELECT svc_fdbk_q_id, svc_fdbk_q, '. 
        '(SELECT COUNT(*) FROM new_dbskms.tblservice_feedbacks '.
        'WHERE new_dbskms.tblservice_feedbacks.svc_fdbk_q_id '. 
        'LIKE new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id '. 
        'AND svc_fdbk_q_answer LIKE '. $id . ' AND svc_id LIKE 3'. 
        ') AS total '. 
        'FROM new_dbskms.tblservice_feedback_questions '. 
        'WHERE svc_fdbk_q_choices LIKE "1,2,3,4,5" AND svc_fdbk_q_code LIKE "CSF-V2022" ');

        return $query;

    }

    static function get_csf_list(){
        
        return DB::connection('dbskms')->table('tblservice_feedbacks')
        ->join('tblpersonal_profiles','pp_usr_id','=','svc_fdbk_usr_id')
        ->join('tblservice_feedback_questions','tblservice_feedback_questions.svc_fdbk_q_id','=','tblservice_feedbacks.svc_fdbk_q_id')
        ->join('tblemployments','emp_usr_id','=','pp_usr_id')
        ->join('tblregions','region_id','=','emp_region', 'left')
        ->join('tblsex','s_id','=','pp_sex','left')
        ->join('tblmembership_profiles','mpr_usr_id','=','pp_usr_id')
        ->join('tbldivisions','div_id','=','mpr_div_id','left')
        ->select('div_number', 'pp_usr_id', 'pp_last_name', 'pp_first_name', 'pp_email', 'sex', 'region_name', 'emp_pos', 'emp_ins', 'tblservice_feedbacks.date_created as date_created', 
        DB::connection('dbskms')->raw('YEAR(CURDATE())-YEAR(pp_date_of_birth) AS age'))
        ->where('svc_id','3')
        ->whereRaw('emp_id in (SELECT MAX(emp_id) AS emp_id FROM new_dbskms.tblemployments GROUP by emp_usr_id ORDER by emp_id DESC)')
        // ->where('svc_fdbk_q_choices', '1,2,3,4,5')
        ->where('svc_fdbk_q_code', 'CSF-V2022')
        ->orderBy('pp_last_name')
        ->groupBy('svc_fdbk_usr_id')
        ->get();
    }

    static function get_csf_answers($user_id){
        return DB::connection('dbskms')->table('tblservice_feedbacks')
        ->join('new_dbskms.tblservice_feedback_questions','new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id','=','tblservice_feedbacks.svc_fdbk_q_id')
        ->where('new_dbskms.tblservice_feedback_questions.svc_fdbk_q_code', 'CSF-V2022')
        ->where('svc_fdbk_usr_id', $user_id)
        ->where('svc_id','3')
        // ->groupBy('tbl_csf_respondents.fb_id')
        ->get();
    }

    // get csf desc
    static function get_csf_desc($id, $user){
        return DB::connection('dbskms')->table('tblservice_feedbacks')
        ->join('tblservice_feedback_questions','tblservice_feedback_questions.svc_fdbk_q_id','=','tblservice_feedbacks.svc_fdbk_q_id')
        ->join('tblpersonal_profiles','pp_usr_id','=','tblservice_feedbacks.svc_fdbk_usr_id')
        ->select('tblservice_feedbacks.svc_fdbk_q_answer as rate')
        ->where('svc_fdbk_q_order', $id)
        ->where('svc_fdbk_q_code', 'CSF-V2022')
        ->where('svc_fdbk_usr_id', $user)
        ->get();
    }

    // get qcsf questions
    static function get_questions(){
        
        return DB::connection('dbskms')->table('tblservice_feedback_questions')
        ->select('svc_fdbk_q_desc')
        ->where('svc_fdbk_q_choices', '1,2,3,4,5')
        ->where('svc_fdbk_q_code', 'CSF-V2022')
        ->get();

        // return DB::connection('dbskms')->table('tblservice_feedback_questions')
        // ->select('svc_fdbk_q')
        // ->where('svc_fdbk_q_choices', '1,2,3,4,5')
        // ->where('svc_fdbk_q_code', 'CSF-V2022')
        // ->get();
    } 

    // get discrepanices (no employment, no region, no status, no counrtry)
    public static function get_no_employment(){

        // SELECT emp_id, emp_usr_id, emp_ins, emp_country, region_name FROM tblemployments
        // inner join tblusers on usr_id = emp_usr_id
        // left join tblpersonal_profiles on pp_usr_id = emp_usr_id
        // left join tblmembers on mem_usr_id = emp_usr_id
        // left join tblregions on region_id = emp_region
        // WHERE usr_grp_id = 3 and mem_status != 3 and mem_status is not null and emp_country = 175 and (emp_region = 0 OR emp_region is null) and emp_id IN 
        // (SELECT max(emp_id) as emp_id
        // FROM tblemployments 
        // GROUP by emp_usr_id 
        // ORDER by emp_id DESC);

        return DB::select('SELECT * FROM new_dbskms.tblpersonal_profiles '.
        'INNER JOIN new_dbskms.tblusers ON usr_id = pp_usr_id  '.
        'LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = pp_usr_id  '.
        'LEFT JOIN new_dbskms.tblmembers ON mem_usr_id = pp_usr_id  '.
        'LEFT JOIN new_dbskms.tblsex ON s_id = pp_sex '.
        'LEFT JOIN new_dbskms.tbldivisions ON div_id = mem_div_id '.
        'LEFT JOIN new_dbskms.tbltitles ON title_id = pp_title '.
        'WHERE emp_usr_id IS NULL AND usr_grp_id = 3 AND mem_status != 3');
    
    }

    public static function get_no_region(){

        $query = DB::select(' SELECT * FROM new_dbskms.tblusers '.
        'INNER JOIN new_dbskms.tblmembers ON mem_usr_id = usr_id '. 
        ' JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id '. 
        ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id '.
        ' JOIN new_dbskms.tblsex ON s_id = pp_sex '.
        ' JOIN new_dbskms.tbldivisions ON div_id = mem_div_id '.
        ' JOIN new_dbskms.tbltitles ON title_id = pp_title '.
        ' WHERE emp_country = 175 '. 
        'AND emp_id = (SELECT MAX(emp_id) FROM new_dbskms.tblemployments where emp_usr_id = usr_id ) '.
        'AND (emp_region = 0 OR emp_region IS NULL OR emp_region = "") '. 
        'AND usr_grp_id LIKE 3 '. 
        'AND mem_status != 3 '.
        'GROUP BY emp_usr_id');

        return $query;
    }

    public static function get_no_status(){

        // SELECT emp_id, emp_usr_id, emp_ins, emp_country, region_name FROM tblemployments
        // inner join tblusers on usr_id = emp_usr_id
        // left join tblpersonal_profiles on pp_usr_id = emp_usr_id
        // left join tblmembers on mem_usr_id = emp_usr_id
        // left join tblregions on region_id = emp_region
        // WHERE usr_grp_id = 3 and mem_status is null and emp_id IN 
        // (SELECT max(emp_id) as emp_id
        // FROM tblemployments 
        // GROUP by emp_usr_id 
        // ORDER by emp_id DESC);

        return DB::select('SELECT * FROM new_dbskms.tblemployments '.
        'INNER JOIN new_dbskms.tblusers ON usr_id = emp_usr_id '. 
        'LEFT JOIN new_dbskms.tblpersonal_profiles on pp_usr_id = emp_usr_id '.
        'LEFT JOIN new_dbskms.tblmembers ON mem_usr_id = pp_usr_id '. 
        'LEFT JOIN new_dbskms.tblsex ON s_id = pp_sex '.
        'LEFT JOIN new_dbskms.tbldivisions ON div_id = mem_div_id '.
        'LEFT JOIN new_dbskms.tbltitles ON title_id = pp_title '.
        'WHERE usr_grp_id = 3 and mem_status IS NULL AND mem_status != 3');
    }

    public static function get_no_country(){

        return DB::select('SELECT * FROM new_dbskms.tblpersonal_profiles '.
        'INNER JOIN new_dbskms.tblusers ON usr_id = pp_usr_id '.
        'LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = pp_usr_id '.
        'LEFT JOIN new_dbskms.tblmembers ON mem_usr_id = emp_usr_id '.
        'LEFT JOIN new_dbskms.tblregions ON region_id = emp_region '.
        'LEFT JOIN new_dbskms.tblsex ON s_id = pp_sex '.
        'LEFT JOIN new_dbskms.tbldivisions ON div_id = mem_div_id '.
        'LEFT JOIN new_dbskms.tbltitles ON title_id = pp_title '.
        'WHERE usr_grp_id = 3 AND mem_status != 3 AND mem_status IS NOT NULL AND (emp_country = 0 OR emp_country IS NULL) AND emp_id IN  '.
        '(SELECT MAX(emp_id) AS emp_id '.
        'FROM new_dbskms.tblemployments  '.
        'GROUP by emp_usr_id  '.
        'ORDER by emp_id DESC)');
    }

    public static function get_abroad(){

        // SELECT emp_id, emp_usr_id, region_name FROM tblpersonal_profiles
        //     inner join tblusers on usr_id = pp_usr_id
        //     left join tblemployments on emp_usr_id = pp_usr_id
        //     left join tblmembers on mem_usr_id = emp_usr_id
        //     left join tblregions on region_id = emp_region
        //     WHERE usr_grp_id = 3 and mem_status != 3 and mem_status is not null and emp_country != 175 and emp_country > 0 and emp_country is not null and emp_id IN 
        //     (SELECT max(emp_id) as emp_id
        //     FROM tblemployments 
        //     GROUP by emp_usr_id 
        //     ORDER by emp_id DESC);
        //tama na

        return DB::select(' SELECT * FROM new_dbskms.tblemployments '.
        'INNER JOIN new_dbskms.tblusers ON usr_id = emp_usr_id '. 
        'LEFT JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = emp_usr_id '. 
        'LEFT JOIN new_dbskms.tblmembers ON mem_usr_id = emp_usr_id '. 
        'LEFT JOIN new_dbskms.tblsex ON s_id = pp_sex '.
        'LEFT JOIN new_dbskms.tbldivisions ON div_id = mem_div_id '.
        'LEFT JOIN new_dbskms.tbltitles ON title_id = pp_title '.
        'LEFT JOIN new_dbskms.tblcountries ON country_id = emp_country '.
        'WHERE usr_grp_id = 3 AND mem_status != 3 AND mem_status IS NOT NULL AND emp_country != 175 AND emp_country > 0 AND emp_country is not null '. 
        'AND emp_id IN (SELECT MAX(emp_id) AS emp_id '.
        'FROM new_dbskms.tblemployments '. 
        'GROUP by emp_usr_id '. 
        'ORDER by emp_id DESC)');
    }

    public static function get_line_division($id, $req){

        $select = '';
        $years = $req->memis_end_year - $req->memis_start_year;
        $year = $req->memis_start_year;
        $union = '';
        $deceased = ' AND mem_status != 3 ';
        $where = ' AND mem_div_id LIKE '. $id;
        $join = '';
        $case = '';
        $field = '';
        $having = '';
        $group_by = ' ';

        
        $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';

        if($req->memis_category > 0 && $req->memis_category != '999'){
            // $join .= ' JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';
            if($req->memis_category == 9){
                $where .= ' AND (mem_type LIKE 1 OR mem_type LIKE 2) ';
            }else{
                $where .= ' AND mem_type LIKE ' . $req->memis_category . ' ';
            }
        }
        
        if($req->memis_sex > 0 && $req->memis_sex != '999'){
            $where .= ' AND pp_sex LIKE ' . $req->memis_sex . ' ';
        }

        if($req->memis_period > 0){

            if($req->memis_year > 0){
                $where .= ' AND YEAR(mem_date_elected) = ' . $req->memis_year;
            }else{
                $where .= ' AND YEAR(mem_date_elected) = ' . date("Y");
            }


            if($req->memis_period == 1){ // monthly

                for($i=1; $i<=12;$i++){
                    if($i < 12){
                        $union = 'UNION';
                    }else{
                        $union = '';
                    }
        
                    $select .= 'SELECT count(mem_usr_id) as total, IFNULL(MONTH(mem_date_elected),'.$i.') AS label '.  
                    'FROM new_dbskms.tblmembers '. 
                    'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
                    'WHERE usr_grp_id LIKE 3 AND MONTH(mem_date_elected) = '. $i . ' ' . $where . $deceased . ' ' . $union . ' ';
                }  
            }else if($req->memis_period == 2){ // quarterly

                for($i=1; $i<=4;$i++){
                    if($i < 4){
                        $union = 'UNION';
                    }else{
                        $union = '';
                    }
        
                    $select .= 'SELECT count(mem_usr_id) as total, IFNULL(QUARTER(mem_date_elected),'.$i.') AS label '.  
                    'FROM new_dbskms.tblmembers '. 
                    'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
                    'WHERE usr_grp_id LIKE 3 AND QUARTER(mem_date_elected) = '. $i . ' ' . $where . $deceased . ' ' . $union . ' ';
                }  
            }else if($req->memis_period == 3){ // semestral 

                for($i=1; $i<=2;$i++){
                    if($i < 2){
                        $union = 'UNION';
                    }else{
                        $union = '';
                    }
                    
                    $select .= 'SELECT count(mem_usr_id) as total, IFNULL(IF(MONTH(mem_date_elected) < 7, 1, 2),'.$i.') AS label '.  
                    'FROM new_dbskms.tblmembers '. 
                    'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
                    'WHERE usr_grp_id LIKE 3 AND IF(MONTH(mem_date_elected) < 7, 1, 2) = '. $i . ' ' . $where . $deceased . ' ' . $union . ' ';
                } 
            }
        }else{

            for($i=0; $i<=$years;$i++){
                if($i < $years){
                    $union = 'UNION';
                }else{
                    $union = '';
                }
    
                $select .= 'SELECT count(mem_usr_id) as total, IFNULL(YEAR(mem_date_elected),'. $year .') AS label '. $field . 
                'FROM new_dbskms.tblmembers '. 
                'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
                'WHERE usr_grp_id LIKE 3 '. $where . $deceased .
                'AND YEAR(mem_date_elected) = '. $year . ' ' . $having . ' ' . $group_by . ' ' . $union . ' ';
    
                $year++;
            }
        }
        
        return DB::select($select);
               
    }

    public static function get_line_region($id, $req){

        $select = '';
        $years = $req->memis_end_year - $req->memis_start_year;
        $year = $req->memis_start_year;
        $union = '';
        $deceased = ' AND mem_status != 3 ';
        $where = ' AND emp_region LIKE '. $id;
        $join = '';
        $case = '';
        $field = '';
        $having = '';
        $group_by = '';

        
        $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = usr_id ';
        $join .= ' JOIN new_dbskms.tblemployments ON emp_usr_id = usr_id ';
        $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments)';   


        if($req->memis_category > 0 && $req->memis_category != '999'){
            // $join .= ' JOIN new_dbskms.tblmembership_types ON membership_type_id = mem_type ';
            if($req->memis_category == 9){
                $where .= ' AND (mem_type LIKE 1 OR mem_type LIKE 2) ';
            }else{
                $where .= ' AND mem_type LIKE ' . $req->memis_category . ' ';
            }
        }
        
        if($req->memis_sex > 0 && $req->memis_sex != '999'){
            $where .= ' AND pp_sex LIKE ' . $req->memis_sex . ' ';
        }

        if($req->memis_period > 0){

            if($req->memis_year > 0){
                $where .= ' AND YEAR(mem_date_elected) = ' . $req->memis_year;
            }else{
                $where .= ' AND YEAR(mem_date_elected) = ' . date("Y");
            }


            if($req->memis_period == 1){ // monthly

                for($i=1; $i<=12;$i++){
                    if($i < 12){
                        $union = 'UNION';
                    }else{
                        $union = '';
                    }
        
                    $select .= 'SELECT count(mem_usr_id) as total, IFNULL(MONTH(mem_date_elected),'.$i.') AS label '.  
                    'FROM new_dbskms.tblmembers '. 
                    'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
                    'WHERE usr_grp_id LIKE 3 AND MONTH(mem_date_elected) = '. $i . ' ' . $where . $deceased . ' ' . $union . ' ';
                }  
            }else if($req->memis_period == 2){ // quarterly

                for($i=1; $i<=4;$i++){
                    if($i < 4){
                        $union = 'UNION';
                    }else{
                        $union = '';
                    }
        
                    $select .= 'SELECT count(mem_usr_id) as total, IFNULL(QUARTER(mem_date_elected),'.$i.') AS label '.  
                    'FROM new_dbskms.tblmembers '. 
                    'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
                    'WHERE usr_grp_id LIKE 3 AND QUARTER(mem_date_elected) = '. $i . ' ' . $where . $deceased . ' ' . $union . ' ';
                }  
            }else if($req->memis_period == 3){ // semestral 

                for($i=1; $i<=2;$i++){
                    if($i < 2){
                        $union = 'UNION';
                    }else{
                        $union = '';
                    }
                    
                    $select .= 'SELECT count(mem_usr_id) as total, IFNULL(IF(MONTH(mem_date_elected) < 7, 1, 2),'.$i.') AS label '.  
                    'FROM new_dbskms.tblmembers '. 
                    'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
                    'WHERE usr_grp_id LIKE 3 AND IF(MONTH(mem_date_elected) < 7, 1, 2) = '. $i . ' ' . $where . $deceased . ' ' . $union . ' ';
                } 
            }
        }else{

            for($i=0; $i<=$years;$i++){
                if($i < $years){
                    $union = 'UNION';
                }else{
                    $union = '';
                }

                $select .= 'SELECT count(mem_usr_id) as total, IFNULL(YEAR(mem_date_elected),'. $year .') AS label '. $field . 
                'FROM new_dbskms.tblmembers '. 
                'INNER JOIN new_dbskms.tblusers ON usr_id = mem_usr_id '. $join .
                'WHERE usr_grp_id LIKE 3 '. $where . $deceased .
                ' AND YEAR(mem_date_elected) = '. $year . ' ' . $having . ' ' . $group_by . ' ' . $union . ' ';

                $year++;
            }
        }

        return DB::select($select);  
    }

    

}
