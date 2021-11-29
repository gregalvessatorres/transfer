<?php

use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Testing\TestCase as BaseTestCase;
use Faker\Factory as Faker;

abstract class TestCase extends BaseTestCase
{
    /**
     * @var \Faker\Generator
     */
    public $faker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->faker = Faker::create();
        DB::setDefaultConnection('no_db');
    }

    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }
}
