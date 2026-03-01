<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Calculator\Middleware\BonusScore;

use GreenZenMonk\AdmissionScoreCalculator\Calculator\Middleware\AbstractMiddleware;
use GreenZenMonk\AdmissionScoreCalculator\Calculator\ScoreAccumulator;
use GreenZenMonk\AdmissionScoreCalculator\Student;

final class GraduationSubjectTypeHighCalculator extends AbstractMiddleware
{
    private const GRADUATION_SUBJECT_TYPE_HIGH_SCORE = 50;

    protected function doCalculate(Student $student, ScoreAccumulator $scoreAccumulator): ScoreAccumulator
    {
        $graduationResultCollection = $student->getGraduationResultCollection();

        $bonusScore = 0;
        foreach ($graduationResultCollection as $graduationResult) {
            $graduationSubjectType = $graduationResult->getGraduationSubjectType();

            if ($graduationSubjectType->isHigh()) {
                $bonusScore += self::GRADUATION_SUBJECT_TYPE_HIGH_SCORE;
            }
        }

        $scoreAccumulator->addBonusScore($bonusScore);

        return $scoreAccumulator;
    }
}
