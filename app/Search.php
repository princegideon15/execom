<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Search extends Model
{
    static function get_regions(){
        return DB::connection('dbskms')->table('tblregions')
        ->select('*')
        ->orderBy('region_id')
        ->get();
    }

    static function get_provinces($id){
        return DB::connection('dbskms')->table('tblprovinces')
        ->select('*')
        ->where('province_region_id', $id)
        ->orderBy('province_id')
        ->get();
    }

    static function get_cities($id){
        return DB::connection('dbskms')->table('tblcities')
        ->select('*')
        ->where('city_province_id', $id)
        ->orderBy('city_id')
        ->get();
    }

    static function get_divisions(){
        return DB::connection('dbskms')->table('tbldivisions')
        ->select('*')
        ->get();
    }

}
