<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Nrcpnet extends Model
{
    static function count_plantillas(){
        return DB::connection('dbhrmis')->table('tblemppersonal AS e_per')
        ->select('*')
        ->join('tblempposition AS e_pos', 'e_per.empNumber','=','e_pos.empNumber')
        ->join('tblappointment AS ap', 'e_pos.appointmentCode','=','ap.appointmentCode')
        ->where('ap.appointmentCode', 'P')
        ->count();
    }

    static function count_jos(){
        return DB::connection('dbhrmis')->table('tblemppersonal AS e_per')
        ->select('*')
        ->join('tblempposition AS e_pos', 'e_per.empNumber','=','e_pos.empNumber')
        ->join('tblappointment AS ap', 'e_pos.appointmentCode','=','ap.appointmentCode')
        ->where('ap.appointmentCode', 'JO')
        ->count();

        
    }

    static function count_contractuals(){
        return DB::connection('dbhrmis')->table('tblemppersonal AS e_per')
        ->select('*')
        ->join('tblempposition AS e_pos', 'e_per.empNumber','=','e_pos.empNumber')
        ->join('tblappointment AS ap', 'e_pos.appointmentCode','=','ap.appointmentCode')
        ->where('ap.appointmentCode', 'CONT')
        ->count();
    }

    static function get_plantillas($keyword){

        $query = DB::connection('dbhrmis')->table('tblemppersonal AS e_per')
        ->select('e_per.surname AS plant_surname', 'e_per.firstname AS plant_firstname' //, 'plnt.plantillaGroupName AS plant_group'
                 , 'e_per.middlename as plant_middlename', 'ap.appointmentDesc AS plant_appointment')
        ->join('tblempposition AS e_pos', 'e_per.empNumber','=','e_pos.empNumber')
        ->join('tblappointment AS ap', 'e_pos.appointmentCode','=','ap.appointmentCode')
        ->where('ap.appointmentCode', 'P');

        if($keyword != null){
            $query->where('e_per.surname','LIKE','%'. $keyword .'%');
        }

        return $query->get();
    }

    static function get_contractuals($keyword){

        $query = DB::connection('dbhrmis')->table('tblemppersonal AS e_per')
        ->select('e_per.surname AS plant_surname', 'e_per.firstname AS plant_firstname' //, 'plnt.plantillaGroupName AS plant_group'
        , 'e_per.middlename as plant_middlename', 'ap.appointmentDesc AS plant_appointment')
        ->join('tblempposition AS e_pos', 'e_per.empNumber','=','e_pos.empNumber')
        ->join('tblappointment AS ap', 'e_pos.appointmentCode','=','ap.appointmentCode')
        ->where('ap.appointmentCode', 'CONT');

        if($keyword != null){
            $query->where('e_per.surname','LIKE','%'. $keyword .'%');
        }

        return $query->get();
    }

    static function get_jos($keyword){

        $query = DB::connection('dbhrmis')->table('tblemppersonal AS e_per')
        ->select('e_per.surname AS plant_surname', 'e_per.firstname AS plant_firstname' //, 'plnt.plantillaGroupName AS plant_group'
        , 'e_per.middlename as plant_middlename', 'ap.appointmentDesc AS plant_appointment')
        ->join('tblempposition AS e_pos', 'e_per.empNumber','=','e_pos.empNumber')
        ->join('tblappointment AS ap', 'e_pos.appointmentCode','=','ap.appointmentCode')
        // ->join('tblplantillagroup AS plnt', 'e_pos.plantillaGroupCode', '=', 'plnt.plantillaGroupCode')
        ->where('ap.appointmentCode', 'JO');

        if($keyword != null){
            $query->where('e_per.surname','LIKE','%'. $keyword .'%');
        }

        return $query->get();
    }

    static function search($keyword, $code, $config){

        $query = DB::connection('dbhrmis')->table('tblemppersonal AS e_per')
        ->select('e_per.surname AS plant_surname', 'e_per.firstname AS plant_firstname'
        , 'e_per.middlename as plant_middlename', 'ap.appointmentDesc AS plant_appointment', 'plnt.plantillaGroupName AS plant_group')
        ->join('tblempposition AS e_pos', 'e_per.empNumber','=','e_pos.empNumber')
        ->join('tblappointment AS ap', 'e_pos.appointmentCode','=','ap.appointmentCode')
        ->join('tblplantillagroup AS plnt', 'e_pos.plantillaGroupCode', '=', 'plnt.plantillaGroupCode');   

        if($config == 'name'){
            $query->where('e_per.surname', 'LIKE', '%'. $keyword . '%');
            $query->orWhere('e_per.firstname', 'LIKE', '%'. $keyword . '%');
            $query->orWhere('e_per.middlename', 'LIKE', '%'. $keyword . '%');
        }else if($config == 'plantillaGroupName'){
            $query->where('plnt.plantillaGroupName', 'LIKE', '%'. $keyword .'%');
        }else{
            if($code != null){
                if($keyword != null){
                    $query->where('e_per.surname', 'LIKE', '%'. $keyword . '%');
                    $query->orWhere('e_per.firstname', 'LIKE', '%'. $keyword . '%');
                    $query->orWhere('e_per.middlename', 'LIKE', '%'. $keyword . '%');
                } 
                $query->where('e_pos.plantillaGroupCode', $code);
            }
        }

        return $query->get();

    }

    static function get_plantilla_group(){
        
        return DB::connection('dbhrmis')->table('tblplantillagroup')
        ->select('plantillaGroupCode', 'plantillaGroupName')
        ->get();
    }
}
