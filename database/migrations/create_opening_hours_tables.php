<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOpeningHoursTables extends Migration
{
    public function up()
    {
        Schema::create('opening_hours', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->increments('id');
            $table->morphs('openable');
            $table->enum('day', [
                'monday',
                'tuesday',
                'wednesday',
                'thursday',
                'friday',
                'saturday',
                'sunday'
            ]);
            $table->time('start');
            $table->time('end');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('opening_hours');
    }
}
