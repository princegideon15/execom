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
        ->distinct('clt_name')
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
        ->distinct()
        ->select('art_title','art_author', DB::connection('dbej')->raw('count(*) as total'))
        ->join('tblclients','art_id','=','clt_journal_downloaded_id')
        ->groupBy('clt_journal_downloaded_id')
        ->orderBy('art_title','asc')
        ->get();
    }

    static function get_most_clients(){
        return DB::connection('dbej')->table('tblclients')
        ->distinct()
        ->select('clt_name', 'clt_affiliation', 'clt_email')
        ->orderBy('clt_name','asc')
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
}
