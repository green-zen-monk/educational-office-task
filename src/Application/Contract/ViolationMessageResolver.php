<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Application\Contract;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Eligibility\EligibilityResult;

interface ViolationMessageResolver
{
    public function resolve(EligibilityResult $result): ?string;
}
