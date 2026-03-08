<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Application\Input\Dto;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\ExtraPoint\LanguageExam\Language;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\ExtraPoint\LanguageExam\Type;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\ExtraPointCategory;

final readonly class ExtraPointInput
{
    public function __construct(
        private ExtraPointCategory $category,
        private Language $language,
        private Type $type
    ) {
    }

    public function getCategory(): ExtraPointCategory
    {
        return $this->category;
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }

    public function getType(): Type
    {
        return $this->type;
    }
}
