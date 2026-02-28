<?php

declare(strict_types=1);

namespace GreenZenMonk\SimplifiedScoreCalculator\Student\ExtraPoint;

use GreenZenMonk\SimplifiedScoreCalculator\Student\ExtraPointCategory;
use GreenZenMonk\SimplifiedScoreCalculator\Student\ExtraPointInterface;
use GreenZenMonk\SimplifiedScoreCalculator\Student\ExtraPointParameter\LanguageExamSubject;
use GreenZenMonk\SimplifiedScoreCalculator\Student\ExtraPointParameter\LanguageExamType;

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
