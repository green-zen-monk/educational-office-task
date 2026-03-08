<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Domain\Scoring\Policy\BasicScore;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Scoring\Contract\ScoringPolicy;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Scoring\ScoreAccumulator;

final class RequiredGraduationSubjectPolicy implements ScoringPolicy
{
    public function apply(Student $student, ScoreAccumulator $accumulator): void
    {
        $graduationResultCollection = $student->getGraduationResultCollection();

        $school = $student->getSelectedSchool();
        $schoolCourse = $school->getCourse();
        $requiredGraduationSubject = $schoolCourse->getRequiredGraduationSubject();

        $requiredGraduationSubjectResult = $graduationResultCollection->findRequiredGraduationSubjectResult(
            $requiredGraduationSubject
        );

        $subjectScore = 0;
        if ($requiredGraduationSubjectResult !== null) {
            $subjectScore = $requiredGraduationSubjectResult->getResult();
        }

        $accumulator->addBasicScore($subjectScore * 2);
    }
}
