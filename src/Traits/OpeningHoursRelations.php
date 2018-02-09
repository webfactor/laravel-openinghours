<?php

namespace Webfactor\Laravel\OpeningHours\Traits;

use Webfactor\Laravel\OpeningHours\Models\DayOpenTimeRange;

trait OpeningHoursRelations
{
    public function dayOpenTimeRanges()
    {
        return $this->morphMany(DayOpenTimeRange::class, 'openable');
    }
}
