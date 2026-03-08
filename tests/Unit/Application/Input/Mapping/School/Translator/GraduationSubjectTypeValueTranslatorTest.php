<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Input\Mapping\School\Translator;

use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\School\Translator\GraduationSubjectTypeValueTranslator;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Graduation\GraduationSubjectType;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class GraduationSubjectTypeValueTranslatorTest extends TestCase
{
    public function testMapReturnsEnumForKnownValue(): void
    {
        $translator = new GraduationSubjectTypeValueTranslator();

        $this->assertSame(GraduationSubjectType::Medium, $translator->map('medium'));
        $this->assertSame(GraduationSubjectType::High, $translator->map('high'));
    }

    public function testMapThrowsForUnknownValue(): void
    {
        $translator = new GraduationSubjectTypeValueTranslator();

        $this->expectException(InvalidArgumentException::class);
        $translator->map('advanced');
    }

    public function testMapThrowsForMissingValue(): void
    {
        $translator = new GraduationSubjectTypeValueTranslator();

        $this->expectException(InvalidArgumentException::class);
        $translator->map('');
    }
}
