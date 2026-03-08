<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Application\Input\Dto;

final readonly class SchoolInput
{
    public function __construct(
        private string $university,
        private string $faculty,
        private CourseInput $course
    ) {
    }

    public function getUniversity(): string
    {
        return $this->university;
    }

    public function getFaculty(): string
    {
        return $this->faculty;
    }

    public function getCourse(): CourseInput
    {
        return $this->course;
    }
}
