<?php

namespace DTL\NotionApi\Entities\Collections;

use DTL\NotionApi\Entities\User;
use Illuminate\Support\Collection;

/**
 * Class UserCollection.
 */
class UserCollection extends EntityCollection
{
    protected function collectChildren(): void
    {
        $this->collection = new Collection();
        foreach ($this->rawResults as $userChild) {
            $this->collection->add(new User($userChild));
        }
    }
}
