<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\School\Translator;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Graduation\GraduationSubject;
use InvalidArgumentException;

final class GraduationSubjectValueTranslator
{
    public function map(string $value): GraduationSubject
    {
        if ($value === '') {
            throw new InvalidArgumentException('Missing graduation subject value.');
        }

        $subject = GraduationSubject::tryFrom($value);
        if ($subject === null) {
            throw new InvalidArgumentException('Invalid graduation subject value: ' . $value);
        }

        return $subject;
    }
}
