<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\ExtraPointCategory;

interface ExtraPointInterface
{
    public function getCategory(): ExtraPointCategory;
}
