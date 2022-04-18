<?php

namespace DTL\NotionApi\Tests;

use DTL\NotionApi\Notion;
use DTL\NotionApi\NotionFacade;
use Illuminate\Support\Collection;
use Orchestra\Testbench\TestCase;

/**
 * Class EndpointPageTest.
 *
 * The fake API responses are based on our test environment (since the current Notion examples do not match with the actual calls).
 *
 * @see https://developers.notion.com/reference/get-page
 */
class NotionApiTest extends TestCase
{
    /**
     * @param  \Illuminate\Foundation\Application  $app
     * @return string[]
     */
    protected function getPackageProviders($app): array
    {
        return ['DTL\NotionApi\LaravelNotionApiServiceProvider'];
    }

    /**
     * @param  \Illuminate\Foundation\Application  $app
     * @return string[]
     */
    protected function getPackageAliases($app): array
    {
        return [
            'Notion' => NotionFacade::class,
        ];
    }

    protected function assertContainsInstanceOf(string $class, $haystack): bool
    {
        if (! is_array($haystack) && ! ($haystack instanceof Collection)) {
            throw new \InvalidArgumentException('$haystack must be an array or a Collection');
        }

        foreach ($haystack as $item) {
            if (get_class($item) === $class) {
                return true;
            }
        }

        return false;
    }

    /** @test */
    public function it_asserts_true()
    {
        $this->assertTrue(true);
    }
}
