<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Ejournal extends Model
{
    protected $connection = 'dbej';

    static function count_journals(){   
       return DB::connection('dbej')->table('tbljournals')->count();
    }

    static function count_articles(){
        return DB::connection('dbej')->table('tblarticles')->count();
    }

    static function count_downloads(){
        return DB::connection('dbej')->table('tblclients')
        ->distinct('clt_journal_downloaded_id')
        ->count();
    }

    static function count_clients(){
        return DB::connection('dbej')->table('tblclients')
        ->distinct('clt_email')
        ->count();
    }

    static function count_citees(){
        return DB::connection('dbej')->table('tblcitations')
        ->distinct('cite_email')
        ->count();
    }

    static function count_views(){
        return DB::connection('dbej')->table('tblhits_abstract')
        ->distinct('hts_art_id')
        ->count();
    }

    static function count_cites(){
        return DB::connection('dbej')->table('tblcitations')
        ->distinct('cite_art_id')
        ->count();
    }

    static function count_visitors(){
        return DB::connection('dbej')->table('tblvisitor_details')
        ->distinct('vis_location')
        ->count();
    }

    static function get_years(){
        return DB::connection('dbej')->table('tbljournals')
                                       ->select('jor_year')
                                       ->orderby('jor_year')
                                       ->distinct()
                                       ->get()->pluck('jor_year');
    }

    static function get_journals_by_year(){

        return DB::connection('dbej')->table('tbljournals')
        ->select('jor_year as label', DB::connection('dbej')->raw('count(*) as total'))
        ->groupBy('jor_year')
        ->orderBy('jor_year','asc')
        ->get()
        ->toArray();

    }

    static function get_articles_by_journal(){

        return DB::connection('dbej')->table('tbljournals')
        ->select('jor_volume as label', DB::connection('dbej')->raw('sum((select count(*) from tblarticles where art_jor_id like jor_id)) as total'))
        ->groupBy('jor_volume')
        ->orderBy('jor_volume','asc')
        ->get()
        ->toArray();
        
    }

    static function get_pdf_downloads_by_journal(){
        
        return DB::connection('dbej')->table('tbljournals')
        ->select('jor_volume as label', DB::connection('dbej')->raw('sum((select count(clt_journal_downloaded_id) from tblclients 
        join tblarticles on clt_journal_downloaded_id = art_id where art_jor_id like jor_id)) as total'))
        ->groupBy('jor_volume')
        ->orderBy('jor_year','asc')
        ->get()
        ->toArray();
    }

    static function get_abstract_views_by_journal(){
        return DB::connection('dbej')->table('tbljournals')
        ->select('jor_volume as label', DB::connection('dbej')->raw('sum((select count(hts_art_id) from tblhits_abstract 
        join tblarticles on hts_art_id = art_id where art_jor_id like jor_id)) as total'))
        ->groupBy('jor_volume')
        ->orderBy('jor_year','asc')
        ->get()
        ->toArray();
    }

    static function get_citations_by_journal(){
        return DB::connection('dbej')->table('tbljournals')
        ->select('jor_volume as label', DB::connection('dbej')->raw('sum((select count(cite_art_id) from tblcitations 
        join tblarticles on cite_art_id = art_id where art_jor_id like jor_id)) as total'))
        ->groupBy('jor_volume')
        ->orderBy('jor_year','asc')
        ->get()
        ->toArray();
    }

    static function get_visitors_by_year ($year){
        
        return DB::connection('dbej')->table('tblvisitor_details')
        ->select(DB::connection('dbej')->raw('EXTRACT(MONTH from vis_datetime) as label, count(*) as total'))
        ->where('vis_datetime','like','%' . $year . '%')
        ->groupBy('label')
        ->orderBy('label','asc')
        ->get()
        ->toArray();
    }

    static function get_visitors_year(){
        return DB::connection('dbej')->table('tblvisitor_details')
        ->select(DB::connection('dbej')->raw('EXTRACT(YEAR from vis_datetime) as years'))
        ->groupBy('years')
        ->orderBy('years','desc')
        ->get();
    }

    /**
     * get all data
     */
    static function get_published_articles(){
        return DB::connection('dbej')->table('tblarticles')
        ->select('art_title','art_author')
        ->orderBy('art_title','asc')
        ->get();
    }

    static function get_cited_articles(){
        return DB::connection('dbej')->table('tblarticles')
        // ->distinct()
        ->select('art_title','art_author', DB::connection('dbej')->raw('count(*) as total'))
        ->join('tblcitations','art_id','=','cite_art_id')
        ->groupBy('cite_art_id')
        ->orderBy('art_title','asc')
        ->get();
    }

    static function get_viewed_articles(){
        return DB::connection('dbej')->table('tblarticles')
        // ->distinct()
        ->select('art_title','art_author', DB::connection('dbej')->raw('count(*) as total'))
        ->join('tblhits_abstract','art_id','=','hts_art_id')
        ->groupBy('hts_art_id')
        ->orderBy('art_title','asc')
        ->get();
    }

    static function get_downloaded_articles(){
        return DB::connection('dbej')->table('tblarticles')
        // ->distinct()
        ->select('art_title','art_author', DB::connection('dbej')->raw('count(*) as total'))
        ->join('tblclients','art_id','=','clt_journal_downloaded_id')
        ->orderBy('art_title','asc')
        ->groupBy('clt_journal_downloaded_id')
        ->get();
    }

    static function get_most_clients(){
        return DB::connection('dbej')->table('tblclients')
        ->select('clt_name', 'clt_affiliation', 'clt_email', DB::connection('dbej')->raw('count(*) as total'))
        ->orderBy('clt_name','asc')
        ->groupBy('clt_email')
        ->get();
    }

    static function get_citees(){
        return DB::connection('dbej')->table('tblcitations')
        ->select('cite_name', 'cite_email', DB::connection('dbej')->raw('count(*) as total'))
        ->orderBy('cite_name','asc')
        ->groupBy('cite_email')
        ->get();
    }    


    static function get_visitors_origin(){
        return DB::connection('dbej')->table('tblvisitor_details')
        ->distinct()
        ->select('vis_location')
        ->orderBy('vis_location','asc')
        ->get();
    }

    // search

    static function search($keyword, $where){
        return DB::connection('dbej')->table('tblarticles')
        ->select('*')
        ->where($where, 'LIKE', '%' . $keyword . '%')   
        ->orderBy($where, 'asc') 
        ->get();
    }

    

    static function get_csf_list(){
        
        return DB::connection('dbej')->table('tblservice_feedbacks')
        ->join('tblclients','clt_id','=','svc_fdbk_usr_id')
        ->join('new_dbskms.tblsex','s_id','=','clt_sex')
        ->join('new_dbskms.tblservice_feedback_questions','new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id','=','tblservice_feedbacks.svc_fdbk_q_id')
        ->select('tblclients.*', 'new_dbskms.tblsex.sex as sex_name')
        ->where('tblservice_feedback_questions.svc_fdbk_q_code', 'CSF-V2022')
        ->groupBy('svc_fdbk_usr_id')
        ->get();
    }

    static function get_csf_answers($user_id){
        return DB::connection('dbej')->table('tblservice_feedbacks')
        ->join('new_dbskms.tblservice_feedback_questions','new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id','=','tblservice_feedbacks.svc_fdbk_q_id')
        ->where('new_dbskms.tblservice_feedback_questions.svc_fdbk_q_code', 'CSF-V2022')
        ->where('svc_fdbk_usr_id', $user_id)
        // ->groupBy('tbl_csf_respondents.fb_id')
        ->get();
    }

    // get csf desc
    static function get_csf_desc($id, $user){
        return DB::connection('dbej')->table('tblservice_feedbacks')
        ->join('tblclients','clt_id','=','tblservice_feedbacks.svc_fdbk_usr_id')
        ->join('new_dbskms.tblservice_feedback_questions','new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id','=','tblservice_feedbacks.svc_fdbk_q_id')
        ->select('tblservice_feedbacks.svc_fdbk_q_answer as rate')
        ->where('svc_fdbk_q_order', $id)
        ->where('dbej.tblservice_feedbacks.svc_fdbk_q_code', 'CSF-V2022')
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
                $result_array[] = array($row->svc_fdbk_rating_id => array($row->svc_fdbk_rating => Ejournal::get_all_csf($row->svc_fdbk_rating_id)));
            }

            
            return $result_array;

        }else if($id == 2){ // pie sex 
            
            $where = ''; $group_by = ''; $join = ''; $sub_q = ''; $select = '';

            // $where .= ' AND pp_sex > 0 ';
            $where .= ' AND dbej.tblservice_feedbacks.svc_fdbk_q_code LIKE "CSF-V2022" ';
            // $where .= ' AND svc_id LIKE 3 ';
            $group_by .= ' GROUP BY clt_id ';
            $join .= ' JOIN new_dbskms.tblsex on s_id = clt_sex ';
                            
            $sub_q .= 'SELECT s_id, count(clt_id) as total, sex AS label '. 
            'FROM dbej.tblclients '. 
            'JOIN dbej.tblservice_feedbacks ON svc_fdbk_usr_id = clt_id '.
            'JOIN new_dbskms.tblservice_feedback_questions ON new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id = dbej.tblservice_feedbacks.svc_fdbk_q_id '. $join . 
            ' '. $where . $group_by;

            $select .= 'SELECT count(total) as total, label FROM '. 
            '( ' . $sub_q . ') as tmp GROUP BY s_id ORDER BY s_id desc';

            return DB::select($select);

        }else if($id == 3){ // bar region 
            
            $where = ''; $group_by = ''; $join = ''; $sub_q = ''; $select = '';

            $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = clt_id) '; 
            // $where .= ' AND emp_period_to = "Present" '; 
            $where .= ' AND dbej.tblservice_feedbacks.svc_fdbk_q_code LIKE "CSF-V2022" ';
            $where .= ' AND clt_member LIKE 1 ';
            $group_by .= ' GROUP BY clt_id ';
 
            $join .= ' LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = clt_id ';
            $join .= ' JOIN new_dbskms.tblregions on region_id = emp_region ';

            $sub_q .= 'SELECT count(clt_id) as total, region_name as label, region_id '.
            'FROM dbej.tblclients '. 
            'JOIN dbej.tblservice_feedbacks ON svc_fdbk_usr_id = clt_id '.
            'JOIN new_dbskms.tblservice_feedback_questions ON new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id = dbej.tblservice_feedbacks.svc_fdbk_q_id '. $join . 
            ' '. $where . $group_by;

            $select .= 'SELECT count(total) as total, label FROM '. 
            '( ' . $sub_q . ') as tmp GROUP BY region_id';

            return DB::select($select);

        }else if($id == 4){ // bar age 
            
            $where = ''; $group_by = ''; $join = ''; $sub_q = ''; $select = '';

            $where .= ' AND dbej.tblservice_feedbacks.svc_fdbk_q_code LIKE "CSF-V2022" ';
            $where .= ' AND clt_member LIKE 1 ';
            $group_by .= ' GROUP BY clt_id ';

            $join .= ' JOIN new_dbskms.tblpersonal_profiles ON pp_usr_id = clt_id ';

            $sub_q .= 'SELECT count(clt_id) AS total, '.
            'CASE  '.
            'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 20 AND 31 then "1" '.
            'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 30 AND 41 then "2" '.
            'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 40 AND 51 then "3" '.
            'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 50 AND 61 then "4" '.
            'WHEN YEAR(CURDATE()) - YEAR(pp_date_of_birth) BETWEEN 60 AND 71 then "5" '.
            'ELSE "6" END AS "range" '. 
            'FROM dbej.tblclients '.
            'JOIN dbej.tblservice_feedbacks ON svc_fdbk_usr_id = clt_id '.
            'JOIN new_dbskms.tblservice_feedback_questions ON new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id = dbej.tblservice_feedbacks.svc_fdbk_q_id '. $join . 
            'WHERE pp_date_of_birth LIKE \'%-%\' '. $where . $group_by;

            $select .= 'SELECT count(total) AS total, '. 
            '(SELECT age_range FROM new_dbskms.tblage_ranges WHERE age_id = tmp.range) AS label FROM '. 
            '( ' . $sub_q . ') AS tmp GROUP BY tmp.range';

            return DB::select($select);
        }else{ // bar affiliation 
             
            
            $where = ''; $group_by = ''; $join = ''; $sub_q = ''; $select = '';

            // $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = tbl_csf_respondents.fb_id) '; 
            // $where .= ' AND emp_period_to = "Present" '; 
            $where .= ' AND dbej.tblservice_feedbacks.svc_fdbk_q_code LIKE "CSF-V2022" ';
            $where .= ' AND svc_fdbk_q_order LIKE 1 ';
            // $where .= ' AND svc_id LIKE 3 ';
            $group_by .= ' GROUP BY clt_id ';
 
            // $join .= 'LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = clt_id ';
 
            $join .= ' JOIN new_dbskms.tblaffiliation_type ON aff_type_id = svc_fdbk_q_answer ';
            // $join .= ' JOIN new_dbskms.tblregions on region_id = region ';

            $sub_q .= 'SELECT count(clt_id) as total, aff_type as label, aff_type_id '.
            'FROM dbej.tblclients '. 
            'JOIN dbej.tblservice_feedbacks ON tblservice_feedbacks.svc_fdbk_usr_id = clt_id '.
            'JOIN new_dbskms.tblservice_feedback_questions ON new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id = dbej.tblservice_feedbacks.svc_fdbk_q_id '. $join . 
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
        '(SELECT COUNT(*) FROM dbej.tblservice_feedbacks '.
        'JOIN dbej.tblclients on clt_id = dbej.tblservice_feedbacks.svc_fdbk_usr_id '.
        'WHERE dbej.tblservice_feedbacks.svc_fdbk_q_id '. 
        'LIKE new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id '. 
        'AND svc_fdbk_q_answer LIKE '. $id . 
        ') AS total '. 
        'FROM new_dbskms.tblservice_feedback_questions '. 
        'WHERE svc_fdbk_q_choices LIKE "1,2,3,4,5" AND svc_fdbk_q_code LIKE "CSF-V2022" ');

        return $query;

    }
    
    
}
