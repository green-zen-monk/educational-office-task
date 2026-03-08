<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Input\Mapping\Student\Homework\Translator;

use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\Student\Homework\Translator\GraduationSubjectTypeTranslator;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Graduation\GraduationSubjectType;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class GraduationSubjectTypeTranslatorTest extends TestCase
{
    public function testMapReturnsEnumForKnownHungarianValue(): void
    {
        $mapper = new GraduationSubjectTypeTranslator();

        $this->assertSame(GraduationSubjectType::Medium, $mapper->map('közép'));
        $this->assertSame(GraduationSubjectType::High, $mapper->map('emelt'));
    }

    public function testMapThrowsForUnknownValue(): void
    {
        $mapper = new GraduationSubjectTypeTranslator();

        $this->expectException(InvalidArgumentException::class);
        $mapper->map('medium');
    }

    public function testMapThrowsForMissingValue(): void
    {
        $mapper = new GraduationSubjectTypeTranslator();

        $this->expectException(InvalidArgumentException::class);
        $mapper->map('');
    }
}
