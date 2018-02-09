<?php

namespace Webfactor\Laravel\OpeningHours\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Webfactor\Laravel\OpeningHours\Entities\Day;
use Webfactor\Laravel\OpeningHours\Entities\OpeningHours;
use Webfactor\Laravel\OpeningHours\Entities\Time;
use Webfactor\Laravel\OpeningHours\Models\DayOpenTimeRange;

class OpeningHoursTest extends TestCase
{
    use RefreshDatabase;

    public function up()
    {
        parent::setUp();
    }

    public function getOpeningHours()
    {
        return OpeningHours::create([
            'monday' => ['09:00-18:00'],
            'tuesday' => ['09:00-18:00'],
            'wednesday' => ['09:00-18:00'],
            'thursday' => ['09:00-18:00'],
            'friday' => ['09:00-18:00'],
            'saturday' => [],
            'sunday' => [],
        ]);
    }

    /** @test */
    public function it_can_create_opening_hour()
    {
        $hour = DayOpenTimeRange::create([
            'openable_id' => 1,
            'openable_type' => 'some',
            'day' => Day::FRIDAY,
            'start' => Time::fromString('08:00'),
            'end' => Time::fromString('18:00'),
        ]);

        $this->assertEquals(Day::FRIDAY, $hour->day);
        $this->assertEquals('08:00', $hour->start);
        $this->assertEquals('18:00', $hour->end);
    }
}
