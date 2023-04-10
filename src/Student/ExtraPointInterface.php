<?php

namespace GreenZenMonk\SimplifiedScoreCalculator\Student;

use GreenZenMonk\SimplifiedScoreCalculator\Student\ExtraPointCategory;

interface ExtraPointInterface
{
    public function getCategory(): ExtraPointCategory;
}
