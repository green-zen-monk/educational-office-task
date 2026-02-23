<?php

declare(strict_types=1);

namespace GreenZenMonk\SimplifiedScoreCalculator\Calculator\Middleware\BonusScore;

use GreenZenMonk\SimplifiedScoreCalculator\Calculator\AbstractMiddleware;
use GreenZenMonk\SimplifiedScoreCalculator\Calculator\ScoreAccumulator;
use GreenZenMonk\SimplifiedScoreCalculator\Student;

final class LanguageExamTypeCalculator extends AbstractMiddleware
{
    private const LANGUAGE_EXAM_TYPE_SCORE_C1 = 40;

    private const LANGUAGE_EXAM_TYPE_SCORE_B2 = 28;

    protected function doCalculate(Student $student, ScoreAccumulator $scoreAccumulator): ScoreAccumulator
    {
        $languageExamCollection = $student->getLanguageExamCollection();

        $bonusScores = [];
        foreach ($languageExamCollection as $extraPoint) {
            $languageExamSubject = $extraPoint->getSubject();
            $languageExamType = $extraPoint->getType();
            $languageExamName = $languageExamSubject->value;

            $score = 0;

            if ($languageExamType->isC1()) {
                $score = self::LANGUAGE_EXAM_TYPE_SCORE_C1;
            } elseif ($languageExamType->isB2()) {
                $score = self::LANGUAGE_EXAM_TYPE_SCORE_B2;
            }

            if (
                !isset($bonusScores[$languageExamName])
                || $bonusScores[$languageExamName] < $score
            ) {
                $bonusScores[$languageExamName] = $score;
            }
        }

        $bonusScore = array_sum($bonusScores);

        $scoreAccumulator->addBonusScore($bonusScore);

        return $scoreAccumulator;
    }
}
