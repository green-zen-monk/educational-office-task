<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Domain\Eligibility\Rule;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Eligibility\AbstractEligibilityRule;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Eligibility\EligibilityResult;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Eligibility\Violation;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Eligibility\ViolationCode;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student;

final class RequiredGraduationSubjectRule extends AbstractEligibilityRule
{
    protected function doCheck(Student $student): EligibilityResult
    {
        $graduationResultCollection = $student->getGraduationResultCollection();

        $school = $student->getSelectedSchool();
        $schoolCourse = $school->getCourse();
        $requiredGraduationSubject = $schoolCourse->getRequiredGraduationSubject();

        $graduationSubjectResult = $graduationResultCollection
            ->findRequiredGraduationSubjectResult($requiredGraduationSubject);

        if (!$graduationSubjectResult) {
            return EligibilityResult::notEligible(
                new Violation(
                    ViolationCode::RequiredGraduationSubjectMissing,
                    [
                        'school' => $school,
                        'requiredGraduationSubject' => $requiredGraduationSubject
                    ]
                )
            );
        }

        return EligibilityResult::eligible();
    }
}
