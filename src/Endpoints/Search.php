<?php

namespace DTL\NotionApi\Endpoints;

use DTL\NotionApi\Entities\Collections\EntityCollection;
use DTL\NotionApi\Notion;
use DTL\NotionApi\Query\Sorting;
use Illuminate\Support\Collection;

/**
 * Class Search.
 */
class Search extends Endpoint
{
    /**
     * @var string
     */
    private string $searchText;

    /**
     * @var string|null
     */
    private ?string $filter = null;

    /**
     * @var Sorting|null
     */
    private ?Sorting $sort = null;

    /**
     * Search constructor.
     *
     * @param  Notion  $notion
     * @param  string  $searchText
     *
     * @throws \DTL\NotionApi\Exceptions\HandlingException
     * @throws \DTL\NotionApi\Exceptions\LaravelNotionAPIException
     */
    public function __construct(Notion $notion, string $searchText = '')
    {
        $this->searchText = $searchText;
        parent::__construct($notion);
    }

    /**
     * @return EntityCollection
     *
     * @throws \DTL\NotionApi\Exceptions\HandlingException
     * @throws \DTL\NotionApi\Exceptions\NotionException
     */
    public function query(): EntityCollection
    {
        $postData = [];

        if ($this->sort !== null) {
            $postData['sort'] = $this->sort->toArray();
        }

        if ($this->filter !== null) {
            $postData['filter'] = ['property' => 'object', 'value' => $this->filter];
        }

        if ($this->startCursor !== null) {
            $postData['start_cursor'] = $this->startCursor;
        }

        if ($this->pageSize !== null) {
            $postData['page_size'] = $this->pageSize;
        }

        if ($this->searchText !== null) {
            $postData['query'] = $this->searchText;
        }

        $response = $this
            ->post(
                $this->url(Endpoint::SEARCH),
                $postData
            )
            ->json();

        return new EntityCollection($response);
    }

    /**
     * @param  string  $direction
     * @return $this
     */
    public function sortByLastEditedTime(string $direction = 'ascending'): Search
    {
        $this->sort = Sorting::timestampSort('last_edited_time', $direction);

        return $this;
    }

    /**
     * @return $this
     */
    public function onlyDatabases(): Search
    {
        $this->filter = 'database';

        return $this;
    }

    /**
     * @return $this
     */
    public function onlyPages(): Search
    {
        $this->filter = 'page';

        return $this;
    }

    /**
     * @return Collection
     *
     * @throws \DTL\NotionApi\Exceptions\HandlingException
     * @throws \DTL\NotionApi\Exceptions\NotionException
     */
    public function getTitles(): Collection
    {
        $titleCollection = new Collection();
        $results = $this->query();

        foreach ($results as $result) {
            $titleCollection->add($result->getTitle());
        }

        return $titleCollection;
    }

    /**
     * @return Collection
     *
     * @throws \DTL\NotionApi\Exceptions\HandlingException
     * @throws \DTL\NotionApi\Exceptions\NotionException
     */
    public function getIds(): Collection
    {
        $idCollection = new Collection();
        $results = $this->query();

        foreach ($results as $result) {
            $idCollection->add($result->getId());
        }

        return $idCollection;
    }

    /**
     * @param  string  $filter
     * @return $this
     */
    public function filterBy(string $filter): Search
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * @param  Sorting  $sort
     * @return $this
     */
    public function sortBy(Sorting $sort): Search
    {
        $this->sort = $sort;

        return $this;
    }
}
