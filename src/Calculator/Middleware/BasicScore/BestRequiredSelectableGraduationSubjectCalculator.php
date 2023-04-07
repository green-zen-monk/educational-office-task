<?php

namespace GreenZenMonk\SimplifiedScoreCalculator\Calculator\Middleware\BasicScore;

use GreenZenMonk\SimplifiedScoreCalculator\SchoolCurse\RequiredGraduationSubjectCollection;
use GreenZenMonk\SimplifiedScoreCalculator\Calculator\AbstractMiddleware;
use GreenZenMonk\SimplifiedScoreCalculator\Calculator\CalculatorResult;
use GreenZenMonk\SimplifiedScoreCalculator\Student;
use GreenZenMonk\SimplifiedScoreCalculator\Student\GraduationResult;
use GreenZenMonk\SimplifiedScoreCalculator\Student\GraduationResultCollection;

final class BestRequiredSelectableGraduationSubjectCalculator extends AbstractMiddleware
{
    private function findBestRequiredSelectableGraduationSubjectResult(
        RequiredGraduationSubjectCollection $requiredSelectableSubjects,
        GraduationResultCollection $graduationResultCollection
    ): ?GraduationResult {
        $filteredGraduationResults = $graduationResultCollection->filterRequiredSelectableGraduationSubjectResults(
            $requiredSelectableSubjects
        );

        $maxScore = 0;
        $selectedRequiredGraduationSubject = null;

        foreach ($filteredGraduationResults as $filteredGraduationResult) {
            $graduationResult = $filteredGraduationResult->getResult();
            if ($graduationResult > $maxScore) {
                $maxScore = $graduationResult;
                $selectedRequiredGraduationSubject = $filteredGraduationResult;
            }
        }

        return $selectedRequiredGraduationSubject;
    }

    protected function doCalculate(Student $student, CalculatorResult $defaultCalculatorResult): CalculatorResult
    {
        $graduationResultCollection = $student->getGraduationResultCollection();

        $school = $student->getSelectedSchool();
        $schoolCurse = $school->getSchoolCurse();

        $requiredSelectableSubjects = $schoolCurse->getRequiredSelectableGraduationSubjects();

        $bestRequiredSelectableGraduationSubjectResults = $this->findBestRequiredSelectableGraduationSubjectResult(
            $requiredSelectableSubjects,
            $graduationResultCollection
        );

        $basicScore = $bestRequiredSelectableGraduationSubjectResults->getResult() * 2;

        $defaultCalculatorResult->addBasicScore($basicScore);

        return $defaultCalculatorResult;
    }
}
