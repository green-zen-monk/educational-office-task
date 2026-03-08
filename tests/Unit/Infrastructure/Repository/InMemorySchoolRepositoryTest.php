<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Repository;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Graduation\GraduationSubject;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School\Course;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School\Course\RequiredGraduationSubject;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School\Course\RequiredGraduationSubjectCollection;
use GreenZenMonk\AdmissionScoreCalculator\Infrastructure\Repository\InMemorySchoolRepository;
use PHPUnit\Framework\TestCase;

class InMemorySchoolRepositoryTest extends TestCase
{
    public function testFindByProgramReturnsMatchingSchool(): void
    {
        $repository = new InMemorySchoolRepository([
            $this->createElteSchool(),
            $this->createPpkeSchool(),
        ]);

        $selectedSchool = $repository->findByProgram('ELTE', 'IK', 'Programtervező informatikus');

        $this->assertNotNull($selectedSchool);
        $this->assertSame('ELTE', $selectedSchool->getName());
        $this->assertSame('IK', $selectedSchool->getFaculty());
        $this->assertSame('Programtervező informatikus', $selectedSchool->getCourse()->getName());
    }

    public function testFindByProgramReturnsNullWhenNoMatchingSchool(): void
    {
        $repository = new InMemorySchoolRepository([$this->createElteSchool()]);

        $selectedSchool = $repository->findByProgram('BME', 'VIK', 'Mérnökinformatikus');

        $this->assertNull($selectedSchool);
    }

    private function createElteSchool(): School
    {
        return new School(
            'ELTE',
            'IK',
            new Course(
                'Programtervező informatikus',
                new RequiredGraduationSubject(GraduationSubject::Mathematics),
                new RequiredGraduationSubjectCollection([
                    new RequiredGraduationSubject(GraduationSubject::Physics),
                ])
            )
        );
    }

    private function createPpkeSchool(): School
    {
        return new School(
            'PPKE',
            'BTK',
            new Course(
                'Anglisztika',
                new RequiredGraduationSubject(GraduationSubject::EnglishGrammar),
                new RequiredGraduationSubjectCollection([
                    new RequiredGraduationSubject(GraduationSubject::History),
                ])
            )
        );
    }
}
