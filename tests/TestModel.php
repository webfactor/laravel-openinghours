<?php

namespace Webfactor\Laravel\OpeningHours\Tests;

use Illuminate\Database\Eloquent\Model;
use Webfactor\Laravel\OpeningHours\Traits\HasOpeningHours;

class TestModel extends Model
{
    use HasOpeningHours;

    public $table = 'test_models';
    protected $guarded = [];
    public $timestamps = false;
}