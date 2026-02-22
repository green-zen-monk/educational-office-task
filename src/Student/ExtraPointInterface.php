<?php

declare(strict_types=1);

namespace GreenZenMonk\SimplifiedScoreCalculator\Student;

use GreenZenMonk\SimplifiedScoreCalculator\Student\ExtraPointCategory;

interface ExtraPointInterface
{
    public function getCategory(): ExtraPointCategory;
}
