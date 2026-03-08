<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Domain\Scoring\Policy\BonusScore;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\ExtraPoint\LanguageExamExtraPoint;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\ExtraPointInterface;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Scoring\Contract\ScoringPolicy;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Scoring\ScoreAccumulator;

final class LanguageExamTypePolicy implements ScoringPolicy
{
    private const LANGUAGE_EXAM_TYPE_SCORE_C1 = 40;
    private const LANGUAGE_EXAM_TYPE_SCORE_B2 = 28;

    public function apply(Student $student, ScoreAccumulator $accumulator): void
    {
        /** @var LanguageExamExtraPoint[] $languageExamCollection */
        $languageExamCollection = $student->getExtraPointCollection()->filterWithCallback(
            static fn (ExtraPointInterface $extraPoint): bool => $extraPoint->getCategory()->isLanguageExam()
        );

        $bonusScores = [];
        foreach ($languageExamCollection as $extraPoint) {
            $languageExamName = $extraPoint->getSubject()->value;
            $languageExamType = $extraPoint->getType();

            $score = 0;
            if ($languageExamType->isC1()) {
                $score = self::LANGUAGE_EXAM_TYPE_SCORE_C1;
            } elseif ($languageExamType->isB2()) {
                $score = self::LANGUAGE_EXAM_TYPE_SCORE_B2;
            }

            if (!isset($bonusScores[$languageExamName]) || $bonusScores[$languageExamName] < $score) {
                $bonusScores[$languageExamName] = $score;
            }
        }

        $accumulator->addBonusScore(array_sum($bonusScores));
    }
}
