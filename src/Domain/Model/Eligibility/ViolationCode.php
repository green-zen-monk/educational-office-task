<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Eligibility;

enum ViolationCode: string
{
    case RequiredGraduationSubjectMissing = 'required_graduation_subject_missing';
    case MandatorySubjectsMissing = 'mandatory_subjects_missing';
    case SelectableSubjectMissing = 'selectable_subject_missing';
    case SubjectBelowMinimum = 'subject_below_minimum';
}
