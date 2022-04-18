<?php

namespace DTL\NotionApi\Entities\Collections;

use DTL\NotionApi\Entities\Page;
use Illuminate\Support\Collection;

/**
 * Class PageCollection.
 */
class PageCollection extends EntityCollection
{
    protected function collectChildren(): void
    {
        $this->collection = new Collection();
        foreach ($this->rawResults as $pageChild) {
            $this->collection->add(new Page($pageChild));
        }
    }
}
