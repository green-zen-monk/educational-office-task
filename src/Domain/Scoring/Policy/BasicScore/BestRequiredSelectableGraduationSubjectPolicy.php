<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Domain\Scoring\Policy\BasicScore;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School\Course\RequiredGraduationSubjectCollection;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\GraduationResult;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\GraduationResultCollection;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Scoring\Contract\ScoringPolicy;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Scoring\ScoreAccumulator;

final class BestRequiredSelectableGraduationSubjectPolicy implements ScoringPolicy
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

    public function apply(Student $student, ScoreAccumulator $accumulator): void
    {
        $graduationResultCollection = $student->getGraduationResultCollection();

        $school = $student->getSelectedSchool();
        $schoolCourse = $school->getCourse();
        $requiredSelectableSubjects = $schoolCourse->getRequiredSelectableGraduationSubjects();

        $bestRequiredSelectableGraduationSubjectResult = $this->findBestRequiredSelectableGraduationSubjectResult(
            $requiredSelectableSubjects,
            $graduationResultCollection
        );

        $subjectScore = 0;
        if ($bestRequiredSelectableGraduationSubjectResult !== null) {
            $subjectScore = $bestRequiredSelectableGraduationSubjectResult->getResult();
        }

        $accumulator->addBasicScore($subjectScore * 2);
    }
}
