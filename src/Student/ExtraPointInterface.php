<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Student;

use GreenZenMonk\AdmissionScoreCalculator\Student\ExtraPointCategory;

interface ExtraPointInterface
{
    public function getCategory(): ExtraPointCategory;
}
