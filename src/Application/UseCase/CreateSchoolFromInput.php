<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Application\UseCase;

use GreenZenMonk\AdmissionScoreCalculator\Application\Exception\CreateSchoolFromInputException;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Dto\CourseInput;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Dto\RequiredGraduationSubjectInput;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\InputFormat;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\School\ArraySchoolInputMapper;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\School\Contract\SchoolInputMapperInterface;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\School\JsonSchoolInputMapper;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\School\SchoolInputMapperChain;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School\Course;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School\Course\RequiredGraduationSubject;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School\Course\RequiredGraduationSubjectCollection;
use InvalidArgumentException;

final class CreateSchoolFromInput
{
    private SchoolInputMapperChain $inputMapperChain;

    /**
     * @param iterable<array-key, SchoolInputMapperInterface> $mappers
     */
    public function __construct(iterable $mappers = [])
    {
        $normalizedMappers = [];
        foreach ($mappers as $mapper) {
            if (!$mapper instanceof SchoolInputMapperInterface) {
                throw new InvalidArgumentException(
                    'CreateSchoolFromInput expects iterable of SchoolInputMapperInterface instances.'
                );
            }

            $normalizedMappers[] = $mapper;
        }

        if ($normalizedMappers === []) {
            $arrayMapper = new ArraySchoolInputMapper();
            $normalizedMappers = [
                $arrayMapper,
                new JsonSchoolInputMapper($arrayMapper),
            ];
        }

        $this->inputMapperChain = new SchoolInputMapperChain($normalizedMappers);
    }

    /**
     * @throws CreateSchoolFromInputException
     */
    public function execute(mixed $rawInput, InputFormat $format = InputFormat::ArrayInput): School
    {
        $schoolInput = $this->inputMapperChain->map($format, $rawInput);

        return new School(
            $schoolInput->getUniversity(),
            $schoolInput->getFaculty(),
            $this->createCourse($schoolInput->getCourse())
        );
    }

    private function createCourse(CourseInput $courseInput): Course
    {
        return new Course(
            $courseInput->getName(),
            $this->createRequiredGraduationSubject($courseInput->getRequiredGraduationSubject()),
            $this->createRequiredGraduationSubjectCollection($courseInput)
        );
    }

    private function createRequiredGraduationSubject(
        RequiredGraduationSubjectInput $requiredGraduationSubjectInput
    ): RequiredGraduationSubject {
        return new RequiredGraduationSubject(
            $requiredGraduationSubjectInput->getSubject(),
            $requiredGraduationSubjectInput->getSubjectType()
        );
    }

    private function createRequiredGraduationSubjectCollection(
        CourseInput $courseInput
    ): RequiredGraduationSubjectCollection {
        $collection = new RequiredGraduationSubjectCollection();
        foreach ($courseInput->getRequiredSelectableGraduationSubjects() as $requiredGraduationSubjectInput) {
            $collection[] = $this->createRequiredGraduationSubject($requiredGraduationSubjectInput);
        }

        return $collection;
    }
}
