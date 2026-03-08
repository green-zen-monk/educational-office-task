<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Input\Mapping\School\Translator;

use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\School\Translator\GraduationSubjectValueTranslator;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Graduation\GraduationSubject;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class GraduationSubjectValueTranslatorTest extends TestCase
{
    public function testMapReturnsEnumForKnownValue(): void
    {
        $translator = new GraduationSubjectValueTranslator();

        $this->assertSame(GraduationSubject::Mathematics, $translator->map('mathematics'));
        $this->assertSame(GraduationSubject::Physics, $translator->map('physics'));
    }

    public function testMapThrowsForUnknownValue(): void
    {
        $translator = new GraduationSubjectValueTranslator();

        $this->expectException(InvalidArgumentException::class);
        $translator->map('math');
    }

    public function testMapThrowsForMissingValue(): void
    {
        $translator = new GraduationSubjectValueTranslator();

        $this->expectException(InvalidArgumentException::class);
        $translator->map('');
    }
}
