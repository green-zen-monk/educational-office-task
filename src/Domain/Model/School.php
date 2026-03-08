<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Domain\Model;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School\Course;

final readonly class School
{
    public function __construct(
        private string $name,
        private string $faculty,
        private Course $course
    ) {
    }

    public function getCourse(): Course
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
