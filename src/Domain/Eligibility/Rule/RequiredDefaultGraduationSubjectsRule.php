<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Domain\Eligibility\Rule;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Eligibility\AbstractEligibilityRule;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Eligibility\EligibilityResult;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Eligibility\Violation;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Eligibility\ViolationCode;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Graduation\GraduationSubject;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\GraduationResult;

final class RequiredDefaultGraduationSubjectsRule extends AbstractEligibilityRule
{
    private const REQUIRED_GRADUATION_SUBJECTS = [
        GraduationSubject::HungarianGrammarAndLiterature,
        GraduationSubject::History,
        GraduationSubject::Mathematics
    ];

    protected function doCheck(Student $student): EligibilityResult
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
                $hasNotRequiredGraduationSubjectValues[] = $requiredGraduationSubject;
            }
        }

        if (!empty($hasNotRequiredGraduationSubjectValues)) {
            return EligibilityResult::notEligible(
                new Violation(
                    ViolationCode::MandatorySubjectsMissing,
                    [
                        'missingSubjects' => $hasNotRequiredGraduationSubjectValues
                    ]
                )
            );
        }

        return EligibilityResult::eligible();
    }
}
