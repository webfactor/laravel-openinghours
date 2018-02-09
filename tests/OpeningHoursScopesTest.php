<?php

namespace Webfactor\Laravel\OpeningHours\Tests;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Webfactor\Laravel\OpeningHours\Entities\Day;
use Webfactor\Laravel\OpeningHours\Exceptions\Exception;

class OpeningHoursScopesTest extends TestCase
{
    use RefreshDatabase;

    protected $openWeek = [
        'monday'    => ['09:00-12:00', '13:00-18:00'],
        'tuesday'   => ['09:00-12:00', '13:00-18:00'],
        'wednesday' => ['09:00-12:00'],
        'thursday'  => ['09:00-12:00', '13:00-18:00'],
        'friday'    => ['09:00-12:00', '13:00-20:00'],
        'saturday'  => ['09:00-12:00']
    ];

    protected $openWeekend = [
        'saturday' => ['08:00-12:00', '13:00-16:00'],
        'sunday'   => ['08:00-12:00', '13:00-16:00'],
    ];

    protected $weekCount = 5;
    protected $weekendCount = 2;
    protected $openSaturdayMorning = 7;


    public function setUp()
    {
        parent::setUp();

        // set now
        $saturday = Carbon::create(2018, 1, 6, 10, 00);
        Carbon::setTestNow($saturday);

        $this->setupData();
    }

    public function setupData()
    {
        for ($i = 0; $i < $this->weekCount; $i++) {
            TestModel::create()->opening_hours = $this->openWeek;
        }

        for ($i = 0; $i < $this->weekendCount; $i++) {
            TestModel::create()->opening_hours = $this->openWeekend;
        }

        TestModel::create();
    }

    /** @test */
    public function it_can_retrieve_only_open_models()
    {
        $models = TestModel::open()->get();
        $this->assertEquals($this->openSaturdayMorning, $models->count());
    }

    /** @test */
    public function it_can_retrieve_open_models_for_time_and_day()
    {
        $models = TestModel::open('08:00', 'saturday')->get();
        $this->assertEquals($this->weekendCount, $models->count());
    }

    /** @test */
    public function it_can_retrieve_open_models_only_with_time()
    {
        $models = TestModel::open('08:00')->get();

        $this->assertEquals($this->weekendCount, $models->count());
    }

    /** @test */
    public function it_can_retrieve_open_models_for_day()
    {
        $models = TestModel::openOn(Day::SATURDAY)->get();
        $this->assertEquals($this->openSaturdayMorning, $models->count());
    }

    /** @test */
    public function it_can_retrieve_open_models_for_date()
    {
        $models = TestModel::openAt(Carbon::parse('2018-01-06 14:00'));
        $this->assertEquals($this->weekendCount, $models->count());
    }

    /** @test */
    public function it_can_retrieve_only_closed_models()
    {
        $models = TestModel::closed()->get();
        $this->assertEquals(1, $models->count());
    }

    /** @test */
    public function it_can_retrieve_closed_models_for_day_and_time()
    {
        $models = TestModel::closed('12:00', 'monday')->get();
        $this->assertEquals($this->weekendCount + 1, $models->count());
    }

    /** @test */
    public function it_can_retrieve_closed_models_only_with_time()
    {
        $models = TestModel::closed('08:00')->get();

        $this->assertEquals($this->weekCount + 1, $models->count());
    }

    /** @test */
    public function it_can_retrieve_closed_models_for_day()
    {
        $models = TestModel::closedOn(Day::SUNDAY)->get();
        $this->assertEquals($this->weekCount + 1, $models->count());
    }

    /** @test */
    public function it_can_retrieve_closed_models_for_date()
    {
        $models = TestModel::closedAt(Carbon::parse('2018-01-06 14:00'));
        $this->assertEquals($this->weekCount + 1, $models->count());
    }
}