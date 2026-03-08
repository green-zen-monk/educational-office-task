<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\School;

use GreenZenMonk\AdmissionScoreCalculator\Application\Exception\CreateSchoolFromInputException;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Dto\CourseInput;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Dto\RequiredGraduationSubjectInput;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Dto\SchoolInput;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\InputFormat;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\School\Contract\SchoolInputMapperInterface;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\School\Translator\GraduationSubjectTypeValueTranslator;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\School\Translator\GraduationSubjectValueTranslator;
use InvalidArgumentException;

final class ArraySchoolInputMapper implements SchoolInputMapperInterface
{
    public function __construct(
        private readonly GraduationSubjectValueTranslator $graduationSubjectTranslator = new GraduationSubjectValueTranslator(),
        private readonly GraduationSubjectTypeValueTranslator $graduationSubjectTypeTranslator = new GraduationSubjectTypeValueTranslator()
    ) {
    }

    public function supports(InputFormat $format, mixed $rawInput): bool
    {
        return $format === InputFormat::ArrayInput && is_array($rawInput);
    }

    /**
     * @throws CreateSchoolFromInputException
     */
    public function map(mixed $rawInput): SchoolInput
    {
        if (!is_array($rawInput)) {
            throw new CreateSchoolFromInputException(
                'Expected array input for format: ' . InputFormat::ArrayInput->value
            );
        }

        if ($rawInput === []) {
            throw new CreateSchoolFromInputException('Has no data to create school!');
        }

        $courseData = $this->requireArrayValue($rawInput, 'course', 'course');

        return new SchoolInput(
            $this->requireStringValue($rawInput, 'university', 'university'),
            $this->requireStringValue($rawInput, 'faculty', 'faculty'),
            new CourseInput(
                $this->requireStringValue($courseData, 'name', 'course.name'),
                $this->mapRequiredGraduationSubject(
                    $this->requireArrayValue(
                        $courseData,
                        'required_graduation_subject',
                        'course.required_graduation_subject'
                    ),
                    'course.required_graduation_subject'
                ),
                $this->mapRequiredSelectableGraduationSubjects($courseData)
            )
        );
    }

    /**
     * @param array<array-key, mixed> $courseData
     * @return list<RequiredGraduationSubjectInput>
     * @throws CreateSchoolFromInputException
     */
    private function mapRequiredSelectableGraduationSubjects(array $courseData): array
    {
        $requiredSelectableGraduationSubjects = [];
        $subjectsData = $this->requireListValue(
            $courseData,
            'required_selectable_graduation_subjects',
            'course.required_selectable_graduation_subjects'
        );

        foreach ($subjectsData as $index => $subjectData) {
            if (!is_array($subjectData)) {
                throw new CreateSchoolFromInputException(
                    'The provided key does not point to an array value: '
                    . 'course.required_selectable_graduation_subjects[' . $index . ']'
                );
            }

            $requiredSelectableGraduationSubjects[] = $this->mapRequiredGraduationSubject(
                $subjectData,
                'course.required_selectable_graduation_subjects[' . $index . ']'
            );
        }

        return $requiredSelectableGraduationSubjects;
    }

    /**
     * @param array<array-key, mixed> $subjectData
     * @throws CreateSchoolFromInputException
     */
    private function mapRequiredGraduationSubject(array $subjectData, string $path): RequiredGraduationSubjectInput
    {
        $subjectValue = $this->requireStringValue($subjectData, 'subject', $path . '.subject');
        $subjectTypeValue = $this->requireStringValue($subjectData, 'type', $path . '.type');

        try {
            $subject = $this->graduationSubjectTranslator->map($subjectValue);
        } catch (InvalidArgumentException) {
            throw new CreateSchoolFromInputException(
                'Invalid graduation subject. Value: ' . $subjectValue . ' Path: ' . $path . '.subject'
            );
        }

        try {
            $subjectType = $this->graduationSubjectTypeTranslator->map($subjectTypeValue);
        } catch (InvalidArgumentException) {
            throw new CreateSchoolFromInputException(
                'Invalid graduation subject type. Value: ' . $subjectTypeValue . ' Path: ' . $path . '.type'
            );
        }

        return new RequiredGraduationSubjectInput($subject, $subjectType);
    }

    /**
     * @param array<array-key, mixed> $data
     * @return array<array-key, mixed>
     * @throws CreateSchoolFromInputException
     */
    private function requireArrayValue(array $data, string $key, string $path): array
    {
        $value = $this->requireValue($data, $key, $path);

        if (!is_array($value)) {
            throw new CreateSchoolFromInputException(
                'The provided key does not point to an array value: ' . $path
            );
        }

        return $value;
    }

    /**
     * @param array<array-key, mixed> $data
     * @return list<mixed>
     * @throws CreateSchoolFromInputException
     */
    private function requireListValue(array $data, string $key, string $path): array
    {
        $value = $this->requireArrayValue($data, $key, $path);

        return array_values($value);
    }

    /**
     * @param array<array-key, mixed> $data
     * @throws CreateSchoolFromInputException
     */
    private function requireStringValue(array $data, string $key, string $path): string
    {
        $value = $this->requireValue($data, $key, $path);

        if (!is_string($value)) {
            throw new CreateSchoolFromInputException(
                'The provided key does not point to a string value: ' . $path
            );
        }

        return $value;
    }

    /**
     * @param array<array-key, mixed> $data
     * @throws CreateSchoolFromInputException
     */
    private function requireValue(array $data, string $key, string $path): mixed
    {
        if (!array_key_exists($key, $data)) {
            throw new CreateSchoolFromInputException('The provided key does not exist: ' . $path);
        }

        return $data[$key];
    }
}
