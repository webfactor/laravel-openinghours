<?php

namespace Webfactor\Laravel\OpeningHours\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Webfactor\Laravel\OpeningHours\Entities\OpeningHours;
use Webfactor\Laravel\OpeningHours\Exceptions\Exception;
use Webfactor\Laravel\OpeningHours\Models\DayOpenTimeRange;

class OpeningHoursAttributeTest extends TestCase
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
    public function it_can_get_model_opening_hours_attribute()
    {
        $this->assertInstanceOf(OpeningHours::class, $this->model->opening_hours);
    }

    /** @test */
    public function it_can_return_the_correct_opening_hours()
    {
        $this->assertTrue($this->model->opening_hours->isOpenOn('monday'));
        $this->assertTrue($this->model->opening_hours->isOpenOn('tuesday'));
        $this->assertTrue($this->model->opening_hours->isOpenOn('wednesday'));
        $this->assertTrue($this->model->opening_hours->isOpenOn('thursday'));
        $this->assertTrue($this->model->opening_hours->isOpenOn('friday'));
        $this->assertTrue($this->model->opening_hours->isClosedOn('saturday'));
        $this->assertTrue($this->model->opening_hours->isClosedOn('sunday'));
    }

    /** @test */
    public function it_can_clear_opening_hours()
    {
        $this->model->opening_hours = null;

        $this->assertTrue($this->model->opening_hours->isClosedOn('monday'));
        $this->assertTrue($this->model->opening_hours->isClosedOn('tuesday'));
        $this->assertTrue($this->model->opening_hours->isClosedOn('wednesday'));
        $this->assertTrue($this->model->opening_hours->isClosedOn('thursday'));
        $this->assertTrue($this->model->opening_hours->isClosedOn('friday'));
        $this->assertTrue($this->model->opening_hours->isClosedOn('saturday'));
        $this->assertTrue($this->model->opening_hours->isClosedOn('sunday'));

        $this->assertEquals(0, DayOpenTimeRange::all()->count());
    }

    /** @test */
    public function it_can_set_opening_hours()
    {
        $hours = OpeningHours::create($this->openHours);

        $this->model->opening_hours = $hours;

        $this->assertTrue($this->model->opening_hours->isClosedOn('monday'));
        $this->assertTrue($this->model->opening_hours->isClosedOn('tuesday'));
        $this->assertTrue($this->model->opening_hours->isClosedOn('wednesday'));
        $this->assertTrue($this->model->opening_hours->isClosedOn('thursday'));
        $this->assertTrue($this->model->opening_hours->isClosedOn('friday'));
        $this->assertTrue($this->model->opening_hours->isOpenOn('saturday'));
        $this->assertTrue($this->model->opening_hours->isOpenOn('sunday'));
    }

    /** @test */
    public function it_can_set_opening_hours_from_array()
    {
        $this->model->opening_hours = $this->openHours;

        $this->assertTrue($this->model->opening_hours->isClosedOn('monday'));
        $this->assertTrue($this->model->opening_hours->isClosedOn('tuesday'));
        $this->assertTrue($this->model->opening_hours->isClosedOn('wednesday'));
        $this->assertTrue($this->model->opening_hours->isClosedOn('thursday'));
        $this->assertTrue($this->model->opening_hours->isClosedOn('friday'));
        $this->assertTrue($this->model->opening_hours->isOpenOn('saturday'));
        $this->assertTrue($this->model->opening_hours->isOpenOn('sunday'));

    }

    /** @test */
    public function it_can_clear_opening_hours_from_array()
    {
        $this->model->opening_hours = [];

        $this->assertTrue($this->model->opening_hours->isClosedOn('monday'));
        $this->assertTrue($this->model->opening_hours->isClosedOn('tuesday'));
        $this->assertTrue($this->model->opening_hours->isClosedOn('wednesday'));
        $this->assertTrue($this->model->opening_hours->isClosedOn('thursday'));
        $this->assertTrue($this->model->opening_hours->isClosedOn('friday'));
        $this->assertTrue($this->model->opening_hours->isClosedOn('saturday'));
        $this->assertTrue($this->model->opening_hours->isClosedOn('sunday'));
    }

    public function it_throws_an_exception_for_wrong_attribute_data()
    {
        $this->expectException(Exception::class);
        $this->model->opening_hours = 'hello world';
    }
}