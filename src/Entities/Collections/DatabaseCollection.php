<?php

namespace DTL\NotionApi\Entities\Collections;

use DTL\NotionApi\Entities\Database;
use Illuminate\Support\Collection;

/**
 * Class DatabaseCollection.
 */
class DatabaseCollection extends EntityCollection
{
    protected function collectChildren(): void
    {
        $this->collection = new Collection();
        foreach ($this->rawResults as $databaseChild) {
            $this->collection->add(new Database($databaseChild));
        }
    }
}
