<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Calculator\Middleware\BasicScore;

use GreenZenMonk\AdmissionScoreCalculator\Calculator\Middleware\AbstractMiddleware;
use GreenZenMonk\AdmissionScoreCalculator\Calculator\ScoreAccumulator;
use GreenZenMonk\AdmissionScoreCalculator\SchoolCourse\RequiredGraduationSubjectCollection;
use GreenZenMonk\AdmissionScoreCalculator\Student;
use GreenZenMonk\AdmissionScoreCalculator\Student\GraduationResult;
use GreenZenMonk\AdmissionScoreCalculator\Student\GraduationResultCollection;

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

    protected function doCalculate(Student $student, ScoreAccumulator $scoreAccumulator): ScoreAccumulator
    {
        $graduationResultCollection = $student->getGraduationResultCollection();

        $school = $student->getSelectedSchool();
        $schoolCourse = $school->getCourse();

        $requiredSelectableSubjects = $schoolCourse->getRequiredSelectableGraduationSubjects();

        $bestRequiredSelectableGraduationSubjectResults = $this->findBestRequiredSelectableGraduationSubjectResult(
            $requiredSelectableSubjects,
            $graduationResultCollection
        );

        $subjectScore = 0;

        if ($bestRequiredSelectableGraduationSubjectResults !== null) {
            $subjectScore = $bestRequiredSelectableGraduationSubjectResults->getResult();
        }

        $basicScore = $subjectScore * 2;

        $scoreAccumulator->addBasicScore($basicScore);

        return $scoreAccumulator;
    }
}
