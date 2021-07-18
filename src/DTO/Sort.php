<?php

namespace App\DTO;

use Symfony\Component\Serializer\Annotation as Serializer;

class Sort
{
    /**
     * @var string
     */
    public $field;
    /**
     * @var string
     */
    public $type;

    /**
     * @Serializer\Groups({"admin","list"})
     * @return array
     */
    public function getCombined(): array
    {
        if ($this->field && $this->type) {
            return [$this->field => $this->type];
        }

        return [];
    }
}
