<?php

namespace Webfactor\Laravel\OpeningHours\Tests;

use DB;
use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as Orchestra;
use Webfactor\Laravel\OpeningHours\OpeningHoursServiceProvider;

abstract class TestCase extends Orchestra
{
    public function setUp()
    {
        parent::setUp();
        $this->setUpDatabase($this->app);
    }

    protected function getPackageProviders($app)
    {
        return [
            OpeningHoursServiceProvider::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase($app)
    {
        //$this->dropAllTables();
        include_once __DIR__.'/../database/migrations/create_opening_hours_tables.php';
        (new \CreateOpeningHoursTables())->up();

        $app['db']->connection()->getSchemaBuilder()->create('test_models', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
        });
    }

    protected function dropAllTables()
    {
        $rows = collect(DB::select('SHOW TABLES'));
        if ($rows->isEmpty()) {
            return;
        }
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        $rows
            ->map(function ($row) {
                return $row->Tables_in_laravel_tags;
            })
            ->each(function (string $tableName) {
                DB::statement("DROP TABLE {$tableName}");
            });
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}