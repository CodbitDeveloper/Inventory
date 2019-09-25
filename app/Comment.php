<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'comment', 'work_order_id', 'user_id', 'type'
    ];

    public function user()
    {
        if($this->type == 'user') {
            return $this->belongsTo('App\User');
        } else {
            return $this->belongsTo('App\Admin', 'user_id');
        }
    }

    public function work_order()
    {
        return $this->belongsTo('App\WorkOrder');
    }
}
