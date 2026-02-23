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
            GraduationSubject::Mathematics,
            GraduationSubjectType::Medium,
            0
        );
        $maxResult = new GraduationResult(
            GraduationSubject::Mathematics,
            GraduationSubjectType::High,
            100
        );

        $this->assertSame(0, $minResult->getResult());
        $this->assertSame(100, $maxResult->getResult());
    }

    public function testGraduationResultThrowsOnNegativeValue(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new GraduationResult(
            GraduationSubject::Mathematics,
            GraduationSubjectType::Medium,
            -1
        );
    }

    public function testGraduationResultThrowsOnValueAboveHundred(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new GraduationResult(
            GraduationSubject::Mathematics,
            GraduationSubjectType::Medium,
            101
        );
    }

    public function testRequiredGraduationSubjectMediumAlsoAcceptsHighExam(): void
    {
        $requiredSubject = new RequiredGraduationSubject(
            GraduationSubject::Histor,
            GraduationSubjectType::Medium
        );

        $this->assertTrue(
            $requiredSubject->isAvailable(GraduationSubject::Histor, GraduationSubjectType::Medium)
        );
        $this->assertTrue(
            $requiredSubject->isAvailable(GraduationSubject::Histor, GraduationSubjectType::High)
        );
    }

    public function testRequiredGraduationSubjectHighRequiresHighExam(): void
    {
        $requiredSubject = new RequiredGraduationSubject(
            GraduationSubject::EnglishGrammar,
            GraduationSubjectType::High
        );

        $this->assertFalse(
            $requiredSubject->isAvailable(GraduationSubject::EnglishGrammar, GraduationSubjectType::Medium)
        );
        $this->assertTrue(
            $requiredSubject->isAvailable(GraduationSubject::EnglishGrammar, GraduationSubjectType::High)
        );
    }
}
