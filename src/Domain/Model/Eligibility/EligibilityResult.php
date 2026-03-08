<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Eligibility;

final readonly class EligibilityResult
{
    /**
     * @param list<Violation> $violations
     */
    private function __construct(private array $violations)
    {
    }

    public static function eligible(): self
    {
        return new self([]);
    }

    public static function notEligible(Violation ...$violations): self
    {
        return new self(array_values($violations));
    }

    public function isEligible(): bool
    {
        return empty($this->violations);
    }

    /** @return list<Violation> */
    public function violations(): array
    {
        return $this->violations;
    }
}
