<?php

declare(strict_types=1);

namespace GreenZenMonk\SimplifiedScoreCalculator;

final class School
{
    private string $name;

    private string $faculty;

    private SchoolCurse $curse;

    public function __construct(
        string $name,
        string $faculty,
        SchoolCurse $curse
    ) {
        $this->name = $name;
        $this->faculty = $faculty;
        $this->curse = $curse;
    }

    public function getCurse(): SchoolCurse
    {
        return $this->curse;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFaculty(): string
    {
        return $this->faculty;
    }

    public function getTitle(): string
    {
        $schoolCurse = $this->getCurse();

        return $this->getName() . ' ' . $this->getFaculty() . ' - ' . $schoolCurse->getName();
    }
}
