<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Application\Input\Dto;

final readonly class SelectedProgramInput
{
    public function __construct(
        private string $university,
        private string $faculty,
        private string $course
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

    public function getCourse(): string
    {
        return $this->course;
    }
}
