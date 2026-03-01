<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Calculator\Validator;

use GreenZenMonk\AdmissionScoreCalculator\Calculator\Validator\AbstractValidator;
use GreenZenMonk\AdmissionScoreCalculator\Calculator\ValidatorResult;
use GreenZenMonk\AdmissionScoreCalculator\Student;

final class GraduationResultMinNotReachValidator extends AbstractValidator
{
    private const MIN_SCORE = 20;

    protected function doCheck(Student $student): ValidatorResult
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
            return new ValidatorResult(
                false,
                'Az egyik tantárgy eredménye ' . $minScore . '% alatti!'
            );
        }

        return new ValidatorResult();
    }
}
