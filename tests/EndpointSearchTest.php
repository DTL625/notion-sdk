<?php

namespace DTL\NotionA\Tests;

use DTL\NotionApi\Entities\Collections\EntityCollection;
use DTL\NotionApi\Entities\Database;
use DTL\NotionApi\Entities\Page;
use DTL\NotionApi\Exceptions\NotionException;
use Illuminate\Support\Facades\Http;
use Notion;

/**
 * Class EndpointSearchTest.
 *
 * The fake API responses are based on Notions documentation.
 *
 * @see https://developers.notion.com/reference/post-search
 */
class EndpointSearchTest extends NotionApiTest
{
    /** @test */
    public function it_throws_a_notion_exception_bad_request()
    {
        // failing /v1/search
        Http::fake([
            'https://api.notion.com/v1/search' => Http::response(
                json_decode('{}', true),
                400,
                ['Headers']
            ),
        ]);

        $this->expectException(NotionException::class);
        $this->expectExceptionMessage('Bad Request');

        Notion::search()->query();
    }

    /** @test */
    public function it_returns_all_pages_and_databases_of_the_workspace_as_collection_with_entity_objects()
    {
        // successful /v1/search
        Http::fake([
            'https://api.notion.com/v1/search' => Http::response(
                json_decode(file_get_contents('tests/stubs/endpoints/search/response_all_200.json'), true),
                200,
                ['Headers']
            ),
        ]);

        $searchResult = Notion::search()->query();
        $entityCollection = $searchResult->asCollection();
        $this->assertInstanceOf(EntityCollection::class, $searchResult);
        $this->assertIsIterable($entityCollection);
        $this->assertCount(2, $entityCollection);

        $database = $entityCollection[0];
        $page = $entityCollection[1];

        $this->assertInstanceOf(Database::class, $database);
        $this->assertInstanceOf(Page::class, $page);
    }

    /** @test */
    public function it_returns_only_pages_of_the_workspace_as_collection_with_entity_objects()
    {
        // successful /v1/search
        Http::fake([
            'https://api.notion.com/v1/search' => Http::response(
                json_decode(file_get_contents('tests/stubs/endpoints/search/response_pages_200.json'), true),
                200,
                ['Headers']
            ),
        ]);

        $searchResult = Notion::search()->onlyPages()->query();
        $entityCollection = $searchResult->asCollection();
        $this->assertInstanceOf(EntityCollection::class, $searchResult);
        $this->assertIsIterable($entityCollection);
        $this->assertCount(1, $entityCollection);

        $page = $entityCollection->first();

        $this->assertInstanceOf(Page::class, $page);
    }

    /** @test */
    public function it_returns_only_databases_of_the_workspace_as_collection_with_entity_objects()
    {
        // successful /v1/search
        Http::fake([
            'https://api.notion.com/v1/search' => Http::response(
                json_decode(file_get_contents('tests/stubs/endpoints/search/response_databases_200.json'), true),
                200,
                ['Headers']
            ),
        ]);

        $searchResult = Notion::search()->onlyDatabases()->query();
        $entityCollection = $searchResult->asCollection();
        $this->assertInstanceOf(EntityCollection::class, $searchResult);
        $this->assertIsIterable($entityCollection);
        $this->assertCount(1, $entityCollection);

        $database = $entityCollection->first();

        $this->assertInstanceOf(Database::class, $database);
    }
}
