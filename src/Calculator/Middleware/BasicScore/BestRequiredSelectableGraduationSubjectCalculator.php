<?php

declare(strict_types=1);

namespace GreenZenMonk\SimplifiedScoreCalculator\Calculator\Middleware\BasicScore;

use GreenZenMonk\SimplifiedScoreCalculator\SchoolCourse\RequiredGraduationSubjectCollection;
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

    protected function doCalculate(Student $student, CalculatorResult $calculatorResult): CalculatorResult
    {
        $graduationResultCollection = $student->getGraduationResultCollection();

        $school = $student->getSelectedSchool();
        $schoolCourse = $school->getCourse();

        $requiredSelectableSubjects = $schoolCourse->getRequiredSelectableGraduationSubjects();

        $bestRequiredSelectableGraduationSubjectResults = $this->findBestRequiredSelectableGraduationSubjectResult(
            $requiredSelectableSubjects,
            $graduationResultCollection
        );

        $basicScore = $bestRequiredSelectableGraduationSubjectResults->getResult() * 2;

        $calculatorResult->addBasicScore($basicScore);

        return $calculatorResult;
    }
}
