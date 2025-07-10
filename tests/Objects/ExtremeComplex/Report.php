<?php

namespace Roke\PhpFactory\Tests\Objects\ExtremeComplex;

class Report
{
    public string $reportId;
    public ?DataSource $source;
    public array $data;
    public string $status;
    public ?string $errorMessage;

    public function __construct(
        string $reportId,
        ?DataSource $source,
        array $data = [],
        string $status = 'PENDING',
        ?string $errorMessage = null
    ) {
        $this->reportId = $reportId;
        $this->source = $source;
        $this->data = $data;
        $this->status = $status;
        $this->errorMessage = $errorMessage;
    }
}
