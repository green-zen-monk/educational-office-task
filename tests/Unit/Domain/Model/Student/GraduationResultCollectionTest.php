<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Model\Student;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Graduation\GraduationSubject;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Graduation\GraduationSubjectType;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School\Course\RequiredGraduationSubject;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School\Course\RequiredGraduationSubjectCollection;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\GraduationResult;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\GraduationResultCollection;
use PHPUnit\Framework\TestCase;

class GraduationResultCollectionTest extends TestCase
{
    public function testFindRequiredGraduationSubjectResultReturnsHighestResultFromDuplicates(): void
    {
        $collection = new GraduationResultCollection([
            new GraduationResult(GraduationSubject::Mathematics, GraduationSubjectType::Medium, 61),
            new GraduationResult(GraduationSubject::Mathematics, GraduationSubjectType::High, 93),
        ]);

        $result = $collection->findRequiredGraduationSubjectResult(
            new RequiredGraduationSubject(GraduationSubject::Mathematics)
        );

        $this->assertNotNull($result);
        $this->assertSame(93, $result->getResult());
    }

    public function testFilterRequiredSelectableGraduationSubjectResultsReturnsBestResultPerSubject(): void
    {
        $collection = new GraduationResultCollection([
            new GraduationResult(GraduationSubject::Physics, GraduationSubjectType::Medium, 55),
            new GraduationResult(GraduationSubject::Physics, GraduationSubjectType::High, 88),
            new GraduationResult(GraduationSubject::Chemistry, GraduationSubjectType::Medium, 70),
        ]);

        $filteredResults = $collection->filterRequiredSelectableGraduationSubjectResults(
            new RequiredGraduationSubjectCollection([
                new RequiredGraduationSubject(GraduationSubject::Physics),
                new RequiredGraduationSubject(GraduationSubject::Chemistry),
            ])
        );

        $this->assertCount(2, $filteredResults);
        $this->assertSame(88, $filteredResults[0]->getResult());
        $this->assertSame(70, $filteredResults[1]->getResult());
    }
}
