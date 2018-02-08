<?php

namespace Webfactor\Laravel\OpeningHours\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Webfactor\Laravel\OpeningHours\Entities\OpeningHours;
use Webfactor\Laravel\OpeningHours\Models\DayOpenTimeRange;

class OpeningHourAttributeTest extends TestCase
{
    use RefreshDatabase;

    /** @var  TestModel */
    protected $model;

    protected $openTimeRanges = [
        ['day' => 'monday', 'start' => '08:00', 'end' => '12:00'],
        ['day' => 'monday', 'start' => '14:00', 'end' => '16:00'],
        ['day' => 'tuesday', 'start' => '08:00', 'end' => '12:00'],
        ['day' => 'tuesday', 'start' => '14:00', 'end' => '16:00'],
        ['day' => 'wednesday', 'start' => '08:00', 'end' => '12:00'],
        ['day' => 'wednesday', 'start' => '14:00', 'end' => '16:00'],
        ['day' => 'thursday', 'start' => '08:00', 'end' => '12:00'],
        ['day' => 'thursday', 'start' => '14:00', 'end' => '16:00'],
        ['day' => 'friday', 'start' => '08:00', 'end' => '12:00'],
        ['day' => 'friday', 'start' => '14:00', 'end' => '16:00'],
    ];

    protected $openHours = [
        'saturday' => ['08:00-12:00', '14:00-16:00'],
        'sunday'   => ['08:00-12:00', '14:00-16:00'],
    ];

    public function setUp()
    {
        parent::setUp();

        $this->model = TestModel::create();

        foreach ($this->openTimeRanges as $hour) {
            $this->model->dayOpenTimeRanges()->create($hour);
        }
    }

    /** @test */
    public function it_can_return_the_correct_opening_hours()
    {
        /** @var OpeningHours $openHours */
        $openHours = $this->model->opening_hours;

        $this->assertTrue($openHours->isOpenOn('monday'));
        $this->assertTrue($openHours->isClosedOn('saturday'));
    }

    /** @test */
    public function it_can_clear_opening_hours()
    {
        $this->model->opening_hours = null;

        $this->assertTrue($this->model->opening_hours->isClosedOn('monday'));
        $this->assertTrue($this->model->opening_hours->isClosedOn('saturday'));

        $this->assertEquals(0, DayOpenTimeRange::all()->count());
    }

    /** @test */
    public function it_can_set_opening_hours()
    {
        $hours = OpeningHours::create($this->openHours);

        $this->model->opening_hours = $hours;

        $this->assertTrue($this->model->opening_hours->isOpenOn('sunday'));
        $this->assertTrue($this->model->opening_hours->isClosedOn('monday'));
    }
}