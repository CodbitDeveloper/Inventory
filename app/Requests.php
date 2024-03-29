<?php

namespace App;
use App\WorkOrder;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Requests extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $fillable = [
        'title', 'description', 'priority_id', 'image', 'department_id', 'unit_id',
        'asset_id', 'requested_by', 'fileName', 'requester_name', 'requester_number',
        'requester_email', 'reason', 'response',
    ];

    public function approve()
    {
        $this->status = 1;
    }

    public function decline()
    {
        $this->status = 0;
    }

    public function priority()
    {
       return $this->belongsTo('App\Priority');
    }

    public function department()
    {
       return $this->belongsTo('App\Department');
    }

    public function unit()
    {
       return $this->belongsTo('App\Unit');
    }

    public function asset()
    {
       return $this->belongsTo('App\Asset');
    }

    public function user()
    {
       return $this->belongsTo('App\User', 'requested_by');
    }

    public function work_orders()
    {
        return $this->hasMany('App\WorkOrder');
    }

    public function toWorkOrder(){
        $work_order = new WorkOrder();

        $work_order->request_id = $this->id;
        $work_order->title = $this->title;
        $work_order->id = md5($work_order->title.time());
        $work_order->hospital_id = $this->hospital_id;
        $work_order->asset_id = $this->asset_id;
        $work_order->priority_id = $this->priority_id;
        $work_order->department_id = $this->department_id;
        $work_order->unit_id = $this->unit_id;

        return $work_order;
    }
}
