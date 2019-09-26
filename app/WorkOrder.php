<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkOrder extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';

    public $incrementing = false;
    
    protected $fillable = [
        'admin_id'
    ];

    public function pending()
    {
        $this->status = 5;
    }

    public function open()
    {
        $this->status = 4;
    }

    public function on_hold()
    {
        $this->status = 3;
    }

    public function in_progress()
    {
        $this->status = 2;
    }

    public function complete()
    {
        $this->status = 1;
    }
    
    public function finish()
    {
        $this->is_complete = 1;
    }

    public function priority()
    {
        return $this->belongsTo('App\Priority');
    }

    public function hospital()
    {
        return $this->belongsTo('App\Hospital');
    }

    public function fault_category()
    {
        return $this->belongsTo('App\FaultCategory');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'assigned_to');
    }

    public function admin()
    {
        return $this->belongsTo('App\Admin');
    }

    public function department()
    {
        return $this->belongsTo('App\Department');
    }

    public function unit()
    {
        return $this->belongsTo('App\Unit');
    }

    public function service_vendor()
    {
        return $this->belongsTo('App\Service_Vendor');
    }

    public function purchase_orders()
    {
        return $this->hasMany('App\PurchaseOrder');
    }

    public function users()
    {
        return $this->belongsToMany('App\User', 'teams', 'work_order_id', 'additional_workers')->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    public function request()
    {
        return $this->belongsTo('App\Requests');
    }

    public function asset()
    {
        return $this->belongsTo('App\Asset');
    }

    public function parts()
    {
        return $this->belongsToMany('App\Part', 'part_work_orders', 'work_order_id', 'part_id')->withPivot('quantity')->withTimestamps();
    }

    public function hospital_messages(){
        return $this->belongsToMany('App\User', 'work_order_messages', 'work_order_id', 'user_id')->withPivot('action_taken')->withTimestamps();
    }

    public function admin_messages()
    {
        return $this->belongsToMany('App\Admin', 'work_order_messages', 'work_order_id', 'user_id')->withPivot('action_taken')->withTimestamps();
    }
    
    public function getUserMessagesAttribute()
    {
        return $this->hospital_user_messages != null ?  $this->hospital_user_messages : $this->admin_user_messages;
    }
}
