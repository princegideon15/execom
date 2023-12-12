<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Thds extends Model
{

    static function count_thd($value){
        return DB::connection('dbskms')->table('tblthds_applications')
        ->join('tblthds_process','thds_pro_apl_id','=','thds_apl_id', 'left')
        ->select('*')
        ->where('thds_apl_type', $value)
        ->where('thds_apl_submit', '1')
        ->where('tblthds_applications.date_created', 'LIKE', '%'. date("Y") .'%')
        ->groupBy('thds_pro_apl_id')
        ->get()
        ->count();
    }
    
    static function count_action_apps($value){
        return DB::connection('dbskms')->table('tblthds_applications')
        ->join('tblthds_process','thds_pro_apl_id','=','thds_apl_id')
        ->join('tblthds_action','thds_action_id','=','thds_pro_action')
        ->join('tblpersonal_profiles','pp_usr_id','=','thds_apl_usr_id')
        ->select('tblthds_applications.*', 'thds_pro_action', 'pp_last_name', 'pp_first_name', 'thds_action')
        ->where('thds_pro_action', $value)
        ->where('tblthds_process.date_created', 'LIKE', '%'. date("Y") .'%')
        ->groupBy('thds_pro_apl_id')
        ->get()
       ->count();
    }

    static function get_thds($value){
        return DB::connection('dbskms')->table('tblthds_applications')
        ->join('tblthds_process','thds_pro_apl_id','=','thds_apl_id', 'left')
        ->join('tblthds_action','thds_action_id','=','thds_pro_action')
        ->join('tblpersonal_profiles','pp_usr_id','=','thds_apl_usr_id')
        ->select('tblthds_applications.*', 'thds_pro_action', 'pp_last_name', 'pp_first_name', 'thds_action')
        ->where('thds_apl_type', $value)
        ->where('thds_apl_submit', '1')
        ->where('tblthds_applications.date_created', 'LIKE', '%'. date("Y") .'%')
        ->groupBy('thds_pro_apl_id')
        ->get()
        ->toArray();
    }

    static function get_action_thds($value){
        return DB::connection('dbskms')->table('tblthds_applications')
        ->join('tblthds_process','thds_pro_apl_id','=','thds_apl_id')
        ->join('tblthds_action','thds_action_id','=','thds_pro_action')
        ->join('tblpersonal_profiles','pp_usr_id','=','thds_apl_usr_id')
        ->select('tblthds_applications.*', 'thds_pro_action', 'pp_last_name', 'pp_first_name', 'thds_action')
        ->where('thds_pro_action', $value)
        ->where('tblthds_process.date_created', 'LIKE', '%'. date("Y") .'%')
        ->groupBy('thds_pro_apl_id')
        ->get()
        ->toArray();
    }      

    static function get_csf_list(){
        return DB::connection('dbskms')->table('tblservice_feedbacks')
        ->join('tblpersonal_profiles','pp_usr_id','=','tblservice_feedbacks.svc_fdbk_usr_id')
        ->join('tblservice_feedback_questions','tblservice_feedback_questions.svc_fdbk_q_id','=','tblservice_feedbacks.svc_fdbk_q_id')
        ->join('tblemployments','emp_usr_id','=','pp_usr_id')
        ->join('tblregions','region_id','=','emp_region', 'left')
        ->join('tblmembership_profiles','mpr_usr_id','=','pp_usr_id')
        ->join('tbldivisions','div_id','=','mpr_div_id', 'left')
        ->join('tblsex','s_id','=','pp_sex', 'left')
        ->select('div_number','tblservice_feedbacks.svc_fdbk_usr_id as user_id', 'pp_last_name', 'pp_first_name', 'pp_email', 'sex', 'region_name', 'emp_pos', 'emp_ins', 'tblservice_feedbacks.date_created as date_created', 
        DB::connection('dbskms')->raw('YEAR(CURDATE())-YEAR(pp_date_of_birth) AS age'))
        ->where('svc_id', '2')
        ->where('tblservice_feedback_questions.svc_fdbk_q_code', 'CSF-V2022')
        ->whereRaw('emp_id in (SELECT MAX(emp_id) AS emp_id FROM new_dbskms.tblemployments GROUP by emp_usr_id ORDER by emp_id DESC)')
        ->orderBy('pp_last_name')
        ->groupBy('svc_fdbk_usr_id')
        ->get();
    }

    static function get_csf_answers($user_id){
        return DB::connection('dbskms')->table('tblservice_feedbacks')
        ->join('new_dbskms.tblservice_feedback_questions','new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id','=','tblservice_feedbacks.svc_fdbk_q_id')
        ->where('new_dbskms.tblservice_feedback_questions.svc_fdbk_q_code', 'CSF-V2022')
        ->where('svc_fdbk_usr_id', $user_id)
        ->where('svc_id','2')
        ->orderBy('tblservice_feedback_questions.svc_fdbk_q_id', 'asc')
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
                $result_array[] = array($row->svc_fdbk_rating_id => array($row->svc_fdbk_rating => Thds::get_all_csf($row->svc_fdbk_rating_id)));
            }

            
            return $result_array;

        }else if($id == 2){ // pie sex 
            
            $where = ''; $group_by = ''; $join = ''; $sub_q = ''; $select = '';

            $where .= ' AND pp_sex > 0 ';
            $where .= ' AND svc_fdbk_q_code LIKE "CSF-V2022" ';
            $where .= ' AND svc_id LIKE 2 ';
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
            $where .= ' AND svc_id LIKE 2 ';
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

        }else if($id == 3){ // bar agemem 
            
            $where = ''; $group_by = ''; $join = ''; $sub_q = ''; $select = '';

            $where .= ' AND svc_fdbk_q_code LIKE "CSF-V2022" ';
            $where .= ' AND svc_id LIKE 2 ';
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
            $where .= ' AND svc_id LIKE 2 ';
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
        'AND svc_fdbk_q_answer LIKE '. $id . ' AND svc_id LIKE 2'. 
        ') AS total '. 
        'FROM new_dbskms.tblservice_feedback_questions '. 
        'WHERE svc_fdbk_q_choices LIKE "1,2,3,4,5" AND svc_fdbk_q_code LIKE "CSF-V2022" ');

        return $query;

    }
}
