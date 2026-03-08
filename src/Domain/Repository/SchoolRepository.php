<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Domain\Repository;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School;

interface SchoolRepository
{
    public function findByProgram(string $university, string $faculty, string $course): ?School;
}
