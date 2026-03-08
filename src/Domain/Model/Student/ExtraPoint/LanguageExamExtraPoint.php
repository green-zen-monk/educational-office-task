<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\ExtraPoint;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\ExtraPoint\LanguageExam\Language;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\ExtraPoint\LanguageExam\Type;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\ExtraPointCategory;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\ExtraPointInterface;

final readonly class LanguageExamExtraPoint implements ExtraPointInterface
{
    public function __construct(
        private Language $subject,
        private Type $type
    ) {
    }

    public function getCategory(): ExtraPointCategory
    {
        return ExtraPointCategory::LanguageExam;
    }

    public function getSubject(): Language
    {
        return $this->subject;
    }

    public function getType(): Type
    {
        return $this->type;
    }
}
