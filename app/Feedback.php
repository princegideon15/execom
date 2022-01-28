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

}
