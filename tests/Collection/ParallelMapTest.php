<?php declare(strict_types = 1);

namespace Elevate\Tests\Collection;

use Amp\Parallel\Worker\DefaultPool;
use Illuminate\Support\Collection;
use Elevate\ServiceProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Stopwatch\Stopwatch;

class ParallelMapTest extends TestCase
{
    /**
     * Register the service providers.
     *
     **/
    protected function getPackageProviders($app) : array
    {
        return [ServiceProvider::class];
    }

    /** Symfony\Component\Stopwatch\Stopwatch */
    protected $stopWatch;

    public function setup(): void
    {
        parent::setUp();


        $this->stopWatch = new Stopwatch();
    }

    /** @test */
    public function it_can_perform_async_map_operations()
    {
        $this->startStopWatch();

        $collection = Collection::make([1, 2, 3, 4, 5])->parallelMap(function (int $number) {
            sleep(1);

            return $number * 10;
        });

        $this->assertTookLessThanSeconds(2);

        $this->assertEquals([10, 20, 30, 40, 50], $collection->toArray());
    }

    /** @test */
    public function it_can_limit_worker_pool_size_with_pool()
    {
        $this->startStopWatch();

        $pool = new DefaultPool(1);

        $collection = Collection::make([1, 2, 3])->parallelMap(function (int $number) {
            sleep(1);
        }, $pool);

        $this->assertTookMoreThanSeconds(2);
    }

    /** @test */
    public function it_can_limit_worker_pool_size_with_int()
    {
        $this->startStopWatch();

        $collection = Collection::make([1, 2, 3])->parallelMap(function (int $number) {
            sleep(1);
        }, 1);

        $this->assertTookMoreThanSeconds(2);
    }

    /** @test */
    public function it_can_handle_large_responses()
    {
        $sources = Collection::make([
            'https://spatie.be/en',
            'https://spatie.be/en/opensource',
            'https://laravel.com',
        ])->parallelMap(function (string $url) {
            return file_get_contents($url);
        });

        foreach ($sources as $source) {
            $this->assertStringContainsString('</html>', $source);
        }
    }

    protected function startStopWatch()
    {
        $this->stopWatch->start('test');
    }

    protected function assertTookLessThanSeconds(int $seconds)
    {
        $durationInMilliseconds = $this->stopWatch->stop('test')->getDuration();

        $this->assertLessThan($seconds * 1000, $durationInMilliseconds);
    }

    protected function assertTookMoreThanSeconds(int $seconds)
    {
        $durationInMilliseconds = $this->stopWatch->stop('test')->getDuration();

        $this->assertGreaterThan($seconds * 1000, $durationInMilliseconds);
    }
}