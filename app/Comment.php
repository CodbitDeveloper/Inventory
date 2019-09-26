<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;
    protected $appends = ['user'];

    protected $fillable = [
        'comment', 'work_order_id', 'user_id', 'type'
    ];

    public function hospital_user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function admin()
    {
        return $this->belongsTo('App\Admin', 'user_id');
    }


    public function work_order()
    {
        return $this->belongsTo('App\WorkOrder');
    }

    public function getUserAttribute()
    {
        return $this->hospital_user != null ? $this->hospital_user : $this->admin;
    }
}
