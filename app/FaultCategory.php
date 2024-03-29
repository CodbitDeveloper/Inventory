<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FaultCategory extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';
    
    public $incrementing = false;

    protected $fillable = ['name', 'hospital_id'];

    public function work_orders()
    {
        return $this->hasMany('App\WorkOrder');
    }
}
