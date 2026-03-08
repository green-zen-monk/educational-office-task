<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Input\Mapping\Student\Homework\Translator;

use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\Student\Homework\Translator\LanguageTranslator;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\ExtraPoint\LanguageExam\Language;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class LanguageTranslatorTest extends TestCase
{
    public function testMapReturnsEnumForKnownHungarianValue(): void
    {
        $mapper = new LanguageTranslator();

        $this->assertSame(Language::English, $mapper->map('angol'));
        $this->assertSame(Language::German, $mapper->map('német'));
    }

    public function testMapThrowsForUnknownValue(): void
    {
        $mapper = new LanguageTranslator();

        $this->expectException(InvalidArgumentException::class);
        $mapper->map('english');
    }

    public function testMapThrowsForMissingValue(): void
    {
        $mapper = new LanguageTranslator();

        $this->expectException(InvalidArgumentException::class);
        $mapper->map('');
    }
}
