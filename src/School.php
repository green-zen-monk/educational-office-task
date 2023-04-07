<?php

namespace GreenZenMonk\SimplifiedScoreCalculator;

final class School
{
    private string $name;

    private string $faculty;

    private SchoolCurse $schoolCurse;

    public function __construct(
        string $name,
        string $faculty,
        SchoolCurse $schoolCurse
    ) {
        $this->name = $name;
        $this->faculty = $faculty;
        $this->schoolCurse = $schoolCurse;
    }

    public function getSchoolCurse(): SchoolCurse
    {
        return $this->schoolCurse;
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
        $schoolCurse = $this->getSchoolCurse();

        return $this->getName() . ' ' . $this->getFaculty() . ' - ' . $schoolCurse->getName();
    }
}
