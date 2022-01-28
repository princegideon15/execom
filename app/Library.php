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
}
