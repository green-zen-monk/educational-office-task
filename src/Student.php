<?php

declare(strict_types=1);

namespace GreenZenMonk\SimplifiedScoreCalculator;

use GreenZenMonk\SimplifiedScoreCalculator\Student\LanguageExamExtraPointCollection;
use GreenZenMonk\SimplifiedScoreCalculator\Student\GraduationResultCollection;

final class Student
{
    private School $selectedSchool;
    private GraduationResultCollection $graduationResults;
    private LanguageExamExtraPointCollection $languageExamExtraPointCollection;

    public function __construct(
        School $selectedSchool,
        GraduationResultCollection $graduationResults,
        LanguageExamExtraPointCollection $languageExamExtraPointCollection
    ) {
        $this->selectedSchool = $selectedSchool;
        $this->graduationResults = $graduationResults;
        $this->languageExamExtraPointCollection = $languageExamExtraPointCollection;
    }

    public function getSelectedSchool(): School
    {
        return $this->selectedSchool;
    }

    public function getGraduationResultCollection(): GraduationResultCollection
    {
        return $this->graduationResults;
    }

    public function getLanguageExamCollection(): LanguageExamExtraPointCollection
    {
        return $this->languageExamExtraPointCollection;
    }

    public static function builder(SchoolCollection $schools): StudentBuilder
    {
        return new StudentBuilder($schools);
    }
}
