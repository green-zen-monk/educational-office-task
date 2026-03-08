<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Domain\Eligibility\Rule;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Eligibility\AbstractEligibilityRule;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Eligibility\EligibilityResult;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Eligibility\Violation;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Eligibility\ViolationCode;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student;

final class GraduationResultMinNotReachRule extends AbstractEligibilityRule
{
    private const MIN_SCORE = 20;

    protected function doCheck(Student $student): EligibilityResult
    {
        $graduationResultCollection = $student->getGraduationResultCollection();

        $hasMinScore = false;
        $minScore = self::MIN_SCORE;
        foreach ($graduationResultCollection as $graduationResult) {
            $score = $graduationResult->getResult();
            if ($score < $minScore) {
                $hasMinScore = true;
                break;
            }
        }

        if ($hasMinScore) {
            return EligibilityResult::notEligible(
                new Violation(
                    ViolationCode::SubjectBelowMinimum,
                    [
                        'minScore' => $minScore
                    ]
                )
            );
        }

        return EligibilityResult::eligible();
    }
}
