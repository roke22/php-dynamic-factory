<?php

namespace Roke\PhpFactory\Tests\Objects;

class ObjectWithDefaults
{
    public string $required;
    public string $optional;
    public ?string $nullable;

    public function __construct(string $required, string $optional = 'default', ?string $nullable = null)
    {
        $this->required = $required;
        $this->optional = $optional;
        $this->nullable = $nullable;
    }
}
