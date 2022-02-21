<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Testing\TestResponse;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../bootstrap/app.php';

        TestResponse::macro('assertResource', function ($resource) {
            return $this->assertJson($resource->response()->getData(true));
        });

        TestResponse::macro('assertExactResource', function ($resource) {
            return $this->assertExactJson($resource->response()->getData(true));
        });

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
