<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Domain\Scoring\Policy\BonusScore;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Scoring\Contract\ScoringPolicy;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Scoring\ScoreAccumulator;

final class GraduationSubjectTypeHighPolicy implements ScoringPolicy
{
    private const GRADUATION_SUBJECT_TYPE_HIGH_SCORE = 50;

    public function apply(Student $student, ScoreAccumulator $accumulator): void
    {
        $graduationResultCollection = $student->getGraduationResultCollection();

        $bonusScore = 0;
        foreach ($graduationResultCollection as $graduationResult) {
            $graduationSubjectType = $graduationResult->getGraduationSubjectType();
            if ($graduationSubjectType->isHigh()) {
                $bonusScore += self::GRADUATION_SUBJECT_TYPE_HIGH_SCORE;
            }
        }

        $accumulator->addBonusScore($bonusScore);
    }
}
