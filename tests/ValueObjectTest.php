<?php

declare(strict_types=1);

namespace Tests;

use GreenZenMonk\SimplifiedScoreCalculator\GraduationSubject;
use GreenZenMonk\SimplifiedScoreCalculator\GraduationSubjectType;
use GreenZenMonk\SimplifiedScoreCalculator\SchoolCourse\RequiredGraduationSubject;
use GreenZenMonk\SimplifiedScoreCalculator\Student\GraduationResult;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ValueObjectTest extends TestCase
{
    public function testGraduationResultAcceptsBoundaryValues(): void
    {
        $minResult = new GraduationResult(
            GraduationSubject::MATHEMATICS,
            GraduationSubjectType::MEDIUM,
            0
        );
        $maxResult = new GraduationResult(
            GraduationSubject::MATHEMATICS,
            GraduationSubjectType::HIGH,
            100
        );

        $this->assertSame(0, $minResult->getResult());
        $this->assertSame(100, $maxResult->getResult());
    }

    public function testGraduationResultThrowsOnNegativeValue(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new GraduationResult(
            GraduationSubject::MATHEMATICS,
            GraduationSubjectType::MEDIUM,
            -1
        );
    }

    public function testGraduationResultThrowsOnValueAboveHundred(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new GraduationResult(
            GraduationSubject::MATHEMATICS,
            GraduationSubjectType::MEDIUM,
            101
        );
    }

    public function testRequiredGraduationSubjectMediumAlsoAcceptsHighExam(): void
    {
        $requiredSubject = new RequiredGraduationSubject(
            GraduationSubject::HISTORY,
            GraduationSubjectType::MEDIUM
        );

        $this->assertTrue(
            $requiredSubject->isAvailable(GraduationSubject::HISTORY, GraduationSubjectType::MEDIUM)
        );
        $this->assertTrue(
            $requiredSubject->isAvailable(GraduationSubject::HISTORY, GraduationSubjectType::HIGH)
        );
    }

    public function testRequiredGraduationSubjectHighRequiresHighExam(): void
    {
        $requiredSubject = new RequiredGraduationSubject(
            GraduationSubject::ENGLISH_GRAMMAR,
            GraduationSubjectType::HIGH
        );

        $this->assertFalse(
            $requiredSubject->isAvailable(GraduationSubject::ENGLISH_GRAMMAR, GraduationSubjectType::MEDIUM)
        );
        $this->assertTrue(
            $requiredSubject->isAvailable(GraduationSubject::ENGLISH_GRAMMAR, GraduationSubjectType::HIGH)
        );
    }
}
