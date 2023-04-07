<?php

namespace GreenZenMonk\SimplifiedScoreCalculator;

use GreenZenMonk\SimplifiedScoreCalculator\Student\ExtraPointCollection;
use GreenZenMonk\SimplifiedScoreCalculator\Student\GraduationResultCollection;

final class Student
{
    private School $selectedSchool;
    private GraduationResultCollection $graduationResults;
    private ExtraPointCollection $extraPointCollection;

    public function __construct(
        School $selectedSchool,
        GraduationResultCollection $graduationResults,
        ExtraPointCollection $extraPointCollection
    ) {
        $this->selectedSchool = $selectedSchool;
        $this->graduationResults = $graduationResults;
        $this->extraPointCollection = $extraPointCollection;
    }

    public function getSelectedSchool(): School
    {
        return $this->selectedSchool;
    }

    public function getGraduationResultCollection(): GraduationResultCollection
    {
        return $this->graduationResults;
    }

    public function getExtraPointCollection(): ExtraPointCollection
    {
        return $this->extraPointCollection;
    }

    public static function builder(SchoolCollection $schools): StudentBuilder
    {
        return new StudentBuilder($schools);
    }
}
