<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestEngineer extends Model
{
    use SoftDeletes; 

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $fillable = [
        ''
    ];

    public function work_order() 
    {
        return $this->belongsTo('App\WorkOrder');
    }
}
