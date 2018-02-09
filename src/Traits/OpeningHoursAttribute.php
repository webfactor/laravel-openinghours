<?php

namespace Webfactor\Laravel\OpeningHours\Traits;

use Webfactor\Laravel\OpeningHours\Entities\OpeningHours;
use Webfactor\Laravel\OpeningHours\Entities\OpeningHoursForDay;
use Webfactor\Laravel\OpeningHours\Entities\TimeRange;
use Webfactor\Laravel\OpeningHours\Exceptions\Exception;
use Webfactor\Laravel\OpeningHours\Models\DayOpenTimeRange;

trait OpeningHoursAttribute
{
    public function getOpeningHoursAttribute()
    {
        if (!key_exists('opening_hours', $this->attributes) || !$this->attributes['opening_hours']) {
            $hours = $this->dayOpenTimeRanges
                ->groupBy('day')->map(function ($day) {
                    return $day->map(function (DayOpenTimeRange $range) {
                        return $range->start.'-'.$range->end;
                    });
                });

            $this->attributes['opening_hours'] = OpeningHours::create($hours->toArray());
        }

        return $this->attributes['opening_hours'];

    }

    public function setOpeningHoursAttribute($data)
    {
        // clear previous open times
        $this->attributes['opening_hours'] = null;
        $this->dayOpenTimeRanges()->delete();

        if ($data == null) {
            return;
        }

        if ($data instanceof OpeningHours) {
            $this->applyOpeningHours($data);
            return;
        }

        if (is_array($data) || is_object($data)) {
            $this->applyOpeningHours(OpeningHours::create((array) $data));
            return;
        }

        throw new Exception("Invalid argument `{$data}` applied to opening hours attribute.");
    }

    private function applyOpeningHours(OpeningHours $openingHours)
    {
        $entries = $this->parseOpeningHoursForDB($openingHours);
        $this->dayOpenTimeRanges()->saveMany($entries);
        $this->attributes['opening_hours'] = $openingHours;
    }

    private function parseOpeningHoursForDB(OpeningHours $openingHours)
    {
        $ranges = $openingHours->flatMap(function (OpeningHoursForDay $openingHoursForDay, string $day) {
            return $openingHoursForDay->map(function (TimeRange $timeRange) use ($day) {
                return [
                    'day'   => $day,
                    'start' => $timeRange->start(),
                    'end'   => $timeRange->end()
                ];
            });
        });

        return collect($ranges)->map(function ($range) {
            return new DayOpenTimeRange($range);
        });
    }
}