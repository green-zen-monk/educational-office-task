<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Model;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Graduation\GraduationSubject;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School\Course;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School\Course\RequiredGraduationSubject;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School\Course\RequiredGraduationSubjectCollection;
use PHPUnit\Framework\TestCase;

class SchoolTest extends TestCase
{
    public function testExposesNameFacultyAndCourse(): void
    {
        $course = new Course(
            'Programtervező informatikus',
            new RequiredGraduationSubject(GraduationSubject::Mathematics),
            new RequiredGraduationSubjectCollection([
                new RequiredGraduationSubject(GraduationSubject::Physics),
            ])
        );
        $school = new School('ELTE', 'IK', $course);

        $this->assertSame('ELTE', $school->getName());
        $this->assertSame('IK', $school->getFaculty());
        $this->assertSame($course, $school->getCourse());
    }

    public function testGetTitleReturnsFormattedValue(): void
    {
        $school = new School(
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

        $this->assertSame('ELTE IK - Programtervező informatikus', $school->getTitle());
    }
}
