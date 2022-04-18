<?php

namespace DTL\NotionApi\Endpoints;

use DTL\NotionApi\Entities\Collections\UserCollection;
use DTL\NotionApi\Entities\User;
use DTL\NotionApi\Exceptions\HandlingException;
use DTL\NotionApi\Exceptions\NotionException;

/**
 * Class Users.
 */
class Users extends Endpoint implements EndpointInterface
{
    /**
     * List users
     * url: https://api.notion.com/{version}/users
     * notion-api-docs: https://developers.notion.com/reference/get-users.
     *
     * @return UserCollection
     *
     * @throws HandlingException
     * @throws NotionException
     */
    public function all(): UserCollection
    {
        $resultData = $this->getJson($this->url(Endpoint::USERS)."?{$this->buildPaginationQuery()}");

        return new UserCollection($resultData);
    }

    /**
     * Retrieve a user
     * url: https://api.notion.com/{version}/users/{user_id}
     * notion-api-docs: https://developers.notion.com/reference/get-user.
     *
     * @param  string  $userId
     * @return User
     *
     * @throws HandlingException
     * @throws NotionException
     */
    public function find(string $userId): User
    {
        $response = $this->get(
            $this->url(Endpoint::USERS.'/'.$userId)
        );

        return new User($response->json());
    }
}
