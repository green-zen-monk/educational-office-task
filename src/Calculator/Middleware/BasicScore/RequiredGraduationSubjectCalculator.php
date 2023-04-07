<?php

namespace GreenZenMonk\SimplifiedScoreCalculator\Calculator\Middleware\BasicScore;

use GreenZenMonk\SimplifiedScoreCalculator\Calculator\AbstractMiddleware;
use GreenZenMonk\SimplifiedScoreCalculator\Calculator\CalculatorResult;
use GreenZenMonk\SimplifiedScoreCalculator\Student;

final class RequiredGraduationSubjectCalculator extends AbstractMiddleware
{
    protected function doCalculate(Student $student, CalculatorResult $defaultCalculatorResult): CalculatorResult
    {
        $graduationResultCollection = $student->getGraduationResultCollection();

        $school = $student->getSelectedSchool();
        $schoolCurse = $school->getSchoolCurse();

        $requiredGraduationSubject = $schoolCurse->getRequiredGraduationSubject();

        $requiredGraduationSubjectResult = $graduationResultCollection->findRequiredGraduationSubjectResult(
            $requiredGraduationSubject
        );

        $basicScore = $requiredGraduationSubjectResult->getResult() * 2;

        $defaultCalculatorResult->addBasicScore($basicScore);

        return $defaultCalculatorResult;
    }
}
