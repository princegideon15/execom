<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Auth;

class Execom extends Model
{
    public $timestamps = true;

    protected $table = 'users';

    // protected $fillable = ['email'];

    static function get_users(){
        return DB::table('users')->where('id','!=', Auth::id())->get();
    }

    static function add_user($id){
        $skms = DB::connection('dbskms')->table('tblusers')
        ->select('pp_first_name', 'pp_last_name', 'usr_name', 'usr_id', 'usr_password')
        ->join('tblpersonal_profiles','usr_id','=','pp_usr_id')
        ->where('usr_id', $id)
        ->get();

        $arr = array();

        foreach($skms as $row){

            $arr['name'] = "{$row->pp_first_name} {$row->pp_last_name}";
            $arr['email'] = $row->usr_name;
            $arr['user_id'] = $id;
            $arr['role'] = 2;
            $arr['password'] = $row->usr_password;
            $arr['status'] = 1;

        }

        return DB::table('users')->insert($arr);
        
    }

    static function remove_user($id){

        return DB::table('users')
        ->where('user_id', $id)
        // ->where('role', '!=', '1')
        ->delete();
    }
    
}
