<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Domain\Scoring\Contract;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Scoring\ScoreAccumulator;

interface ScoringPolicy
{
    public function apply(Student $student, ScoreAccumulator $accumulator): void;
}
