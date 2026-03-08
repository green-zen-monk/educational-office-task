<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Model\School\Course;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Graduation\GraduationSubject;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Graduation\GraduationSubjectType;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School\Course\RequiredGraduationSubject;
use PHPUnit\Framework\TestCase;

class RequiredGraduationSubjectTest extends TestCase
{
    public function testGetTitleReturnsSubjectValueForMediumType(): void
    {
        $requiredSubject = new RequiredGraduationSubject(
            GraduationSubject::History,
            GraduationSubjectType::Medium
        );

        $this->assertSame('history', $requiredSubject->getTitle());
    }

    public function testGetTitleAppendsSubjectTypeForHighType(): void
    {
        $requiredSubject = new RequiredGraduationSubject(
            GraduationSubject::EnglishGrammar,
            GraduationSubjectType::High
        );

        $this->assertSame('english language(high)', $requiredSubject->getTitle());
    }

    public function testMediumRequiredSubjectAcceptsMediumAndHighActualType(): void
    {
        $requiredSubject = new RequiredGraduationSubject(
            GraduationSubject::Mathematics,
            GraduationSubjectType::Medium
        );

        $this->assertTrue(
            $requiredSubject->isAvailable(GraduationSubject::Mathematics, GraduationSubjectType::Medium)
        );
        $this->assertTrue(
            $requiredSubject->isAvailable(GraduationSubject::Mathematics, GraduationSubjectType::High)
        );
    }

    public function testHighRequiredSubjectRejectsMediumActualType(): void
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

    public function testIsAvailableReturnsFalseWhenSubjectDoesNotMatch(): void
    {
        $requiredSubject = new RequiredGraduationSubject(
            GraduationSubject::Mathematics,
            GraduationSubjectType::Medium
        );

        $this->assertFalse(
            $requiredSubject->isAvailable(GraduationSubject::History, GraduationSubjectType::High)
        );
    }
}
