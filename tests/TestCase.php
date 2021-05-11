<?php

namespace BBSLab\LighthousePersistedQueries\Tests;

use BBSLab\LighthousePersistedQueries\LighthousePersistedQueriesServiceProvider;
use BBSLab\LighthousePersistedQueries\PersistsQuery;
use Nuwave\Lighthouse\LighthouseServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            LighthousePersistedQueriesServiceProvider::class,
            LighthouseServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set(
            'lighthouse.route.middleware',
            array_merge(
                config('lighthouse.route.middleware', []),
                [PersistsQuery::class]
            )
        );

        config()->set(
            'lighthouse.schema.register',
            __DIR__.'/graphql/schema.graphql'
        );
    }
}
