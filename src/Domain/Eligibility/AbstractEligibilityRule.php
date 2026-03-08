<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Domain\Eligibility;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Eligibility\Contract\EligibilityRule;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Eligibility\EligibilityResult;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student;

abstract class AbstractEligibilityRule implements EligibilityRule
{
    private ?EligibilityRule $next = null;

    public function setNext(EligibilityRule $next): EligibilityRule
    {
        $this->next = $next;

        return $next;
    }

    public function check(Student $student): EligibilityResult
    {
        $validatorResult = $this->doCheck($student);

        if ($this->next !== null && $validatorResult->isEligible()) {
            $validatorResult = $this->next->check($student);
        }

        return $validatorResult;
    }

    abstract protected function doCheck(Student $student): EligibilityResult;
}
