<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PreventiveMaintenance extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'pm_schedule_id', 'observation', 'recommendation', 'action_taken'
    ];

    public function pm_schedule()
    {
        return $this->belongsTo('App\PmSchedule');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'reported_by');
    }
}
