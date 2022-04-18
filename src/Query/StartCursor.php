<?php

namespace DTL\NotionApi\Query;

/**
 * Class StartCursor.
 */
class StartCursor
{
    /**
     * @var string
     */
    private string $cursor;

    /**
     * StartCursor constructor.
     *
     * @param  string  $cursor
     */
    public function __construct(string $cursor)
    {
        $this->cursor = $cursor;
    }

    public function __toString()
    {
        return $this->cursor;
    }
}
