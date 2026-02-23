<?php

declare(strict_types=1);

namespace GreenZenMonk\SimplifiedScoreCalculator\Calculator\Middleware\BasicScore;

use GreenZenMonk\SimplifiedScoreCalculator\Calculator\Middleware\AbstractMiddleware;
use GreenZenMonk\SimplifiedScoreCalculator\Calculator\ScoreAccumulator;
use GreenZenMonk\SimplifiedScoreCalculator\Student;

final class RequiredGraduationSubjectCalculator extends AbstractMiddleware
{
    protected function doCalculate(Student $student, ScoreAccumulator $scoreAccumulator): ScoreAccumulator
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

        $basicScore = $subjectScore * 2;

        $scoreAccumulator->addBasicScore($basicScore);

        return $scoreAccumulator;
    }
}
