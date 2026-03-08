<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Application\Input\Dto;

final readonly class StudentInput
{
    /**
     * @param list<GraduationResultInput> $graduationResults
     * @param list<ExtraPointInput> $extraPoints
     */
    public function __construct(
        private SelectedProgramInput $selectedProgram,
        private array $graduationResults,
        private array $extraPoints
    ) {
    }

    public function getSelectedProgram(): SelectedProgramInput
    {
        return $this->selectedProgram;
    }

    /**
     * @return list<GraduationResultInput>
     */
    public function getGraduationResults(): array
    {
        return $this->graduationResults;
    }

    /**
     * @return list<ExtraPointInput>
     */
    public function getExtraPoints(): array
    {
        return $this->extraPoints;
    }
}
