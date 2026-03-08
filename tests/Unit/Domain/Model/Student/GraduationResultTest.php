<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Model\Student;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Graduation\GraduationSubject;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Graduation\GraduationSubjectType;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\GraduationResult;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class GraduationResultTest extends TestCase
{
    public function testCanBeCreatedWithBoundaryValues(): void
    {
        $minResult = new GraduationResult(
            GraduationSubject::Mathematics,
            GraduationSubjectType::Medium,
            0
        );
        $maxResult = new GraduationResult(
            GraduationSubject::EnglishGrammar,
            GraduationSubjectType::High,
            100
        );

        $this->assertSame(0, $minResult->getResult());
        $this->assertSame(100, $maxResult->getResult());
    }

    public function testExposesSubjectAndType(): void
    {
        $result = new GraduationResult(
            GraduationSubject::History,
            GraduationSubjectType::High,
            77
        );

        $this->assertSame(GraduationSubject::History, $result->getGraduationSubject());
        $this->assertSame(GraduationSubjectType::High, $result->getGraduationSubjectType());
    }

    public function testThrowsOnNegativeResult(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new GraduationResult(
            GraduationSubject::Mathematics,
            GraduationSubjectType::Medium,
            -1
        );
    }

    public function testThrowsOnResultAboveHundred(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new GraduationResult(
            GraduationSubject::Mathematics,
            GraduationSubjectType::Medium,
            101
        );
    }
}
