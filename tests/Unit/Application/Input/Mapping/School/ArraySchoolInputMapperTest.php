<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Input\Mapping\School;

use GreenZenMonk\AdmissionScoreCalculator\Application\Exception\CreateSchoolFromInputException;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\School\ArraySchoolInputMapper;
use PHPUnit\Framework\TestCase;

class ArraySchoolInputMapperTest extends TestCase
{
    public function testMapBuildsSchoolInputFromValidArray(): void
    {
        $mapper = new ArraySchoolInputMapper();

        $actual = $mapper->map([
            'university' => 'ELTE',
            'faculty' => 'IK',
            'course' => [
                'name' => 'Programtervezo informatikus',
                'required_graduation_subject' => [
                    'subject' => 'mathematics',
                    'type' => 'medium',
                ],
                'required_selectable_graduation_subjects' => [
                    [
                        'subject' => 'physics',
                        'type' => 'high',
                    ],
                ],
            ],
        ]);

        $this->assertSame('ELTE', $actual->getUniversity());
        $this->assertSame('IK', $actual->getFaculty());
        $this->assertSame('Programtervezo informatikus', $actual->getCourse()->getName());
        $this->assertSame(
            'mathematics',
            $actual->getCourse()->getRequiredGraduationSubject()->getSubject()->value
        );
        $this->assertSame(
            'medium',
            $actual->getCourse()->getRequiredGraduationSubject()->getSubjectType()->value
        );
        $this->assertCount(1, $actual->getCourse()->getRequiredSelectableGraduationSubjects());
        $this->assertSame(
            'physics',
            $actual->getCourse()->getRequiredSelectableGraduationSubjects()[0]->getSubject()->value
        );
        $this->assertSame(
            'high',
            $actual->getCourse()->getRequiredSelectableGraduationSubjects()[0]->getSubjectType()->value
        );
    }

    public function testMapThrowsWhenRequiredKeyIsMissing(): void
    {
        $mapper = new ArraySchoolInputMapper();

        $this->expectException(CreateSchoolFromInputException::class);
        $this->expectExceptionMessage('The provided key does not exist: faculty');

        $mapper->map([
            'university' => 'ELTE',
            'course' => [
                'name' => 'Programtervezo informatikus',
                'required_graduation_subject' => [
                    'subject' => 'mathematics',
                    'type' => 'medium',
                ],
                'required_selectable_graduation_subjects' => [],
            ],
        ]);
    }

    public function testMapThrowsWhenFieldHasInvalidType(): void
    {
        $mapper = new ArraySchoolInputMapper();

        $this->expectException(CreateSchoolFromInputException::class);
        $this->expectExceptionMessage('The provided key does not point to a string value: university');

        $mapper->map([
            'university' => ['ELTE'],
            'faculty' => 'IK',
            'course' => [
                'name' => 'Programtervezo informatikus',
                'required_graduation_subject' => [
                    'subject' => 'mathematics',
                    'type' => 'medium',
                ],
                'required_selectable_graduation_subjects' => [],
            ],
        ]);
    }

    public function testMapThrowsWhenGraduationSubjectIsInvalid(): void
    {
        $mapper = new ArraySchoolInputMapper();

        $this->expectException(CreateSchoolFromInputException::class);
        $this->expectExceptionMessage(
            'Invalid graduation subject. Value: invalid-subject Path: course.required_graduation_subject.subject'
        );

        $mapper->map([
            'university' => 'ELTE',
            'faculty' => 'IK',
            'course' => [
                'name' => 'Programtervezo informatikus',
                'required_graduation_subject' => [
                    'subject' => 'invalid-subject',
                    'type' => 'medium',
                ],
                'required_selectable_graduation_subjects' => [],
            ],
        ]);
    }

    public function testMapThrowsWhenGraduationSubjectTypeIsInvalid(): void
    {
        $mapper = new ArraySchoolInputMapper();

        $this->expectException(CreateSchoolFromInputException::class);
        $this->expectExceptionMessage(
            'Invalid graduation subject type. Value: invalid-type Path: course.required_graduation_subject.type'
        );

        $mapper->map([
            'university' => 'ELTE',
            'faculty' => 'IK',
            'course' => [
                'name' => 'Programtervezo informatikus',
                'required_graduation_subject' => [
                    'subject' => 'mathematics',
                    'type' => 'invalid-type',
                ],
                'required_selectable_graduation_subjects' => [],
            ],
        ]);
    }
}
