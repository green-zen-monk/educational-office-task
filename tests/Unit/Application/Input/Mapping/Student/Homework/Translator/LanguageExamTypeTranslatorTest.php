<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Input\Mapping\Student\Homework\Translator;

use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\Student\Homework\Translator\LanguageExamTypeTranslator;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\ExtraPoint\LanguageExam\Type;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class LanguageExamTypeTranslatorTest extends TestCase
{
    public function testMapReturnsEnumForKnownValue(): void
    {
        $mapper = new LanguageExamTypeTranslator();

        $this->assertSame(Type::B2, $mapper->map('B2'));
        $this->assertSame(Type::C1, $mapper->map('C1'));
    }

    public function testMapThrowsForUnknownValue(): void
    {
        $mapper = new LanguageExamTypeTranslator();

        $this->expectException(InvalidArgumentException::class);
        $mapper->map('b2');
    }

    public function testMapThrowsForMissingValue(): void
    {
        $mapper = new LanguageExamTypeTranslator();

        $this->expectException(InvalidArgumentException::class);
        $mapper->map('');
    }
}
