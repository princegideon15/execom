<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Grant extends Model
{
    protected $db = 'dbrdlip';
    
    static function count_paper_grant(){   
        return DB::connection('dbrdlip')->table('tblilc_infos')->count();
    }
     
    static function count_pub_grant(){   
        return DB::connection('dbrdlip')->table('tblpub_infos')->count();
    }
    static function get_paper_grant(){  
        
        return DB::connection('dbrdlip')->table('tblilc_infos AS a')
        ->join('new_dbskms.tblpersonal_profiles AS b','a.rd_user_id','=','b.pp_usr_id')
        ->join('new_dbskms.tblmembers AS c','a.rd_user_id','=','c.mem_usr_id', 'left')
        ->join('new_dbskms.tblmembership_types AS d','c.mem_type','=','d.membership_type_id', 'left')
        ->join('new_dbskms.tbltitles AS e','e.title_id','=','b.pp_title', 'left')
        ->select('e.title_name', 'a.last_updated', 'a.row_id as id', 'pp_last_name', 'pp_first_name', 'pp_middle_name', 'a.date_created as date_submitted', 'mem_type', 'membership_type_name', 'rd_status', 'rd_user_id')
        ->get()
        ->toArray();

    }
     
    static function get_pub_grant(){   
        
        return DB::connection('dbrdlip')->table('tblpub_infos AS a')
        ->join('new_dbskms.tblpersonal_profiles AS b','a.pub_user_id','=','b.pp_usr_id')
        ->join('new_dbskms.tblmembers AS c','a.pub_user_id','=','c.mem_usr_id', 'left')
        ->join('new_dbskms.tblmembership_types AS d','c.mem_type','=','d.membership_type_id', 'left')
        ->join('new_dbskms.tbltitles AS e','e.title_id','=','b.pp_title', 'left')
        ->select('e.title_name', 'a.last_updated', 'a.row_id as id', 'pp_last_name', 'pp_first_name', 'pp_middle_name', 'a.date_created as date_submitted', 'mem_type', 'membership_type_name', 'pub_status', 'pub_user_id')
        ->get()
        ->toArray();
    } 

    static function get_csf_list(){
        return DB::connection('dbrdlip')->table('tblservice_feedbacks')
        ->join('new_dbskms.tblpersonal_profiles','pp_usr_id','=','tblservice_feedbacks.svc_fdbk_usr_id')
        ->join('new_dbskms.tblservice_feedback_questions','new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id','=','tblservice_feedbacks.svc_fdbk_q_id')
        ->join('new_dbskms.tblemployments','emp_usr_id','=','pp_usr_id', 'left')
        ->join('new_dbskms.tblregions','region_id','=','emp_region', 'left')
        ->join('new_dbskms.tblmembership_profiles','mpr_usr_id','=','pp_usr_id', 'left')
        ->join('new_dbskms.tbldivisions','div_id','=','mpr_div_id', 'left')
        ->join('new_dbskms.tblsex','s_id','=','pp_sex')
        ->select('div_number','tblservice_feedbacks.svc_fdbk_usr_id as user_id', 'pp_last_name', 'pp_first_name', 'pp_email', 'sex', 'region_name', 'emp_pos', 'emp_ins', 'tblservice_feedbacks.date_created as date_created', 
        DB::connection('dbskms')->raw('YEAR(CURDATE())-YEAR(pp_date_of_birth) AS age'))
        ->where('new_dbskms.tblservice_feedback_questions.svc_fdbk_q_code', 'CSF-V2022')
        ->orderBy('pp_last_name')
        ->groupBy('svc_fdbk_usr_id')
        ->get();
    }

    static function get_csf_answers($user_id){
        return DB::connection('dbrdlip')->table('tblservice_feedbacks')
        ->join('new_dbskms.tblservice_feedback_questions','new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id','=','tblservice_feedbacks.svc_fdbk_q_id')
        ->where('new_dbskms.tblservice_feedback_questions.svc_fdbk_q_code', 'CSF-V2022')
        ->where('svc_fdbk_usr_id', $user_id)
        ->orderBy('new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id', 'asc')
        ->get();
    }

    // get csf desc
    static function get_csf_desc($id, $user){
        return DB::connection('dbrdlip')->table('tblservice_feedbacks')
        ->join('new_dbskms.tblservice_feedback_questions','new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id','=','tblservice_feedbacks.svc_fdbk_q_id')
        ->join('new_dbskms.tblpersonal_profiles','pp_usr_id','=','tblservice_feedbacks.svc_fdbk_usr_id')
        ->select('tblservice_feedbacks.svc_fdbk_q_answer as rate')
        ->where('svc_fdbk_q_order', $id)
        ->where('svc_fdbk_q_code', 'CSF-V2022')
        ->where('svc_fdbk_usr_id', $user)
        ->get();
    }

    static function get_csf($id){
        
        if($id == 1){ // stacked bar category

            $result_array = array();

            $query = DB::connection('dbrdlip')->table('tblservice_feedback_ratings')
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
            'JOIN dbrdlip.tblservice_feedbacks ON svc_fdbk_usr_id = pp_usr_id '.
            'JOIN new_dbskms.tblservice_feedback_questions ON new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id = dbrdlip.tblservice_feedbacks.svc_fdbk_q_id '. $join . 
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
            'JOIN dbrdlip.tblservice_feedbacks ON svc_fdbk_usr_id = pp_usr_id '.
            'JOIN new_dbskms.tblservice_feedback_questions ON new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id = dbrdlip.tblservice_feedbacks.svc_fdbk_q_id '. $join . 
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
            'JOIN dbrdlip.tblservice_feedbacks ON svc_fdbk_usr_id = pp_usr_id '.
            'JOIN new_dbskms.tblservice_feedback_questions ON new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id = dbrdlip.tblservice_feedbacks.svc_fdbk_q_id '. $join . 
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
            'JOIN dbrdlip.tblservice_feedbacks ON svc_fdbk_usr_id = pp_usr_id '.
            'JOIN new_dbskms.tblservice_feedback_questions ON new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id = dbrdlip.tblservice_feedbacks.svc_fdbk_q_id '. $join . 
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
