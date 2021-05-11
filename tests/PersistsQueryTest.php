<?php

namespace BBSLab\LighthousePersistedQueries\Tests;

use BBSLab\LighthousePersistedQueries\PersistsQuery;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Symfony\Component\Routing\RouterInterface;

class PersistsQueryTest extends TestCase
{
    use MakesGraphQLRequests;


    public function can_call_test_query()
    {
        $this->graphQL($this->query())->assertJson([
            'data' => [
                'test' => 'Hello World!',
            ],
        ]);
    }


    public function get_error_when_query_is_not_persisted()
    {
        $url = $this->graphQLEndpointUrl().'?'.Arr::query($this->extensions());

        $this->json('GET', $url)->assertJson([
                'errors' => [
                    array_merge(['message' => 'PersistedQueryNotFound'], $this->extensions()),
                ],

            ])
            ->assertHeader('Cache-Control', 'no-store, private');


    }

    /** @test */
    public function can_store_query()
    {
        $this->withoutExceptionHandling();
        $this->graphQL($this->query(), [], $this->extensions())->assertJson([
            'data' => [
                'test' => 'Hello World!',
            ],
        ]);

        $key = PersistsQuery::cacheKey($this->hash());

        $this->assertTrue(Cache::has($key));

        $this->assertEquals(
            trim($this->query()),
            Cache::get($key),
        );

        $url = $this->graphQLEndpointUrl().'?'.Arr::query($this->extensions());

        $this->json('GET', $url)->assertJson([
            'data' => [
                'test' => 'Hello World!',
            ],
        ])
            ->assertHeader('Cache-Control', 'max-age=86400, public');
    }

    protected function query(): string
    {
        return /** @lang GraphQL */'
{
    test
}
';
    }

    protected function hash(): string
    {
        return hash('sha256', $this->query());
    }

    protected function extensions(): array
    {
        return [
            'extensions' => [
                'persistedQuery' => [
                    'version' => 1,
                    'sha256Hash' => $this->hash(),
                ],
            ],
        ];
    }
}
