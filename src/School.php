<?php

declare(strict_types=1);

namespace GreenZenMonk\SimplifiedScoreCalculator;

final class School
{
    private string $name;

    private string $faculty;

    private SchoolCourse $course;

    public function __construct(
        string $name,
        string $faculty,
        SchoolCourse $course
    ) {
        $this->name = $name;
        $this->faculty = $faculty;
        $this->course = $course;
    }

    public function getCourse(): SchoolCourse
    {
        return $this->course;
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
        $schoolCourse = $this->getCourse();

        return $this->getName() . ' ' . $this->getFaculty() . ' - ' . $schoolCourse->getName();
    }
}
