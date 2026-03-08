<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Eligibility;

final readonly class Violation
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function __construct(
        private ViolationCode $code,
        private array $parameters = [],
        private ?string $path = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function getCode(): ViolationCode
    {
        return $this->code;
    }
}
