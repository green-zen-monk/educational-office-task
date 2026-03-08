<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Domain\Model;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\ExtraPointCollection;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\GraduationResultCollection;

final readonly class Student
{
    public function __construct(
        private School $selectedSchool,
        private GraduationResultCollection $graduationResults,
        private ExtraPointCollection $extraPointCollection
    ) {
    }

    public function getSelectedSchool(): School
    {
        return $this->selectedSchool;
    }

    public function getGraduationResultCollection(): GraduationResultCollection
    {
        return $this->graduationResults;
    }

    public function getExtraPointCollection(): ExtraPointCollection
    {
        return $this->extraPointCollection;
    }
}
