<?php

namespace GreenZenMonk\SimplifiedScoreCalculator\Calculator\Middleware\BonusScore;

use GreenZenMonk\SimplifiedScoreCalculator\Calculator\AbstractMiddleware;
use GreenZenMonk\SimplifiedScoreCalculator\Calculator\CalculatorResult;
use GreenZenMonk\SimplifiedScoreCalculator\Student;

final class GraduationSubjectTypeHighCalculator extends AbstractMiddleware
{
    private const GRADUATION_SUBJECT_TYPE_HIGH_SCORE = 50;

    protected function doCalculate(Student $student, CalculatorResult $calculatorResult): CalculatorResult
    {
        $graduationResultCollection = $student->getGraduationResultCollection();

        $bonusScore = 0;
        foreach ($graduationResultCollection as $graduationResult) {
            $graduationSubjectType = $graduationResult->getGraduationSubjectType();

            if ($graduationSubjectType->isHigh()) {
                $bonusScore += self::GRADUATION_SUBJECT_TYPE_HIGH_SCORE;
            }
        }

        $calculatorResult->addBonusScore($bonusScore);

        return $calculatorResult;
    }
}
