<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanningLb extends Model
{
    protected $table = 'planning_lbs';

    protected $fillable = [
        'location',
        'process_date',
        'total_plan_chicken',
        'total_plan_truck',
    ];

    protected $casts = [
        'process_date' => 'date',
        'total_plan_chicken' => 'integer',
        'total_plan_truck' => 'integer',
    ];
}
