<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Calculator\Validator;

use GreenZenMonk\AdmissionScoreCalculator\Calculator\Validator\AbstractValidator;
use GreenZenMonk\AdmissionScoreCalculator\Calculator\ValidatorResult;
use GreenZenMonk\AdmissionScoreCalculator\GraduationSubject;
use GreenZenMonk\AdmissionScoreCalculator\Student;
use GreenZenMonk\AdmissionScoreCalculator\Student\GraduationResult;

final class RequiredDefaultGraduationSubjectsValidator extends AbstractValidator
{
    private const REQUIRED_GRADUATION_SUBJECTS = [
        GraduationSubject::HungarianGrammarAndLiterature,
        GraduationSubject::Histor,
        GraduationSubject::Mathematics
    ];

    protected function doCheck(Student $student): ValidatorResult
    {
        $graduationResultCollection = $student->getGraduationResultCollection();

        $hasNotRequiredGraduationSubjectValues = [];
        foreach (self::REQUIRED_GRADUATION_SUBJECTS as $requiredGraduationSubject) {
            $hasRequiredGraduationSubject = $graduationResultCollection
                ->containsWithConditionCallback(
                    function (GraduationResult $item) use ($requiredGraduationSubject) {
                        return $requiredGraduationSubject === $item->getGraduationSubject();
                    }
                );

            if (!$hasRequiredGraduationSubject) {
                $hasNotRequiredGraduationSubjectValues[] = $requiredGraduationSubject->value;
            }
        }

        if (!empty($hasNotRequiredGraduationSubjectValues)) {
            $graduationSubjectValues = implode(', ', $hasNotRequiredGraduationSubjectValues);

            return new ValidatorResult(
                false,
                'A kötelező érettségi tantárgyak közül nem végezte el az alábbiakat: ' . $graduationSubjectValues
            );
        }

        return new ValidatorResult();
    }
}
