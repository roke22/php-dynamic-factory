<?php

namespace Roke\PhpFactory\Tests\Objects\ExtremeComplex;

class DataSource
{
    public string $sourceName;

    public function __construct(string $sourceName = 'Default Source')
    {
        $this->sourceName = $sourceName;
    }
}
