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
        ->select('prd_title', 'prd_duration', 
        DB::connection('dbbris')->raw('(select prs_name from tblproject_status where prs_id like prd_status) AS status'));
        
        $query->where('prd_proposal', 0);

        if($status == 0){

            return $query->get();
        }else{
            $query->where('prd_status', $status);

            return $query->get();
        }
    }

    static function get_nibra_by_id($id){
        return DB::connection('dbbris')->table('tblproject_details')
        ->select('prd_title', 'tblproject_details.prd_date_created', 'prd_proponent_id AS proponent',
        DB::connection('dbbris')->raw('(select prs_name from tblproject_status where prs_id like prd_status) AS status'))
        ->join('tblconcerned_areas', 'prd_id', '=', 'ca_proj_id')
        ->where('prd_proposal', 0)
        ->where('ca_nibra', $id)
        ->get();
    }

    static function get_dost_agenda_by_id($id){ 


        return DB::connection('dbbris')->table('tblconcerned_areas')
        ->select('prd_title', 'tblproject_details.prd_date_created', 'prd_proponent_id AS proponent',
        DB::connection('dbbris')->raw('(select prs_name from tblproject_status where prs_id like prd_status) AS status'))
        ->join('tbldost_agenda', 'ca_dost_agenda', '=', 'dost_agenda_id')
        ->join('tblproject_details', 'prd_id', '=', 'ca_proj_id')
        ->where('ca_dost_agenda', $id)
        ->where('prd_proposal', 0)
        ->get();

    }

    static function get_program_status($status){

        // return DB::connection('dbbris')->table('tblprograms')
        // ->select('prg_title', 'date_created', 'prg_coordinator_id AS proponent', 'prg_duration',
        // DB::connection('dbbris')->raw('(select prs_name from tblproject_status where prs_id like prg_program_status) AS status'))
        // ->where('prg_proposal', 0)
        // ->get();   
        
       
        $query = DB::connection('dbbris')->table('tblprograms')
        ->select('prg_title', 'date_created', 'prg_coordinator_id AS proponent', 'prg_duration',
        DB::connection('dbbris')->raw('(select prs_name from tblproject_status where prs_id like prg_program_status) AS status'));
        
        $query->where('prg_proposal', 0);

        if($status == 0){

            return $query->get();
        }else{
            $query->where('prg_program_status', $status);

            return $query->get();
        }
    }

    static function search_projects($keyword){

        return DB::connection('dbbris')->table('tblproject_details')
        ->select('prd_proposal AS prp', 'prd_title AS title', 'prd_date_created', 'prd_proponent_id AS proponent', 
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
}
