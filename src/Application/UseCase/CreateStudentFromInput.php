<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Application\UseCase;

use GreenZenMonk\AdmissionScoreCalculator\Application\Exception\CreateStudentFromInputException;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Dto\ExtraPointInput;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Dto\GraduationResultInput;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Dto\SelectedProgramInput;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Dto\StudentInput;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\InputFormat;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\Student\Contract\StudentInputMapperInterface;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\Student\Homework\HomeworkStudentInputMapper;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\Student\JsonStudentInputMapper;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\Student\StudentInputMapperChain;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\ExtraPoint\LanguageExamExtraPoint;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\ExtraPointCollection;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\GraduationResult;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\GraduationResultCollection;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Repository\SchoolRepository;
use InvalidArgumentException;

final class CreateStudentFromInput
{
    private StudentInputMapperChain $inputMapperChain;

    /**
     * @param iterable<array-key, StudentInputMapperInterface> $mappers
     */
    public function __construct(
        private SchoolRepository $schools,
        iterable $mappers = []
    ) {
        $normalizedMappers = [];
        foreach ($mappers as $mapper) {
            if (!$mapper instanceof StudentInputMapperInterface) {
                throw new InvalidArgumentException(
                    'CreateStudentFromInput expects iterable of StudentInputMapperInterface instances.'
                );
            }

            $normalizedMappers[] = $mapper;
        }

        if ($normalizedMappers === []) {
            $arrayMapper = new HomeworkStudentInputMapper();
            $normalizedMappers = [
                $arrayMapper,
                new JsonStudentInputMapper($arrayMapper),
            ];
        }

        $this->inputMapperChain = new StudentInputMapperChain($normalizedMappers);
    }

    /**
     * @throws CreateStudentFromInputException
     */
    public function execute(mixed $rawInput, InputFormat $format = InputFormat::ArrayInput): Student
    {
        $studentInput = $this->inputMapperChain->map($format, $rawInput);

        $school = $this->findSelectedSchool($studentInput->getSelectedProgram());
        if ($school === null) {
            throw new CreateStudentFromInputException('Has no selected school!');
        }

        return new Student(
            $school,
            $this->createGraduationResultCollection($studentInput),
            $this->createExtraPointCollection($studentInput)
        );
    }

    private function findSelectedSchool(SelectedProgramInput $selectedProgram): ?School
    {
        return $this->schools->findByProgram(
            $selectedProgram->getUniversity(),
            $selectedProgram->getFaculty(),
            $selectedProgram->getCourse()
        );
    }

    private function createGraduationResultCollection(StudentInput $studentInput): GraduationResultCollection
    {
        $collection = new GraduationResultCollection();
        foreach ($studentInput->getGraduationResults() as $graduationResult) {
            $collection[] = $this->createGraduationResult($graduationResult);
        }

        return $collection;
    }

    private function createGraduationResult(GraduationResultInput $graduationResult): GraduationResult
    {
        return new GraduationResult(
            $graduationResult->getSubject(),
            $graduationResult->getSubjectType(),
            $graduationResult->getResult()
        );
    }

    private function createExtraPointCollection(StudentInput $studentInput): ExtraPointCollection
    {
        $collection = new ExtraPointCollection();
        foreach ($studentInput->getExtraPoints() as $extraPointInput) {
            $collection[] = $this->createLanguageExamExtraPoint($extraPointInput);
        }

        return $collection;
    }

    private function createLanguageExamExtraPoint(ExtraPointInput $extraPointInput): LanguageExamExtraPoint
    {
        return new LanguageExamExtraPoint(
            $extraPointInput->getLanguage(),
            $extraPointInput->getType()
        );
    }
}
