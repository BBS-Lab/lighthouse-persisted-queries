<?php

namespace BBSLab\LighthousePersistedQueries\Tests;

use BBSLab\LighthousePersistedQueries\PersistsQuery;
use Nuwave\Lighthouse\GlobalId\GlobalIdServiceProvider;
use Nuwave\Lighthouse\LighthouseServiceProvider;
use Nuwave\Lighthouse\OrderBy\OrderByServiceProvider;
use Nuwave\Lighthouse\Pagination\PaginationServiceProvider;
use Nuwave\Lighthouse\Scout\ScoutServiceProvider;
use Nuwave\Lighthouse\SoftDeletes\SoftDeletesServiceProvider;
use Nuwave\Lighthouse\Validation\ValidationServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use BBSLab\LighthousePersistedQueries\LighthousePersistedQueriesServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            LighthousePersistedQueriesServiceProvider::class,
            LighthouseServiceProvider::class,
            GlobalIdServiceProvider::class,
            OrderByServiceProvider::class,
            PaginationServiceProvider::class,
            ScoutServiceProvider::class,
            SoftDeletesServiceProvider::class,
            ValidationServiceProvider::class
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
