<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Student\ExtraPoint;

use GreenZenMonk\AdmissionScoreCalculator\Student\ExtraPointCategory;
use GreenZenMonk\AdmissionScoreCalculator\Student\ExtraPointInterface;
use GreenZenMonk\AdmissionScoreCalculator\Student\ExtraPointParameter\LanguageExamSubject;
use GreenZenMonk\AdmissionScoreCalculator\Student\ExtraPointParameter\LanguageExamType;

final readonly class LanguageExamExtraPoint implements ExtraPointInterface
{
    private ExtraPointCategory $category;
    private LanguageExamSubject $subject;
    private LanguageExamType $type;

    public function __construct(
        ExtraPointCategory $category,
        LanguageExamSubject $subject,
        LanguageExamType $type
    ) {
        $this->category = $category;
        $this->subject = $subject;
        $this->type = $type;
    }

    public function getCategory(): ExtraPointCategory
    {
        return $this->category;
    }

    public function getSubject(): LanguageExamSubject
    {
        return $this->subject;
    }

    public function getType(): LanguageExamType
    {
        return $this->type;
    }
}
