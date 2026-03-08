<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Input\Mapping\Student\Homework\Translator;

use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\Student\Homework\Translator\GraduationSubjectTranslator;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Graduation\GraduationSubject;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class GraduationSubjectTranslatorTest extends TestCase
{
    public function testMapReturnsEnumForKnownHungarianValue(): void
    {
        $mapper = new GraduationSubjectTranslator();

        $this->assertSame(
            GraduationSubject::HungarianGrammarAndLiterature,
            $mapper->map('magyar nyelv és irodalom')
        );
        $this->assertSame(
            GraduationSubject::EnglishGrammar,
            $mapper->map('angol nyelv')
        );
    }

    public function testMapThrowsForUnknownValue(): void
    {
        $mapper = new GraduationSubjectTranslator();

        $this->expectException(InvalidArgumentException::class);
        $mapper->map('history');
    }

    public function testMapThrowsForMissingValue(): void
    {
        $mapper = new GraduationSubjectTranslator();

        $this->expectException(InvalidArgumentException::class);
        $mapper->map('');
    }
}
