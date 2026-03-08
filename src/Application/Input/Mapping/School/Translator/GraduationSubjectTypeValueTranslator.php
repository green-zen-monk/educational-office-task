<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\School\Translator;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Graduation\GraduationSubjectType;
use InvalidArgumentException;

final class GraduationSubjectTypeValueTranslator
{
    public function map(string $value): GraduationSubjectType
    {
        if ($value === '') {
            throw new InvalidArgumentException('Missing graduation subject type value.');
        }

        $subjectType = GraduationSubjectType::tryFrom($value);
        if ($subjectType === null) {
            throw new InvalidArgumentException('Invalid graduation subject type value: ' . $value);
        }

        return $subjectType;
    }
}
