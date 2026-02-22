<?php

declare(strict_types=1);

namespace GreenZenMonk\SimplifiedScoreCalculator\Calculator;

final class ValidatorResult
{
    private bool $isValid;

    private ?string $message;

    public function __construct(bool $isValid = true, string $message = 'A tanuló pontozható!')
    {
        $this->isValid = $isValid;
        $this->message = $message;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
