<?php

namespace Webfactor\Laravel\OpeningHours\Models;

use Illuminate\Database\Eloquent\Model;

class DayOpenTimeRange extends Model {

    protected $table = 'opening_hours';

    protected $guarded = ['id'];
}