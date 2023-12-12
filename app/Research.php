<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Research extends Model
{
    static function count_projects(){
        return DB::connection('dbbris')->table('tblproject_details')
        ->where('prd_proposal', 0)
        ->count();
        
    }

    static function count_research($value){
        return DB::connection('dbbris')->table('tblproject_details')->where('prd_research_type', $value)->count();
    }

    static function count_programs(){
        return DB::connection('dbbris')->table('tblprograms')
        ->where('prg_proposal', 0)
        ->count();
    }

    static function get_sdg(){

        return DB::connection('dbbris')->table('tblsdg')
       ->select('sdg_def as label', DB::connection('dbbris')
       ->raw('(select count(ca_proj_id) from tblconcerned_areas where ca_sdg like sdg_id) as total'))
       ->groupBy('sdg_def','sdg_id')
       ->orderBy('sdg_id','asc')
       ->get()
       ->toArray();

    }

    static function get_snt(){

        return DB::connection('dbbris')->table('tblsci_n_tech')
       ->select('st_name as label', DB::connection('dbbris')
       ->raw('(select count(ca_proj_id) from tblconcerned_areas where ca_s_and_t like st_id) as total'))
       ->groupBy('st_name','st_id')
       ->orderBy('st_id','asc')
       ->get()
       ->toArray();

    }

    static function get_nsea(){

        return DB::connection('dbbris')->table('tblnsea')
       ->select('nsea_name as label', DB::connection('dbbris')
       ->raw('(select count(ca_proj_id) from tblconcerned_areas where ca_nsea like nsea_id) as total'))
       ->groupBy('nsea_name','nsea_id')
       ->orderBy('nsea_id','asc')
       ->get()
       ->toArray();

    }

    static function get_nsub(){

        return DB::connection('dbbris')->table('tblnibra_sub_items')
       ->select('nibra_sub_name as label', DB::connection('dbbris')
       ->raw('(select count(ca_proj_id) from tblconcerned_areas where ca_nibra_sub like nibra_sub_id) as total'))
       ->groupBy('nibra_sub_name','nibra_sub_id')
       ->orderBy('nibra_sub_id','asc')
       ->get()
       ->toArray();

    }

    static function get_nibra(){

        return DB::connection('dbbris')->table('tblnibra')
       ->select('nibra_name as label', DB::connection('dbbris')
       ->raw('(select count(ca_proj_id) from tblconcerned_areas where ca_nibra like nibra_id) as total'))
       ->groupBy('nibra_name','nibra_id')
       ->orderBy('nibra_id','asc')
       ->get()
       ->toArray();

    }

    static function get_pdp(){

        return DB::connection('dbbris')->table('tblpdp')
       ->select('pdp_definition as label', DB::connection('dbbris')
       ->raw('(select count(ca_proj_id) from tblconcerned_areas where ca_pdp like pdp_id) as total'))
       ->groupBy('pdp_definition','pdp_id')
       ->orderBy('pdp_id','asc')
       ->get()
       ->toArray();

    }

    static function get_strat(){

        return DB::connection('dbbris')->table('tblstrat_plan_outcome')
       ->select('strat_outcome as label', DB::connection('dbbris')
       ->raw('(select count(ca_proj_id) from tblconcerned_areas where ca_strat_plan_outcome like strat_id) as total'))
       ->groupBy('strat_outcome','strat_id')
       ->orderBy('strat_id','asc')
       ->get()
       ->toArray();

    }

    static function get_dost(){

        return DB::connection('dbbris')->table('tbldost_agenda')
       ->select('dost_agenda_code as label', DB::connection('dbbris')
       ->raw('(select count(ca_proj_id) from tblconcerned_areas where ca_dost_agenda like dost_agenda_id) as total'))
       ->groupBy('dost_agenda_code','dost_agenda_id')
       ->orderBy('dost_agenda_id','asc')
       ->get()
       ->toArray();

    }

    static function get_pag(){

        return DB::connection('dbbris')->table('tblpriority_areas')
       ->select('pri_name as label', DB::connection('dbbris')
       ->raw('(select count(ca_proj_id) from tblconcerned_areas where ca_priority_areas like pri_id) as total'))
       ->groupBy('pri_name','pri_id')
       ->get()
       ->toArray();

    }

    static function get_prexc(){

        return DB::connection('dbbris')->table('tblprexc')
       ->select('prexc_classification as label', DB::connection('dbbris')
       ->raw('(select count(ca_proj_id) from tblconcerned_areas where ca_prexc like prexc_id) as total'))
       ->groupBy('prexc_classification','prexc_id')
       ->get()
       ->toArray();

    }

    static function get_hnrda(){
         return DB::connection('dbbris')->table('tblhnrda')
        ->select('hnrda_name as label', DB::connection('dbbris')
        ->raw('(select count(ca_proj_id) from tblconcerned_areas where ca_hnrda like hnrda_id) as total'))
        ->groupBy('hnrda_name','hnrda_id')
        ->get()
        ->toArray();
    }

    static function get_ps(){

        return DB::connection('dbbris')->table('tblproject_status')
        ->select('prs_name as label')
        ->get()
        ->toArray();
    }

    static function get_pc($data=''){
        
        $query = DB::connection('dbbris')->table('tblproject_classification')
        ->select('prc_name as label', DB::connection('dbbris')
        ->raw('(select count(ca_proj_id) from tblconcerned_areas where ca_type like prc_id) as total'));

        if($data) $query->where('prc_id','%' . $data . '%');

        $rows = $query->groupBy('prc_name','prc_id')
        ->get()
        ->toArray();

        return $rows;
    }

    static function pdp(){
        return DB::connection('dbbris')->table('tblpdp')
        ->get()
        ->toArray();
    }

    static function sdg(){
        return DB::connection('dbbris')->table('tblsdg')
        ->get()
        ->toArray();
    }

    static function strat(){
        return DB::connection('dbbris')->table('tblstrat_plan_outcome')
        ->get()
        ->toArray();
    }

    static function nibra_sub(){
        return DB::connection('dbbris')->table('tblnibra_sub_items')
        ->get()
        ->toArray();
    }

    static function nibra(){
        return DB::connection('dbbris')->table('tblnibra')
        ->get()
        ->toArray();
    }

    static function priority_areas(){
        return DB::connection('dbbris')->table('tblpriority_areas')
        ->get()
        ->toArray();
    }

    static function dost_agenda(){
        return DB::connection('dbbris')->table('tbldost_agenda')
        ->get()
        ->toArray();
    }    

    static function nsea(){
        return DB::connection('dbbris')->table('tblnsea')
        ->get()
        ->toArray();
    }

    static function hnrda(){
        return DB::connection('dbbris')->table('tblhnrda')
        ->get()
        ->toArray();
    }

    static function snt(){
        return DB::connection('dbbris')->table('tblsci_n_tech')
        ->get()
        ->toArray();
    }

    static function prexc(){
        return DB::connection('dbbris')->table('tblprexc')
        ->get()
        ->toArray();
    }

    static function proj_status(){
        return DB::connection('dbbris')->table('tblproject_status')
        ->get()
        ->toArray();
    }

    static function proj_type(){
        return DB::connection('dbbris')->table('tblproject_classification')
        ->get()
        ->toArray();
    }

    static function get_nibras(){ 
        
        return DB::connection('dbbris')->table('tblnibra')
        ->select('nibra_name','nibra_id', 
        DB::connection('dbbris')->raw('(select count(*) from tblconcerned_areas 
                                                        join tblproject_details 
                                                        on ca_proj_id = prd_id
                                                        where ca_nibra like nibra_id
                                                        and prd_proposal like 0) as total'))
        ->groupBy('nibra_name','nibra_id')
        ->orderBy('nibra_id')
        ->get()
        ->toArray();

        
    }

    static function get_dost_agendas(){ 
        
        return DB::connection('dbbris')->table('tbldost_agenda')
        ->select('dost_agenda_code','dost_agenda_id', 'dost_agenda_name',
         DB::connection('dbbris')->raw('(select count(*) from tblconcerned_areas 
                                                         join tblproject_details 
                                                         on ca_proj_id = prd_id
                                                         where ca_dost_agenda like dost_agenda_id
                                                         and prd_proposal like 0) as total'))
        ->groupBy('dost_agenda_code','dost_agenda_id', 'dost_agenda_name')
        ->orderBy('dost_agenda_id')
        ->get()
        ->toArray();
    }

    static function count_status($value){
        return DB::connection('dbbris')->table('tblproject_details')
        ->select('*')
        ->where('prd_status', $value)
        ->get()
        ->count();
    }

    static function get_project_status($status){
        $query = DB::connection('dbbris')->table('tblproject_details')
        ->select('prd_title', 'prd_duration', 'prd_date_created',
        DB::connection('dbbris')->raw('(select prs_name from tblproject_status where prs_id like prd_status) AS status'));
        
        $query->where('prd_proposal', 0);
        //if proposal = proponent, if project = project leader
        //add region, division, description(clicakble link), propoent, project leader
        if($status == 0){

            return $query->get();
        }else{
            $query->where('prd_status', $status);

            return $query->get();
        }
    }

    static function get_nibra_by_id($id){
        return DB::connection('dbbris')->table('tblproject_details')
        ->select('prd_title', 'prd_date_created', 'prd_proponent_id AS proponent',
        DB::connection('dbbris')->raw('(select prs_name from tblproject_status where prs_id like prd_status) AS status'))
        ->join('tblconcerned_areas', 'prd_id', '=', 'ca_proj_id')
        ->where('prd_proposal', 0)
        ->where('ca_nibra', $id)
        ->get();
    }

    static function get_dost_agenda_by_id($id){ 


        return DB::connection('dbbris')->table('tblconcerned_areas')
        ->select('prd_title', 'tblproject_details.date_created', 'prd_proponent_id AS proponent',
        DB::connection('dbbris')->raw('(select prs_name from tblproject_status where prs_id like prd_status) AS status'))
        ->join('tbldost_agenda', 'ca_dost_agenda', '=', 'dost_agenda_id')
        ->join('tblproject_details', 'prd_id', '=', 'ca_proj_id')
        ->where('ca_dost_agenda', $id)
        ->where('prd_proposal', 0)
        ->get();

    }

    static function get_program_status($status){

        return DB::connection('dbbris')->table('tblprograms')
        ->select('prg_title', 'date_created', 'prg_coordinator_id AS proponent', 'prg_duration',
        DB::connection('dbbris')->raw('(select prs_name from tblproject_status where prs_id like prg_program_status) AS status'))
        ->where('prg_proposal', 0)
        ->get();   
        
       
        $query = DB::connection('dbbris')->table('tblprograms')
        ->select('prg_title', 'date_created', 'prg_coordinator_id AS proponent', 'prg_duration',
        DB::connection('dbbris')->raw('(select prs_name from tblproject_status where prs_id like prg_program_status) AS status'));
        
        $query->where('prg_proposal', 0);

        if($status == 0){

            return $query->get();
            // return $query->last_query();
        }else{
            $query->where('prg_program_status', $status);

            // return $query->last_query();
            return $query->get();
        }
    }

    static function search_projects($keyword){

        return DB::connection('dbbris')->table('tblproject_details')
        ->select('prd_proposal AS prp', 'prd_title AS title', 'prd_date_created as date_created', 'prd_proponent_id AS proponent', 
        DB::connection('dbbris')->raw('(select prs_name from tblproject_status where prs_id like prd_status) AS status'))
        ->where('prd_title', 'LIKE', '%' . $keyword . '%')
        ->get();
       
    }
    
    static function search_programs($keyword){
        
        return DB::connection('dbbris')->table('tblprograms')
        ->select('prg_proposal AS prp', 'prg_title AS title', 'prg_coordinator_id AS proponent', 'date_created',
        DB::connection('dbbris')->raw('(select prs_name from tblproject_status where prs_id like prg_program_status) AS status'))
        ->where('prg_title', 'LIKE', '%' . $keyword . '%')
        ->get();

    }

    static function get_coorindator(){
        
        return DB::connection('dbskms')
        ->table('tblusers')
        ->select('usr_name', 'usr_id')
        ->get();
        
    }

    //todo
    static function search_proposals($keyword){

        return DB::connection('dbbris')->table('tblprograms')
        ->select('*',  
        DB::connection('dbbris')->raw('(select prs_name from tblproject_status where prs_id like prg_program_status) AS status'))
        ->where('prg_proposal', 0)
        ->where('prg_title', 'LIKE', '%' . $keyword . '%')
        ->get();

    }

    static function get_projects_per_stat(){
        
        return DB::connection('dbbris')->table('tblproject_status')
        ->select('prs_id', 'prs_name', 
        DB::connection('dbbris')
        ->raw('(SELECT COUNT(*) FROM tblproject_details WHERE prd_status LIKE prs_id and prd_proposal LIKE 0) AS total'))
        ->groupBy('prs_id')
        ->get();  
    }

    static function get_programs_per_stat(){
        
        return DB::connection('dbbris')->table('tblproject_status')
        ->select('prs_id', 'prs_name', 
        DB::connection('dbbris')
        ->raw('(SELECT COUNT(*) FROM tblprograms WHERE prg_program_status LIKE prs_id and prg_proposal LIKE 0) AS total'))
        ->groupBy('prs_id')
        ->get();  
    }

    static function get_csf_list(){
        
        return DB::connection('dbbris')->table('tblservice_feedbacks')
        ->join('tbl_csf_respondents','tbl_csf_respondents.fb_id','=','tblservice_feedbacks.fb_id')
        ->join('new_dbskms.tblservice_feedback_questions','new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id','=','tblservice_feedbacks.svc_fdbk_q_id')
        ->join('tblsex','sx_id','=','tbl_csf_respondents.sex')
        ->join('new_dbskms.tbldivisions','div_id','=','tbl_csf_respondents.sci_div', 'left')
        ->join('new_dbskms.tblregions','region_id','=','tbl_csf_respondents.region', 'left')
        ->select('tbl_csf_respondents.*', 'sx_sex', 'region_name', 'div_number')
        ->where('new_dbskms.tblservice_feedback_questions.svc_fdbk_q_code', 'CSF-V2022')
        ->groupBy('tbl_csf_respondents.fb_id')
        ->get();
    }

    static function get_csf_answers($user_id){
        return DB::connection('dbbris')->table('tblservice_feedbacks')
        ->join('tbl_csf_respondents','tbl_csf_respondents.fb_id','=','tblservice_feedbacks.fb_id')
        ->join('new_dbskms.tblservice_feedback_questions','new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id','=','tblservice_feedbacks.svc_fdbk_q_id')
        ->where('new_dbskms.tblservice_feedback_questions.svc_fdbk_q_code', 'CSF-V2022')
        ->where('tbl_csf_respondents.fb_id', $user_id)
        // ->groupBy('tbl_csf_respondents.fb_id')
        ->get();
    }

    // get csf desc
    static function get_csf_desc($id, $user){
        return DB::connection('dbbris')->table('tblservice_feedbacks')
        ->join('new_dbskms.tblservice_feedback_questions','new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id','=','tblservice_feedbacks.svc_fdbk_q_id')
        ->join('tbl_csf_respondents','tbl_csf_respondents.fb_id','=','tblservice_feedbacks.fb_id')
        ->select('tblservice_feedbacks.svc_fdbk_q_answer as rate')
        ->where('svc_fdbk_q_order', $id)
        ->where('svc_fdbk_q_code', 'CSF-V2022')
        ->where('tbl_csf_respondents.fb_id', $user)
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
                $result_array[] = array($row->svc_fdbk_rating_id => array($row->svc_fdbk_rating => Research::get_all_csf($row->svc_fdbk_rating_id)));
            }

            
            return $result_array;

        }else if($id == 2){ // pie sex 
            
            $where = ''; $group_by = ''; $join = ''; $sub_q = ''; $select = '';

            // $where .= ' AND pp_sex > 0 ';
            $where .= ' AND svc_fdbk_q_code LIKE "CSF-V2022" ';
            // $where .= ' AND svc_id LIKE 3 ';
            $group_by .= ' GROUP BY tbl_csf_respondents.fb_id ';
            $join .= ' JOIN dbbris.tblsex on sx_id = dbbris.tbl_csf_respondents.sex ';
            // $join .= ' JOIN dbbris.tbl_csf_respondents on tbl_csf_respondents.fb_id =  tblservice_feedbacks.tblservice_feedbacks.fb_id ';
                            
            $sub_q .= 'SELECT sx_id, count(tbl_csf_respondents.fb_id) as total, sx_sex AS label '. 
            'FROM dbbris.tbl_csf_respondents '. 
            'JOIN dbbris.tblservice_feedbacks ON tblservice_feedbacks.fb_id = tbl_csf_respondents.fb_id '.
            'JOIN new_dbskms.tblservice_feedback_questions ON new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id = dbbris.tblservice_feedbacks.svc_fdbk_q_id '. $join . 
            ' '. $where . $group_by;

            $select .= 'SELECT count(total) as total, label FROM '. 
            '( ' . $sub_q . ') as tmp GROUP BY sx_id ORDER BY sx_id desc';

            return DB::select($select);

        }else if($id == 3){ // bar region 
            
            $where = ''; $group_by = ''; $join = ''; $sub_q = ''; $select = '';

            // $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = tbl_csf_respondents.fb_id) '; 
            // $where .= ' AND emp_period_to = "Present" '; 
            $where .= ' AND svc_fdbk_q_code LIKE "CSF-V2022" ';
            // $where .= ' AND svc_id LIKE 3 ';
            $group_by .= ' GROUP BY tbl_csf_respondents.fb_id ';
 
            // $join .= 'LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = tbl_csf_respondents.fb_id ';
            $join .= ' JOIN new_dbskms.tblregions on region_id = region ';

            $sub_q .= 'SELECT count(tbl_csf_respondents.fb_id) as total, region_name as label, region_id '.
            'FROM dbbris.tbl_csf_respondents '. 
            'JOIN dbbris.tblservice_feedbacks ON tblservice_feedbacks.fb_id = tbl_csf_respondents.fb_id '.
            'JOIN new_dbskms.tblservice_feedback_questions ON new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id = dbbris.tblservice_feedbacks.svc_fdbk_q_id '. $join . 
            ' '. $where . $group_by;

            $select .= 'SELECT count(total) as total, label FROM '. 
            '( ' . $sub_q . ') as tmp GROUP BY region_id';

            return DB::select($select);

        }else if($id == 4){ // bar age 
            
            $where = ''; $group_by = ''; $join = ''; $sub_q = ''; $select = '';

            $where .= ' AND svc_fdbk_q_code LIKE "CSF-V2022" ';
            // $where .= ' AND svc_id LIKE 3 ';
            $group_by .= ' GROUP BY tbl_csf_respondents.fb_id ';

            $sub_q .= 'SELECT count(tbl_csf_respondents.fb_id) AS total, '.
            'CASE  '.
            'WHEN age BETWEEN 20 AND 31 then "1" '.
            'WHEN age BETWEEN 30 AND 41 then "2" '.
            'WHEN age BETWEEN 40 AND 51 then "3" '.
            'WHEN age BETWEEN 50 AND 61 then "4" '.
            'WHEN age BETWEEN 60 AND 71 then "5" '.
            'ELSE "6" END AS "range" '. 
            'FROM dbbris.tbl_csf_respondents '.
            'JOIN dbbris.tblservice_feedbacks ON tblservice_feedbacks.fb_id = tbl_csf_respondents.fb_id '.
            'JOIN new_dbskms.tblservice_feedback_questions ON new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id = dbbris.tblservice_feedbacks.svc_fdbk_q_id '. $join . 
            $where . $group_by;

            $select .= 'SELECT count(total) AS total, '. 
            '(SELECT age_range FROM new_dbskms.tblage_ranges WHERE age_id = tmp.range) AS label FROM '. 
            '( ' . $sub_q . ') AS tmp GROUP BY tmp.range';

            return DB::select($select);
        }else{ // bar affiliation 
            
            $where = ''; $group_by = ''; $join = ''; $sub_q = ''; $select = '';

            // $where .= ' AND emp_period_to = (SELECT MAX(emp_period_to) FROM new_dbskms.tblemployments WHERE emp_usr_id = tbl_csf_respondents.fb_id) '; 
            // $where .= ' AND emp_period_to = "Present" '; 
            $where .= ' AND svc_fdbk_q_code LIKE "CSF-V2022" ';
            $where .= ' AND svc_fdbk_q_order LIKE 1 ';
            // $where .= ' AND svc_id LIKE 3 ';
            $group_by .= ' GROUP BY tbl_csf_respondents.fb_id ';
 
            // $join .= 'LEFT JOIN new_dbskms.tblemployments ON emp_usr_id = tbl_csf_respondents.fb_id ';
 
            $join .= 'JOIN new_dbskms.tblaffiliation_type ON aff_type_id = svc_fdbk_q_answer ';
            // $join .= ' JOIN new_dbskms.tblregions on region_id = region ';

            $sub_q .= 'SELECT count(tbl_csf_respondents.fb_id) as total, aff_type as label, aff_type_id '.
            'FROM dbbris.tbl_csf_respondents '. 
            'JOIN dbbris.tblservice_feedbacks ON tblservice_feedbacks.fb_id = tbl_csf_respondents.fb_id '.
            'JOIN new_dbskms.tblservice_feedback_questions ON new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id = dbbris.tblservice_feedbacks.svc_fdbk_q_id '. $join . 
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
        
        // ->join('tbl_csf_respondents','tbl_csf_respondents.fb_id','=','tblservice_feedbacks.fb_id')
        $query = DB::select('SELECT svc_fdbk_q_id, svc_fdbk_q, '. 
        '(SELECT COUNT(*) FROM dbbris.tblservice_feedbacks '. 
        'JOIN dbbris.tbl_csf_respondents on tbl_csf_respondents.fb_id =  tblservice_feedbacks.fb_id '.
        'WHERE dbbris.tblservice_feedbacks.svc_fdbk_q_id '. 
        'LIKE new_dbskms.tblservice_feedback_questions.svc_fdbk_q_id '. 
        'AND svc_fdbk_q_answer LIKE '. $id .  
        ') AS total '. 
        'FROM new_dbskms.tblservice_feedback_questions '. 
        'WHERE svc_fdbk_q_choices LIKE "1,2,3,4,5" AND svc_fdbk_q_code LIKE "CSF-V2022" ');
        // ' AND svc_id LIKE 3'.
        return $query;

    }

}
