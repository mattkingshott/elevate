<?php declare(strict_types = 1);

namespace Elevate\Tests\Collection;

use Elevate\ServiceProvider;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Collection;

class ToPairsTest extends TestCase
{
    /**
     * Register the service providers.
     *
     **/
    protected function getPackageProviders($app) : array
    {
        return [ServiceProvider::class];
    }

    /** @test */
    public function it_provides_a_fromPairs_macro()
    {
        $this->assertTrue(Collection::hasMacro('fromPairs'));
    }

    /** @test */
    public function it_can_transform_a_collection_into_an_array_with_pairs()
    {
        $this->assertEquals([
            ['john@example.com', 'John'],
            ['jane@example.com', 'Jane'],
            ['dave@example.com', 'Dave'],
        ], Collection::make([
            'john@example.com' => 'John',
            'jane@example.com' => 'Jane',
            'dave@example.com' => 'Dave',
        ])->toPairs()->toArray());
    }
}
