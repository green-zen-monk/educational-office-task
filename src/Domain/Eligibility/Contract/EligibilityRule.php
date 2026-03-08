<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Domain\Eligibility\Contract;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Eligibility\EligibilityResult;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student;

interface EligibilityRule
{
    public function setNext(EligibilityRule $next): EligibilityRule;
    public function check(Student $student): EligibilityResult;
}
