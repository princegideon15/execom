<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class Feedback extends Model
{
    public $timestamps = true;

    protected $table = 'tblfeedbacks';

    protected $fillable = ['id','fb_rate_ui','fb_suggest_ui','fb_rate_ux','fb_suggest_ux','fb_usr_id'];

    public function getTableColumns($tbl) {

        return $this->getConnection()->getSchemaBuilder()->getColumnListing($tbl);

    }

    static function get_ratings(){

        $query = DB::table('tblratings')
        ->select('rate_description', 
                    DB::raw('(select count(*) from tblfeedbacks where fb_rate_ui like tblratings.id) as UI'),
                    DB::raw('(select count(*) from tblfeedbacks where fb_rate_ux like tblratings.id) as UX'))
                    ->get();   

        return $query;
    }

    static function get_feedbacks(){
        $query = DB::table('tblfeedbacks')
        ->select('created_at', 'fb_suggest_ui', 'fb_suggest_ux',
                    DB::raw('(select rate_description from tblratings where id like fb_rate_ui) as UI'),
                    DB::raw('(select rate_description from tblratings where id like fb_rate_ux) as UX'),
                    DB::raw('(select name from users where user_id like fb_usr_id) as name'))
                    ->orderBy('created_at', 'desc')
                    ->get();   

        return $query;
    }

    // get qcsf questions decsiprtion
    static function get_csf_desc(){
        return DB::connection('dbskms')->table('tblservice_feedback_questions')
        ->select('svc_fdbk_q_desc', 'svc_fdbk_q_id')
        // ->where('svc_fdbk_q_choices', '1,2,3,4,5')
        ->where('svc_fdbk_q_code', 'CSF-V2022')
        ->get();
    }

    static function get_overall_csf_graph($id){


        $sex = DB::connection('dbskms')->table('tblsex')
                ->select('s_id','sex')
                ->get();  

        $sex_bris = DB::connection('dbbris')->table('tblsex')
        ->select('sx_id','sx_sex')
        ->get();

        $ins = DB::connection('dbskms')->table('tblaffiliation_type')
                ->select('aff_type_id','aff_type')
                ->get(); 
      
        if($id == 1){ // stacked bar sex

            // memis

            foreach($sex as $row){  
                $memis[] = array($row->sex => Feedback::get_overall_sex_memis($row->s_id));
            }

             $result_array[] = $memis;
           
             // bris

            foreach($sex_bris as $row){  
                $bris[] = array($row->sx_sex => Feedback::get_overall_sex_bris($row->sx_id));
            }

             $result_array[] = $bris;

            // ej

            foreach($sex as $row){  
                $ej[] = array($row->sex => Feedback::get_overall_sex_ej($row->s_id));
            }
            
            $result_array[] = $ej;

            // lms

            foreach($sex as $row){  
                $lms[] = array($row->sex => Feedback::get_overall_sex_lms($row->s_id));
            }
            
            $result_array[] = $lms;
            
            // thds

            foreach($sex as $row){  
                $thds[] = array($row->sex => Feedback::get_overall_sex_thds($row->s_id));
            }
            
            $result_array[] = $thds;

            return $result_array;

        }else if($id == 2){ // bar institiution
            
            // memis

            foreach($ins as $row){  
                $memis[] = array($row->aff_type => Feedback::get_overall_aff_memis($row->aff_type_id));
            }

             $result_array[] = $memis;
             // bris

            foreach($ins as $row){  
                $bris[] = array($row->aff_type => Feedback::get_overall_aff_bris($row->aff_type_id));
            }

             $result_array[] = $bris;

            // ej

            foreach($ins as $row){  
                $ej[] = array($row->aff_type => Feedback::get_overall_aff_ej($row->aff_type_id));
            }
            
            $result_array[] = $ej;

            // lms

            foreach($ins as $row){  
                $lms[] = array($row->aff_type => Feedback::get_overall_aff_lms($row->aff_type_id));
            }
            
            $result_array[] = $lms;
            
            // thds

            foreach($ins as $row){  
                $thds[] = array($row->aff_type => Feedback::get_overall_aff_thds($row->aff_type_id));
            }
            
            $result_array[] = $thds;

            return $result_array;

        }     
    }

    // overall sex

    static function get_overall_sex_memis($id){

        $query = DB::connection('dbskms')->table('tblservice_feedbacks')
        ->join('tblpersonal_profiles','pp_usr_id','=','svc_fdbk_usr_id')
        ->join('tblservice_feedback_questions','tblservice_feedback_questions.svc_fdbk_q_id','=','tblservice_feedbacks.svc_fdbk_q_id')
        ->join('tblemployments','emp_usr_id','=','pp_usr_id')
        ->join('tblregions','region_id','=','emp_region')
        ->join('tblsex','s_id','=','pp_sex')
        ->join('tblmembership_profiles','mpr_usr_id','=','pp_usr_id')
        ->join('tbldivisions','div_id','=','mpr_div_id')
        ->select('div_number', 'pp_usr_id', 'pp_last_name', 'pp_first_name', 'pp_email', 'sex', 'region_name', 'emp_pos', 'emp_ins', 'tblservice_feedbacks.date_created as date_created', 
        DB::connection('dbskms')->raw('YEAR(CURDATE())-YEAR(pp_date_of_birth) AS age'))
        ->where('svc_id','3')
        ->where('pp_sex', $id)
        ->whereRaw('emp_id in (SELECT MAX(emp_id) AS emp_id FROM new_dbskms.tblemployments GROUP by emp_usr_id ORDER by emp_id DESC)')
        ->where('svc_fdbk_q_code', 'CSF-V2022')
        ->orderBy('pp_last_name')
        ->groupBy('svc_fdbk_usr_id')
        ->get();
    
        return count($query);
         
        // $where = ''; $group_by = ''; $join = ''; $sub_q = ''; $select = '';

        // // $where .= ' WHERE pp_sex > 0 ';
        // $where .= ' AND pp_sex LIKE ' . $id;
        // $where .= ' AND svc_fdbk_q_code LIKE "CSF-V2022" ';
        // $where .= ' AND svc_id LIKE 3 ';
        // $where .= ' AND emp_id IN (SELECT MAX(emp_id) AS emp_id FROM new_dbskms.tblemployments GROUP by emp_usr_id ORDER by emp_id DESC) ';
        // $group_by .= ' GROUP BY svc_fdbk_usr_id ';
        // $join .= ' JOIN new_dbskms.tblsex on s_id = pp_sex ';
                        
        // $select .= 'SELECT * '. 
        // 'FROM new_dbskms.tblservice_feedbacks '. 
        // 'JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = svc_fdbk_usr_id '.
        // 'JOIN new_dbskms.tblemployments ON emp_usr_id = pp_usr_id '.
        // 'JOIN new_dbskms.tblmembership_profiles ON mpr_usr_id = pp_usr_id '.
        // 'JOIN new_dbskms.tblservice_feedback_questions ON new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id = new_dbskms.tblservice_feedbacks.svc_fdbk_q_id '. $join . 
        // ' '. $where . $group_by;

        // return count(DB::select($select));
    }

    static function get_overall_sex_bris($id){

        
        
        $query = DB::connection('dbbris')->table('tblservice_feedbacks')
        ->join('tbl_csf_respondents','tbl_csf_respondents.fb_id','=','tblservice_feedbacks.fb_id')
        ->join('new_dbskms.tblservice_feedback_questions','new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id','=','tblservice_feedbacks.svc_fdbk_q_id')
        ->join('tblsex','sx_id','=','tbl_csf_respondents.sex')
        ->join('new_dbskms.tbldivisions','div_id','=','tbl_csf_respondents.sci_div')
        ->join('new_dbskms.tblregions','region_id','=','tbl_csf_respondents.region')
        ->select('tbl_csf_respondents.*', 'sx_sex', 'region_name', 'div_number')
        ->where('new_dbskms.tblservice_feedback_questions.svc_fdbk_q_code', 'CSF-V2022')
        ->where('tbl_csf_respondents.sex', $id)
        ->groupBy('tbl_csf_respondents.fb_id')
        ->get();

            // $where = ''; $group_by = ''; $join = ''; $sub_q = ''; $select = '';

            // $where .= ' WHERE dbbris.tbl_csf_respondents.sex LIKE ' . $id;
            // $where .= ' AND svc_fdbk_q_code LIKE "CSF-V2022" ';
            // $group_by .= ' GROUP BY tbl_csf_respondents.fb_id ';
            // $join .= ' JOIN dbbris.tblsex on sx_id = dbbris.tbl_csf_respondents.sex ';
                            
            // $select .= 'SELECT * '.
            // 'FROM dbbris.tbl_csf_respondents '. 
            // 'JOIN dbbris.tblservice_feedbacks ON tblservice_feedbacks.fb_id = tbl_csf_respondents.fb_id '.
            // 'JOIN new_dbskms.tblservice_feedback_questions ON new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id = dbbris.tblservice_feedbacks.svc_fdbk_q_id '. $join . 
            // ' '. $where . $group_by;


            // return count(DB::select($select));
    }

    static function get_overall_sex_ej($id){

        $query = DB::connection('dbej')->table('tblservice_feedbacks')
        ->join('tblclients','clt_id','=','svc_fdbk_usr_id')
        ->join('new_dbskms.tblsex','s_id','=','clt_sex')
        ->join('new_dbskms.tblservice_feedback_questions','new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id','=','tblservice_feedbacks.svc_fdbk_q_id')
        ->select('tblclients.*', 'new_dbskms.tblsex.sex as sex_name')
        ->where('tblservice_feedback_questions.svc_fdbk_q_code', 'CSF-V2022')
        ->groupBy('svc_fdbk_usr_id')
        ->get();

        return count($query);
            
            // $where = ''; $group_by = ''; $join = ''; $sub_q = ''; $select = '';

            // $where .= ' WHERE dbej.tblservice_feedbacks.svc_fdbk_q_code LIKE "CSF-V2022" ';
            // $where .= ' AND clt_sex LIKE ' . $id;
            // $group_by .= ' GROUP BY clt_id ';
            // $join .= ' JOIN new_dbskms.tblsex on s_id = clt_sex ';
                            
            // $select .= 'SELECT *'. 
            // 'FROM dbej.tblclients '. 
            // 'JOIN dbej.tblservice_feedbacks ON svc_fdbk_usr_id = clt_id '.
            // 'JOIN new_dbskms.tblservice_feedback_questions ON new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id = dbej.tblservice_feedbacks.svc_fdbk_q_id '. $join . 
            // ' '. $where . $group_by;


            // return count(DB::select($select));
    }

    static function get_overall_sex_lms($id){

        $query = DB::connection('dblms')->table('tblservice_feedbacks')
        ->join('tblusers','p_id','=','svc_fdbk_usr_id')
        ->join('tblpersonal_info','tblpersonal_info.p_id','=','tblusers.p_id')
        ->join('new_dbskms.tblsex','s_id','=','p_sex')
        ->join('new_dbskms.tblservice_feedback_questions','new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id','=','tblservice_feedbacks.svc_fdbk_q_id')
        ->select('tblpersonal_info.*', 'tblservice_feedbacks.date_created as date_submitted', 'usr_email', 'sex')
        ->where('tblservice_feedback_questions.svc_fdbk_q_code', 'CSF-V2022')
        ->where('p_sex', $id)
        ->groupBy('svc_fdbk_usr_id')
        ->get();

        return count($query);

            // $where = ''; $group_by = ''; $join = ''; $sub_q = ''; $select = '';

            // $where .= ' WHERE new_dbskms.tblservice_feedback_questions.svc_fdbk_q_code LIKE "CSF-V2022" ';
            // $where .= ' AND p_sex LIKE ' . $id;
            // $group_by .= ' GROUP BY tblusers.p_id ';
            // $join .= ' JOIN new_dbskms.tblsex on s_id = p_sex ';
                            
            // $select .= 'SELECT * '. 
            // 'FROM dblms.tblpersonal_info '. 
            // 'JOIN dblms.tblusers ON tblusers.p_id = tblpersonal_info.p_id '.
            // 'JOIN dblms.tblservice_feedbacks ON svc_fdbk_usr_id = tblusers.p_id '.
            // 'JOIN new_dbskms.tblservice_feedback_questions ON new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id = dblms.tblservice_feedbacks.svc_fdbk_q_id '. $join . 
            // ' '. $where . $group_by;

            // return count(DB::select($select));
    }

    static function get_overall_sex_thds($id){

        
        $query = DB::connection('dbskms')->table('tblservice_feedbacks')
        ->join('tblpersonal_profiles','pp_usr_id','=','tblservice_feedbacks.svc_fdbk_usr_id')
        ->join('tblservice_feedback_questions','tblservice_feedback_questions.svc_fdbk_q_id','=','tblservice_feedbacks.svc_fdbk_q_id')
        ->join('tblemployments','emp_usr_id','=','pp_usr_id', 'left')
        ->join('tblregions','region_id','=','emp_region', 'left')
        ->join('tblmembership_profiles','mpr_usr_id','=','pp_usr_id', 'left')
        ->join('tbldivisions','div_id','=','mpr_div_id', 'left')
        ->join('tblsex','s_id','=','pp_sex')
        ->select('div_number','tblservice_feedbacks.svc_fdbk_usr_id as user_id', 'pp_last_name', 'pp_first_name', 'pp_email', 'sex', 'region_name', 'emp_pos', 'emp_ins', 'tblservice_feedbacks.date_created as date_created', 
        DB::connection('dbskms')->raw('YEAR(CURDATE())-YEAR(pp_date_of_birth) AS age'))
        ->where('svc_id', 2)
        ->where('pp_sex', $id)
        ->where('tblservice_feedback_questions.svc_fdbk_q_code', 'CSF-V2022')
        ->whereRaw('emp_id in (SELECT MAX(emp_id) AS emp_id FROM new_dbskms.tblemployments GROUP by emp_usr_id ORDER by emp_id DESC)')
        ->orderBy('pp_last_name')
        ->groupBy('svc_fdbk_usr_id')
        ->get();

        return count($query);
            
            // $where = ''; $group_by = ''; $join = ''; $sub_q = ''; $select = '';

            // $where .= ' WHERE pp_sex > 0 ';
            // $where .= ' AND pp_sex LIKE '. $id;
            // $where .= ' AND svc_fdbk_q_code LIKE "CSF-V2022" ';
            // $where .= ' AND svc_id LIKE 2 ';
            // $group_by .= ' GROUP BY pp_usr_id ';
            // $join .= ' JOIN new_dbskms.tblsex on s_id = pp_sex ';
                            
            // $select .= 'SELECT * '. 
            // 'FROM new_dbskms.tblpersonal_profiles '. 
            // 'JOIN new_dbskms.tblservice_feedbacks ON svc_fdbk_usr_id = pp_usr_id '.
            // 'JOIN new_dbskms.tblservice_feedback_questions ON new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id = new_dbskms.tblservice_feedbacks.svc_fdbk_q_id '. $join . 
            // ' '. $where . $group_by;

            // return count(DB::select($select));
    }

    // overall affiliation

    static function get_overall_aff_memis($id){

        $query = DB::connection('dbskms')->table('tblservice_feedbacks')
        ->join('tblpersonal_profiles','pp_usr_id','=','svc_fdbk_usr_id')
        ->join('tblservice_feedback_questions','tblservice_feedback_questions.svc_fdbk_q_id','=','tblservice_feedbacks.svc_fdbk_q_id')
        ->join('tblemployments','emp_usr_id','=','pp_usr_id')
        ->join('tblregions','region_id','=','emp_region')
        ->join('tblsex','s_id','=','pp_sex')
        ->join('tblmembership_profiles','mpr_usr_id','=','pp_usr_id')
        ->join('tbldivisions','div_id','=','mpr_div_id')
        ->join('tblaffiliation_type','aff_type_id','=','svc_fdbk_q_answer')
        ->select('div_number', 'pp_usr_id', 'pp_last_name', 'pp_first_name', 'pp_email', 'sex', 'region_name', 'emp_pos', 'emp_ins', 'tblservice_feedbacks.date_created as date_created', 
        DB::connection('dbskms')->raw('YEAR(CURDATE())-YEAR(pp_date_of_birth) AS age'))
        ->where('svc_id','3')
        ->where('svc_fdbk_q_order', '1')
        ->where('svc_fdbk_q_answer', $id)
        ->whereRaw('emp_id in (SELECT MAX(emp_id) AS emp_id FROM new_dbskms.tblemployments GROUP by emp_usr_id ORDER by emp_id DESC)')
        ->where('svc_fdbk_q_code', 'CSF-V2022')
        ->orderBy('pp_last_name')
        ->groupBy('svc_fdbk_usr_id')
        ->get();
    
        return count($query);

            
        // $where = ''; $group_by = ''; $join = ''; $sub_q = ''; $select = '';

        // $where .= ' WHERE svc_fdbk_q_code LIKE "CSF-V2022" ';
        // $where .= ' AND svc_id LIKE 3 ';
        // $where .= ' AND svc_fdbk_q_order LIKE 1 ';
        // $where .= ' AND svc_fdbk_q_answer LIKE ' . $id;
        // $group_by .= ' GROUP BY pp_usr_id ';

        // $join .= 'JOIN new_dbskms.tblaffiliation_type ON aff_type_id = svc_fdbk_q_answer ';

        // $select .= 'SELECT *'.
        // 'FROM new_dbskms.tblpersonal_profiles '. 
        // 'JOIN new_dbskms.tblservice_feedbacks ON svc_fdbk_usr_id = pp_usr_id '.
        // 'JOIN new_dbskms.tblservice_feedback_questions ON new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id = new_dbskms.tblservice_feedbacks.svc_fdbk_q_id '. $join . 
        // ' '. $where . $group_by;

        // return count(DB::select($select));
    }

    static function get_overall_aff_bris($id){

        $query = DB::connection('dbbris')->table('tblservice_feedbacks')
        ->join('tbl_csf_respondents','tbl_csf_respondents.fb_id','=','tblservice_feedbacks.fb_id')
        ->join('new_dbskms.tblservice_feedback_questions','new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id','=','tblservice_feedbacks.svc_fdbk_q_id')
        ->join('tblsex','sx_id','=','tbl_csf_respondents.sex')
        ->join('new_dbskms.tbldivisions','div_id','=','tbl_csf_respondents.sci_div')
        ->join('new_dbskms.tblregions','region_id','=','tbl_csf_respondents.region')
        ->join('new_dbskms.tblaffiliation_type','aff_type_id','=','svc_fdbk_q_answer')
        ->select('tbl_csf_respondents.*', 'sx_sex', 'region_name', 'div_number')
        ->where('new_dbskms.tblservice_feedback_questions.svc_fdbk_q_code', 'CSF-V2022')
        ->where('svc_fdbk_q_order', '1')
        ->where('svc_fdbk_q_answer', $id)
        ->groupBy('tbl_csf_respondents.fb_id')
        ->get();

        return count($query);


        // $where = ''; $group_by = ''; $join = ''; $sub_q = ''; $select = '';

        // $where .= ' WHERE svc_fdbk_q_code LIKE "CSF-V2022" ';
        // $where .= ' AND svc_fdbk_q_order LIKE 1 ';
        // $where .= ' AND svc_fdbk_q_answer LIKE ' . $id;
        // $group_by .= ' GROUP BY tbl_csf_respondents.fb_id ';

        // $join .= 'JOIN new_dbskms.tblaffiliation_type ON aff_type_id = svc_fdbk_q_answer ';

        // $select .= 'SELECT * '.
        // 'FROM dbbris.tbl_csf_respondents '. 
        // 'JOIN dbbris.tblservice_feedbacks ON tblservice_feedbacks.fb_id = tbl_csf_respondents.fb_id '.
        // 'JOIN new_dbskms.tblservice_feedback_questions ON new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id = dbbris.tblservice_feedbacks.svc_fdbk_q_id '. $join . 
        // ' '. $where . $group_by;

        // return count(DB::select($select));
    }

    static function get_overall_aff_ej($id){

        $query = DB::connection('dbej')->table('tblservice_feedbacks')
        ->join('tblclients','clt_id','=','svc_fdbk_usr_id')
        ->join('new_dbskms.tblsex','s_id','=','clt_sex')
        ->join('new_dbskms.tblaffiliation_type','aff_type_id','=','svc_fdbk_q_answer')
        ->join('new_dbskms.tblservice_feedback_questions','new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id','=','tblservice_feedbacks.svc_fdbk_q_id')
        ->select('tblclients.*', 'new_dbskms.tblsex.sex as sex_name')
        ->where('tblservice_feedback_questions.svc_fdbk_q_code', 'CSF-V2022')
        ->where('svc_fdbk_q_order', '1')
        ->where('svc_fdbk_q_answer', $id)
        ->groupBy('svc_fdbk_usr_id')
        ->get();

        return count($query);
            
        // $where = ''; $group_by = ''; $join = ''; $sub_q = ''; $select = '';

        // $where .= ' WHERE dbej.tblservice_feedbacks.svc_fdbk_q_code LIKE "CSF-V2022" ';
        // $where .= ' AND svc_fdbk_q_order LIKE 1 ';
        // $where .= ' AND svc_fdbk_q_answer LIKE ' . $id;
        // $group_by .= ' GROUP BY clt_id ';
        // $join .= ' JOIN new_dbskms.tblaffiliation_type ON aff_type_id = svc_fdbk_q_answer ';

        // $select .= 'SELECT * '.
        // 'FROM dbej.tblclients '. 
        // 'JOIN dbej.tblservice_feedbacks ON tblservice_feedbacks.svc_fdbk_usr_id = clt_id '.
        // 'JOIN new_dbskms.tblservice_feedback_questions ON new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id = dbej.tblservice_feedbacks.svc_fdbk_q_id '. $join . 
        // ' '. $where . $group_by;


        // return count(DB::select($select));
    }

    static function get_overall_aff_lms($id){

        $query = DB::connection('dblms')->table('tblservice_feedbacks')
        ->join('tblusers','p_id','=','svc_fdbk_usr_id')
        ->join('tblpersonal_info','tblpersonal_info.p_id','=','tblusers.p_id')
        ->join('new_dbskms.tblsex','s_id','=','p_sex')
        ->join('new_dbskms.tblaffiliation_type','aff_type_id','=','svc_fdbk_q_answer')
        ->join('new_dbskms.tblservice_feedback_questions','new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id','=','tblservice_feedbacks.svc_fdbk_q_id')
        ->select('tblpersonal_info.*', 'tblservice_feedbacks.date_created as date_submitted', 'usr_email', 'sex')
        ->where('tblservice_feedback_questions.svc_fdbk_q_code', 'CSF-V2022')
        ->where('svc_fdbk_q_order', '1')
        ->where('svc_fdbk_q_answer', $id)
        ->groupBy('svc_fdbk_usr_id')
        ->get();

        return count($query);
            
        // $where = ''; $group_by = ''; $join = ''; $sub_q = ''; $select = '';

        // $where .= ' WHERE new_dbskms.tblservice_feedback_questions.svc_fdbk_q_code LIKE "CSF-V2022" ';
        // $where .= ' AND svc_fdbk_q_order LIKE 1 ';
        // $where .= ' AND svc_fdbk_q_answer LIKE ' . $id;
        // $group_by .= ' GROUP BY p_id ';

        // $join .= ' JOIN new_dbskms.tblaffiliation_type ON aff_type_id = svc_fdbk_q_answer ';
        
        // $select .= 'SELECT *'.
        // 'FROM dblms.tblpersonal_info '. 
        // 'JOIN dblms.tblservice_feedbacks ON tblservice_feedbacks.svc_fdbk_usr_id = p_id '.
        // 'JOIN new_dbskms.tblservice_feedback_questions ON new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id = dblms.tblservice_feedbacks.svc_fdbk_q_id '. $join . 
        // ' '. $where . $group_by;

        // return count(DB::select($select));
    }

    static function get_overall_aff_thds($id){

        
        $query = DB::connection('dbskms')->table('tblservice_feedbacks')
        ->join('tblpersonal_profiles','pp_usr_id','=','tblservice_feedbacks.svc_fdbk_usr_id')
        ->join('tblservice_feedback_questions','tblservice_feedback_questions.svc_fdbk_q_id','=','tblservice_feedbacks.svc_fdbk_q_id')
        ->join('tblemployments','emp_usr_id','=','pp_usr_id', 'left')
        ->join('tblregions','region_id','=','emp_region', 'left')
        ->join('tblmembership_profiles','mpr_usr_id','=','pp_usr_id', 'left')
        ->join('tbldivisions','div_id','=','mpr_div_id', 'left')
        ->join('tblsex','s_id','=','pp_sex')
        ->join('new_dbskms.tblaffiliation_type','aff_type_id','=','svc_fdbk_q_answer')
        ->select('div_number','tblservice_feedbacks.svc_fdbk_usr_id as user_id', 'pp_last_name', 'pp_first_name', 'pp_email', 'sex', 'region_name', 'emp_pos', 'emp_ins', 'tblservice_feedbacks.date_created as date_created', 
        DB::connection('dbskms')->raw('YEAR(CURDATE())-YEAR(pp_date_of_birth) AS age'))
        ->where('svc_id', 2)
        ->where('svc_fdbk_q_order', '1')
        ->where('svc_fdbk_q_answer', $id)
        ->where('tblservice_feedback_questions.svc_fdbk_q_code', 'CSF-V2022')
        ->whereRaw('emp_id in (SELECT MAX(emp_id) AS emp_id FROM new_dbskms.tblemployments GROUP by emp_usr_id ORDER by emp_id DESC)')
        ->orderBy('pp_last_name')
        ->groupBy('svc_fdbk_usr_id')
        ->get();

        return count($query);
            
        // $where = ''; $group_by = ''; $join = ''; $sub_q = ''; $select = '';

        // $where .= ' WHERE svc_fdbk_q_code LIKE "CSF-V2022" ';
        // $where .= ' AND svc_id LIKE 2 ';
        // $where .= ' AND svc_fdbk_q_order LIKE 1 ';
        // $where .= ' AND svc_fdbk_q_answer LIKE ' . $id;
        // $group_by .= ' GROUP BY pp_usr_id ';

        // $join .= 'JOIN new_dbskms.tblaffiliation_type ON aff_type_id = svc_fdbk_q_answer ';

        // $select .= 'SELECT * '.
        // 'FROM new_dbskms.tblpersonal_profiles '. 
        // 'JOIN new_dbskms.tblservice_feedbacks ON svc_fdbk_usr_id = pp_usr_id '.
        // 'JOIN new_dbskms.tblservice_feedback_questions ON new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id = new_dbskms.tblservice_feedbacks.svc_fdbk_q_id '. $join . 
        // ' '. $where . $group_by;
        
        // return count(DB::select($select));
    }
    

}
