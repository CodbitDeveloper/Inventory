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
        'assigned_to', 'status'
    ];

    public function work_order() 
    {
        return $this->belongsTo('App\WorkOrder');
    }

    public function engineer()
    {
        return $this->belongsTo('App\Admin', 'assigned_to');
    }
}
