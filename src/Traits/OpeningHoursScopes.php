<?php

namespace Webfactor\Laravel\OpeningHours\Traits;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Webfactor\Laravel\OpeningHours\Entities\Day;
use Webfactor\Laravel\OpeningHours\Entities\Time;
use Webfactor\Laravel\OpeningHours\Exceptions\InvalidDayName;
use Webfactor\Laravel\OpeningHours\Exceptions\InvalidTimeString;

trait OpeningHoursScopes
{
    protected $openingHoursRelationName = 'dayOpenTimeRanges';

    public function scopeWithOpeningHours(Builder $query)
    {
        return $query->with($this->openingHoursRelationName);
    }

    /**
     * Get only open models.
     * If no time and day are provided current time and day are used.
     * If only time is provided current day is used.
     *
     * @param Builder $query
     * @param Time|string|null $time defaults to current time
     * @param string|null $day defaults to current day
     * @return Builder|static
     * @throws InvalidDayName
     * @throws InvalidTimeString
     */
    public function scopeOpen(Builder $query, $time = null, string $day = null)
    {
        $now = Carbon::now();
        if ($time == null) {
            $time = Time::fromDateTime($now);
        } else if (is_string($time)) {
            $time = Time::fromString($time);
        }

        if ($day == null) {
            $day = Day::onDateTime($now);
        } else {
            if (! Day::isValid($day)) {
                throw new InvalidDayName();
            }
        }

        return $query->whereHas($this->openingHoursRelationName, function(Builder $subquery) use ($day, $time) {
            $subquery
                ->where('day', $day)
                ->where('start', '<=', $time)
                ->where('end', '>=', $time);
        });
    }

    /**
     * Get only closed models.
     * If no time and day are provided current time and day are used.
     * If only time is provided current day is used.
     *
     * @param Builder $query
     * @param Time|string|null $time defaults to current time
     * @param string|null $day defaults to current day
     * @return Builder|static
     * @throws InvalidDayName
     * @throws InvalidTimeString
     */
    public function scopeClosed(Builder $query, $time = null, string $day = null)
    {
        $now = Carbon::now();
        if ($time == null) {
            $time = Time::fromDateTime($now);
        } else if (is_string($time)) {
            $time = Time::fromString($time);
        }

        if ($day == null) {
            $day = Day::onDateTime($now);
        } else {
            if (! Day::isValid($day)) {
                throw new InvalidDayName();
            }
        }

        return $query->whereDoesntHave($this->openingHoursRelationName, function(Builder $subquery) use ($day, $time) {
            $subquery
                ->where('day', $day)
                ->where('start', '<=', $time)
                ->where('end', '>=', $time);
        });
    }

    /**
     * Get all models open at the specified day at random time
     *
     * @param Builder $query
     * @param string $day
     * @return Builder|static
     * @throws InvalidDayName
     */
    public function scopeOpenOn(Builder $query, string $day)
    {
        if (! Day::isValid($day)) {
            throw new InvalidDayName();
        }

        return $query->whereHas($this->openingHoursRelationName, function (Builder $subquery) use ($day) {
            $subquery->where('day', $day);
        });
    }

    /**
     * Get all models closed at the specified day
     *
     * @param Builder $query
     * @param string $day
     * @return Builder|static
     * @throws InvalidDayName
     */
    public function scopeClosedOn(Builder $query, string $day)
    {
        if (! Day::isValid($day)) {
            throw new InvalidDayName();
        }

        return $query->whereDoesntHave($this->openingHoursRelationName, function (Builder $subquery) use ($day) {
            $subquery->where('day', $day);
        });
    }

    /**
     * Get all models open at the specified DateTime
     *
     * @param Builder $query
     * @param DateTimeInterface $date
     * @return mixed
     */
    public function scopeOpenAt(Builder $query, DateTimeInterface $date)
    {
        return $query->open(Time::fromDateTime($date), Day::onDateTime($date));
    }

    /**
     * Get all models closed at the specified DateTime
     *
     * @param Builder $query
     * @param DateTimeInterface $date
     * @return mixed
     */
    public function scopeClosedAt(Builder $query, DateTimeInterface $date)
    {
        return $query->closed(Time::fromDateTime($date), Day::onDateTime($date));
    }
}