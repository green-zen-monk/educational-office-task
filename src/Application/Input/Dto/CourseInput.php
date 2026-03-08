<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Application\Input\Dto;

final readonly class CourseInput
{
    /**
     * @param list<RequiredGraduationSubjectInput> $requiredSelectableGraduationSubjects
     */
    public function __construct(
        private string $name,
        private RequiredGraduationSubjectInput $requiredGraduationSubject,
        private array $requiredSelectableGraduationSubjects
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRequiredGraduationSubject(): RequiredGraduationSubjectInput
    {
        return $this->requiredGraduationSubject;
    }

    /**
     * @return list<RequiredGraduationSubjectInput>
     */
    public function getRequiredSelectableGraduationSubjects(): array
    {
        return $this->requiredSelectableGraduationSubjects;
    }
}
