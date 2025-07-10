<?php

namespace Roke\PhpFactory\Tests\Objects;

class Team
{
    public string $teamName;
    /**
     * @var Member[]
     */
    public array $members;

    /**
     * @param string $teamName
     * @param array<\Roke\PhpFactory\Tests\Objects\Member> $members
     */
    public function __construct(string $teamName, array $members)
    {
        $this->teamName = $teamName;
        $this->members = $members;
    }
}
