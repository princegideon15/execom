<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Library extends Model
{
    static function count_articles(){
        return DB::connection('dblms')->table('tblarticles')->count();
    }

    
    static function count_views(){
        return DB::connection('dblms')->table('tblhits')->count();
    }

    static function count_downloads(){
        return DB::connection('dblms')->table('tbldownloads')->count();
    }

    static function count_active_users(){
        return DB::connection('dblms')->table('tblusers')
        ->where('usr_status_id', 1)->count();
    }

    static function get_categories(){ 
        
        return DB::connection('dblms')->table('tblcategories')
        ->select('category','cat_id', DB::connection('dblms')->raw('(select count(*) from tblarticles where art_category like cat_id) as total'))
        ->groupBy('category','cat_id')
        ->get()
        ->toArray();
    }


    static function get_views(){
        
        return DB::connection('dblms')->table('tblcategories')
        ->select('category','cat_id', DB::connection('dblms')->raw('(select count(art_id) from tblarticles join tblhits on art_id = hit_art_id where art_category like cat_id) as total'))
        ->groupBy('category','cat_id')
        ->get()
        ->toArray();
    }

    static function get_downloads(){
        
        return DB::connection('dblms')->table('tblcategories')
        ->select('category','cat_id', 
        DB::connection('dblms')->raw('(select count(art_id) from tblarticles join tbldownloads on art_id = dl_art_id where art_category like cat_id) as total'))
        ->groupBy('category','cat_id')
        ->get()
        ->toArray();
    }

    static function get_category($data){
        return DB::connection('dblms')->table('tblarticles')
        ->select('art_title', 'art_author', 'art_keywords', 'art_full_text', 'art_id')
        ->where('art_category', $data)
        ->orderBy('art_title', 'desc')
        ->get();
    }

    static function get_cat_label($id){
        return DB::connection('dblms')->table('tblarticles')
        ->select(DB::connection('dblms')->raw('(select category from tblcategories where cat_id like art_category) as category'))
        ->where('art_id', $id)
        ->first()->category;
    }

    static function get_file($id){
        return DB::connection('dblms')->table('tblarticles')
        ->select('art_full_text')
        ->where('art_id', $id)
        ->first()->art_full_text;
    }

    // search

    static function search($keyword, $cat_id){
        return DB::connection('dblms')->table('tblarticles')
        ->select('*')
        // ->where('art_title', 'LIKE', '%' . $keyword . '%')  
        ->Where('art_keywords', 'LIKE', '%' . $keyword . '%')  
        ->where('art_category', $cat_id)
        ->orderBy('art_title', 'asc') 
        ->get();
    }

    static function get_tables(){
        return DB::select('SHOW TABLES');
    }

    

    static function get_csf_list(){
        
        return DB::connection('dblms')->table('tblservice_feedbacks')
        ->join('tblusers','p_id','=','svc_fdbk_usr_id')
        ->join('tblpersonal_info','tblpersonal_info.p_id','=','tblusers.p_id')
        ->join('new_dbskms.tblsex','s_id','=','p_sex')
        ->join('new_dbskms.tblservice_feedback_questions','new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id','=','tblservice_feedbacks.svc_fdbk_q_id')
        ->select('tblpersonal_info.*', 'tblservice_feedbacks.date_created as date_submitted', 'usr_email', 'sex')
        ->where('tblservice_feedback_questions.svc_fdbk_q_code', 'CSF-V2022')
        ->groupBy('svc_fdbk_usr_id')
        ->get();

    }

    static function get_csf_answers($user_id){
        return DB::connection('dblms')->table('tblservice_feedbacks')
        ->join('new_dbskms.tblservice_feedback_questions','new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id','=','tblservice_feedbacks.svc_fdbk_q_id')
        ->where('new_dbskms.tblservice_feedback_questions.svc_fdbk_q_code', 'CSF-V2022')
        ->where('svc_fdbk_usr_id', $user_id)
        // ->groupBy('tbl_csf_respondents.fb_id')
        ->get();
    }

    // get csf desc
    static function get_csf_desc($id, $user){
        return DB::connection('dblms')->table('tblservice_feedbacks')
        ->join('tblservice_feedback_questions','tblservice_feedback_questions.svc_fdbk_q_id','=','tblservice_feedbacks.svc_fdbk_q_id')
        ->join('tblusers','p_id','=','svc_fdbk_usr_id')
        ->join('tblpersonal_info','tblusers.p_id','=','tblpersonal_info.p_id')
        ->select('tblservice_feedbacks.svc_fdbk_q_answer as rate')
        ->where('svc_fdbk_q_order', $id)
        ->where('svc_fdbk_q_code', 'CSF-V2022')
        ->where('svc_fdbk_usr_id', $user)
        ->get();
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
                $result_array[] = array($row->svc_fdbk_rating_id => array($row->svc_fdbk_rating => Library::get_all_csf($row->svc_fdbk_rating_id)));
            }

            
            return $result_array;

        }else if($id == 2){ // pie sex 
            
            $where = ''; $group_by = ''; $join = ''; $sub_q = ''; $select = '';

            // $where .= ' AND pp_sex > 0 ';
            $where .= ' AND new_dbskms.tblservice_feedback_questions.svc_fdbk_q_code LIKE "CSF-V2022" ';
            // $where .= ' AND svc_id LIKE 3 ';
            $group_by .= ' GROUP BY tblusers.p_id ';
            $join .= ' JOIN new_dbskms.tblsex on s_id = p_sex ';
                            
            $sub_q .= 'SELECT s_id, count(tblpersonal_info.p_id) as total, sex AS label '. 
            'FROM dblms.tblpersonal_info '. 
            'JOIN dblms.tblusers ON tblusers.p_id = tblpersonal_info.p_id '.
            'JOIN dblms.tblservice_feedbacks ON svc_fdbk_usr_id = tblusers.p_id '.
            'JOIN new_dbskms.tblservice_feedback_questions ON new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id = dblms.tblservice_feedbacks.svc_fdbk_q_id '. $join . 
            ' '. $where . $group_by;

            $select .= 'SELECT count(total) as total, label FROM '. 
            '( ' . $sub_q . ') as tmp GROUP BY s_id ORDER BY s_id desc';

            return DB::select($select);

        }else if($id == 3){ // bar region 
            
            // $where = ''; $group_by = ''; $join = ''; $sub_q = ''; $select = '';

            // $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = tblpersonal_info.p_id) '; 
            // // $where .= ' AND emp_period_to = "Present" '; 
            // $where .= ' AND new_dbskms.tblservice_feedback_questions.svc_fdbk_q_code LIKE "CSF-V2022" ';
            // // $where .= ' AND svc_id LIKE 3 ';
            // $group_by .= ' GROUP BY tblpersonal_info.p_id ';
 
            // $join .= 'LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = tblpersonal_info.p_id ';
            // $join .= 'JOIN new_dbskms.tblregions on region_id = emp_region ';

            // $sub_q .= 'SELECT count(tblpersonal_info.p_id) as total, region_name as label, region_id '.
            // 'FROM dblms.tblpersonal_info '. 
            // 'JOIN dblms.tblusers ON tblusers.p_id = tblpersonal_info.p_id '.
            // 'JOIN dbej.tblservice_feedbacks ON svc_fdbk_usr_id = tblpersonal_info.p_id '.
            // 'JOIN new_dbskms.tblservice_feedback_questions ON new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id = dbej.tblservice_feedbacks.svc_fdbk_q_id '. $join . 
            // ' '. $where . $group_by;

            // $select .= 'SELECT count(total) as total, label FROM '. 
            // '( ' . $sub_q . ') as tmp GROUP BY region_id';

            // return DB::select($select);

        }else if($id == 4){ // bar age 
            
            // $where = ''; $group_by = ''; $join = ''; $sub_q = ''; $select = '';

            // $where .= ' AND dbej.tblservice_feedbacks.svc_fdbk_q_code LIKE "CSF-V2022" ';
            // // $where .= ' AND svc_id LIKE 3 ';
            // $group_by .= ' GROUP BY tblpersonal_info.p_id ';

            // $sub_q .= 'SELECT count(tblpersonal_info.p_id) AS total, '.
            // 'CASE  '.
            // 'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 20 AND 31 then "1" '.
            // 'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 30 AND 41 then "2" '.
            // 'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 40 AND 51 then "3" '.
            // 'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 50 AND 61 then "4" '.
            // 'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 60 AND 71 then "5" '.
            // 'ELSE "6" END AS "range" '. 
            // 'FROM dblms.tblpersonal_info '. 
            // 'JOIN dblms.tblusers ON tblusers.p_id = tblpersonal_info.p_id '.
            // 'JOIN dbej.tblservice_feedbacks ON svc_fdbk_usr_id = tblpersonal_info.p_id '.
            // 'JOIN new_dbskms.tblservice_feedback_questions ON new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id = dbej.tblservice_feedbacks.svc_fdbk_q_id '. $join . 
            // 'WHERE pp_date_of_birth LIKE \'%-%\' '. $where . $group_by;

            // $select .= 'SELECT count(total) AS total, '. 
            // '(SELECT age_range FROM new_dbskms.tblage_ranges WHERE age_id = tmp.range) AS label FROM '. 
            // '( ' . $sub_q . ') AS tmp GROUP BY tmp.range';

            // return DB::select($select);
        } else{ // bar affiliation 
             
            
            $where = ''; $group_by = ''; $join = ''; $sub_q = ''; $select = '';

            // $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = tbl_csf_respondents.fb_id) '; 
            // $where .= ' AND emp_period_to = "Present" '; 
            $where .= ' AND new_dbskms.tblservice_feedback_questions.svc_fdbk_q_code LIKE "CSF-V2022" ';
            $where .= ' AND svc_fdbk_q_order LIKE 1 ';
            // $where .= ' AND svc_id LIKE 3 ';
            $group_by .= ' GROUP BY p_id ';
 
            // $join .= 'LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = p_id ';
 
            $join .= ' JOIN new_dbskms.tblaffiliation_type ON aff_type_id = svc_fdbk_q_answer ';
            // $join .= ' JOIN new_dbskms.tblregions on region_id = region ';

            $sub_q .= 'SELECT count(p_id) as total, aff_type as label, aff_type_id '.
            'FROM dblms.tblpersonal_info '. 
            'JOIN dblms.tblservice_feedbacks ON tblservice_feedbacks.svc_fdbk_usr_id = p_id '.
            'JOIN new_dbskms.tblservice_feedback_questions ON new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id = dblms.tblservice_feedbacks.svc_fdbk_q_id '. $join . 
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
        '(SELECT COUNT(*) FROM dblms.tblservice_feedbacks '.
        'JOIN dblms.tblusers on usr_id = dblms.tblservice_feedbacks.svc_fdbk_usr_id '.
        'JOIN dblms.tblpersonal_info on tblpersonal_info.p_id = dblms.tblusers.p_id '.
        'WHERE dblms.tblservice_feedbacks.svc_fdbk_q_id '. 
        'LIKE new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id '. 
        'AND svc_fdbk_q_answer LIKE '. $id . 
        ') AS total '. 
        'FROM new_dbskms.tblservice_feedback_questions '. 
        'WHERE svc_fdbk_q_choices LIKE "1,2,3,4,5" AND svc_fdbk_q_code LIKE "CSF-V2022" ');

        return $query;

    }
}
