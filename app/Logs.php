<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
    public $timestamps = true;

    protected $table = 'tbllogs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'log_user_id', 'log_email', 'log_description', 'log_query', 'log_url', 'log_controller', 'log_model', 'id', 'log_ip_address', 'log_user_agent', 'log_browser'
    ];

    

    
}
