<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Application\UseCase;

use GreenZenMonk\AdmissionScoreCalculator\Application\Contract\ViolationMessageResolver;
use GreenZenMonk\AdmissionScoreCalculator\Application\Exception\CalculateAdmissionScoreException;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Eligibility\Contract\EligibilityRule;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Eligibility\EligibilityResult;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Scoring\AdmissionScore;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Scoring\ScoreEngine;

final class CalculateAdmissionScore
{
    public function __construct(
        private EligibilityRule $eligibilityRule,
        private ScoreEngine $scoreEngine,
        private ?ViolationMessageResolver $violationMessageResolver = null
    ) {
    }

    public function check(Student $student): EligibilityResult
    {
        return $this->eligibilityRule->check($student);
    }

    /**
     * @throws CalculateAdmissionScoreException
     */
    public function execute(Student $student): AdmissionScore
    {
        $eligibilityResult = $this->check($student);

        if (!$eligibilityResult->isEligible()) {
            throw new CalculateAdmissionScoreException($this->resolveEligibilityFailureMessage($eligibilityResult));
        }

        return $this->scoreEngine->calculate($student);
    }

    private function resolveEligibilityFailureMessage(EligibilityResult $result): string
    {
        if ($this->violationMessageResolver !== null) {
            $resolvedMessage = $this->violationMessageResolver->resolve($result);
            if ($resolvedMessage !== null && $resolvedMessage !== '') {
                return $resolvedMessage;
            }
        }

        $violations = $result->violations();
        if ($violations === []) {
            return 'Student is not eligible for admission.';
        }

        return $violations[0]->getCode()->value;
    }
}
