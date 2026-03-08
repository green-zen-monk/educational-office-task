<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Domain\Scoring;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Scoring\AdmissionScore;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Scoring\Contract\ScoringPolicy;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Scoring\ScoreAccumulator;

final class ScoreEngine
{
    /** @param list<ScoringPolicy> $policies */
    public function __construct(private array $policies)
    {
    }

    public function calculate(Student $student): AdmissionScore
    {
        $accumulator = new ScoreAccumulator();

        foreach ($this->policies as $policy) {
            $policy->apply($student, $accumulator);
        }

        return $accumulator->toAdmissionScore();
    }
}
