<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Input\Mapping\Student\Homework\Translator;

use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\Student\Homework\Translator\ExtraPointCategoryTranslator;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\ExtraPointCategory;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ExtraPointCategoryTranslatorTest extends TestCase
{
    public function testMapReturnsEnumForKnownHungarianValue(): void
    {
        $mapper = new ExtraPointCategoryTranslator();

        $this->assertSame(ExtraPointCategory::LanguageExam, $mapper->map('Nyelvvizsga'));
    }

    public function testMapThrowsForUnknownValue(): void
    {
        $mapper = new ExtraPointCategoryTranslator();

        $this->expectException(InvalidArgumentException::class);
        $mapper->map('language exam');
    }

    public function testMapThrowsForMissingValue(): void
    {
        $mapper = new ExtraPointCategoryTranslator();

        $this->expectException(InvalidArgumentException::class);
        $mapper->map('');
    }
}
