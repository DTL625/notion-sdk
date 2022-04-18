<?php

namespace DTL\NotionApi\Entities\Properties;

use DTL\NotionApi\Entities\Contracts\Modifiable;
use DTL\NotionApi\Exceptions\HandlingException;

/**
 * Class Checkbox.
 */
class Checkbox extends Property implements Modifiable
{
    /**
     * @param $checked
     * @return Checkbox
     */
    public static function value(bool $checked): Checkbox
    {
        $checkboxProperty = new Checkbox();
        $checkboxProperty->content = $checked;

        $checkboxProperty->rawContent = [
            'checkbox' => $checkboxProperty->isChecked(),
        ];

        return $checkboxProperty;
    }

    /**
     * @throws HandlingException
     */
    protected function fillFromRaw(): void
    {
        parent::fillFromRaw();
        $this->content = $this->rawContent;
    }

    /**
     * @return bool
     */
    public function getContent(): bool
    {
        return $this->content;
    }

    /**
     * @return bool
     */
    public function isChecked(): bool
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function asText(): string
    {
        return ($this->getContent()) ? 'true' : 'false';
    }
}
