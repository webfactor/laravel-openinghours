<?php

namespace Webfactor\Laravel\OpeningHours\Traits;

use Webfactor\Laravel\OpeningHours\Entities\OpeningHours;
use Webfactor\Laravel\OpeningHours\Entities\OpeningHoursForDay;
use Webfactor\Laravel\OpeningHours\Entities\TimeRange;
use Webfactor\Laravel\OpeningHours\Models\DayOpenTimeRange;


/** @property OpeningHours opening_hours */
trait HasOpeningHours
{
    use OpeningHoursRelations;


    public function getOpeningHoursAttribute()
    {
        $hours = $this->dayOpenTimeRanges
            ->groupBy('day')->map(function($day) {
                return $day->map(function (DayOpenTimeRange $range) {
                    return $range->start.'-'.$range->end;
                });
            });

        return OpeningHours::create($hours->toArray());
    }

    public function setOpeningHoursAttribute($data)
    {
        // clear previous open times
        $this->dayOpenTimeRanges()->delete();

        if ($data == null) {
            return;
        }

        $rangesArray = collect($data->flatMap(function (OpeningHoursForDay $openingHoursForDay, string $day) {
            return $openingHoursForDay->map(function (TimeRange $timeRange) use ($day) {
                return [
                    'day' => $day,
                    'start' => $timeRange->start(),
                    'end' => $timeRange->end()
                ];
            });
        }))->map(function ($range) {
            return new DayOpenTimeRange($range);
        });

        $this->dayOpenTimeRanges()->saveMany($rangesArray);
    }
}