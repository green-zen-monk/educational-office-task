<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School\Course\RequiredGraduationSubject;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School\Course\RequiredGraduationSubjectCollection;

final readonly class Course
{
    public function __construct(
        private string $name,
        private RequiredGraduationSubject $requiredGraduationSubject,
        private RequiredGraduationSubjectCollection $requiredSelectableGraduationSubjects
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRequiredGraduationSubject(): RequiredGraduationSubject
    {
        return $this->requiredGraduationSubject;
    }

    public function getRequiredSelectableGraduationSubjects(): RequiredGraduationSubjectCollection
    {
        return $this->requiredSelectableGraduationSubjects;
    }
}
