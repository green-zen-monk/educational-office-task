<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Calculator\Validator;

use GreenZenMonk\AdmissionScoreCalculator\Calculator\Validator\AbstractValidator;
use GreenZenMonk\AdmissionScoreCalculator\Calculator\ValidatorResult;
use GreenZenMonk\AdmissionScoreCalculator\Student;

final class RequiredSelectableGraduationSubjectsValidator extends AbstractValidator
{
    protected function doCheck(Student $student): ValidatorResult
    {
        $graduationResultCollection = $student->getGraduationResultCollection();

        $school = $student->getSelectedSchool();
        $schoolCourse = $school->getCourse();
        $requiredSelectableSubjects = $schoolCourse->getRequiredSelectableGraduationSubjects();

        $selectedGraduationSubjectResults = $graduationResultCollection
            ->filterRequiredSelectableGraduationSubjectResults(
                $requiredSelectableSubjects
            );

        if (empty($selectedGraduationSubjectResults)) {
            $requiredGraduationSubjectTitles = [];

            foreach ($requiredSelectableSubjects as $requiredGraduationSubject) {
                $requiredGraduationSubjectTitles[] = $requiredGraduationSubject->getTitle();
            }

            $graduationSubjectTitles = implode(', ', $requiredGraduationSubjectTitles);
            $schoolTitle = $school->getTitle();

            return new ValidatorResult(
                false,
                'A(z) ' . $schoolTitle . ' szakon kötelezően választható érettségi tantárgyak közül' .
                ' egyiket sem végezte el: ' . $graduationSubjectTitles
            );
        }

        return new ValidatorResult();
    }
}
