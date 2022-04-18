<?php

namespace DTL\NotionApi\Endpoints;

use DTL\NotionApi\Entities\Entity;

/**
 * Interface EndpointInterface.
 */
interface EndpointInterface
{
    /**
     * @param  string  $id
     * @return Entity
     */
    public function find(string $id): Entity;
}
