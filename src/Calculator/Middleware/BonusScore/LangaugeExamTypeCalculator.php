<?php

namespace GreenZenMonk\SimplifiedScoreCalculator\Calculator\Middleware\BonusScore;

use GreenZenMonk\SimplifiedScoreCalculator\Student\ExtraPointParameterName;
use GreenZenMonk\SimplifiedScoreCalculator\Calculator\AbstractMiddleware;
use GreenZenMonk\SimplifiedScoreCalculator\Calculator\CalculatorResult;
use GreenZenMonk\SimplifiedScoreCalculator\Student;
use GreenZenMonk\SimplifiedScoreCalculator\Student\ExtraPointParameter\LanguageExamSubject;
use GreenZenMonk\SimplifiedScoreCalculator\Student\ExtraPointParameter\LanguageExamType;

final class LangaugeExamTypeCalculator extends AbstractMiddleware
{
    private const LANGAUGE_EXAM_TYPE_SCORE_C1 = 40;

    private const LANGAUGE_EXAM_TYPE_SCORE_B2 = 28;

    protected function doCalculate(Student $student, CalculatorResult $defaultCalculatorResult): CalculatorResult
    {
        $extraPointCollection = $student->getExtraPointCollection();

        $bonusScores = [];
        foreach ($extraPointCollection as $extraPoint) {
            if ($extraPoint->getCategory()->isLanguageExam()) {
                /**
                 * @var LanguageExamSubject $languageExamSubject
                 * @var LanguageExamType $languageExamType
                 */
                $languageExamSubject = $extraPoint->getParameter(ExtraPointParameterName::LANGUAGE_EXAM_SUBJECT);
                $languageExamType = $extraPoint->getParameter(ExtraPointParameterName::LANGUAGE_EXAM_TYPE);
                $languageExamName = $languageExamSubject->value;

                $score = 0;

                if ($languageExamType->isC1()) {
                    $score = self::LANGAUGE_EXAM_TYPE_SCORE_C1;
                } elseif ($languageExamType->isB2()) {
                    $score = self::LANGAUGE_EXAM_TYPE_SCORE_B2;
                }

                if (
                    !isset($bonusScores[$languageExamName])
                    || $bonusScores[$languageExamName] < $score
                ) {
                    $bonusScores[$languageExamName] = $score;
                }
            }
        }

        $bonusScore = array_sum($bonusScores);

        $defaultCalculatorResult->addBonusScore($bonusScore);

        return $defaultCalculatorResult;
    }
}
