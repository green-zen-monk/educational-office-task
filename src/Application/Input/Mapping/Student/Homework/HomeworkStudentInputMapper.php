<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\Student\Homework;

use GreenZenMonk\AdmissionScoreCalculator\Application\Exception\CreateStudentFromInputException;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Dto\ExtraPointInput;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Dto\GraduationResultInput;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Dto\SelectedProgramInput;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Dto\StudentInput;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\InputFormat;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\Student\Contract\StudentInputMapperInterface;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\Student\Homework\Translator\ExtraPointCategoryTranslator;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\Student\Homework\Translator\GraduationSubjectTranslator;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\Student\Homework\Translator\GraduationSubjectTypeTranslator;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\Student\Homework\Translator\LanguageExamTypeTranslator;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\Student\Homework\Translator\LanguageTranslator;
use InvalidArgumentException;

final class HomeworkStudentInputMapper implements StudentInputMapperInterface
{
    public function __construct(
        private readonly GraduationSubjectTranslator $graduationSubjectMapper = new GraduationSubjectTranslator(),
        private readonly GraduationSubjectTypeTranslator $graduationSubjectTypeMapper = new GraduationSubjectTypeTranslator(),
        private readonly ExtraPointCategoryTranslator $extraPointCategoryMapper = new ExtraPointCategoryTranslator(),
        private readonly LanguageTranslator $languageMapper = new LanguageTranslator(),
        private readonly LanguageExamTypeTranslator $languageExamTypeMapper = new LanguageExamTypeTranslator()
    ) {
    }

    public function supports(InputFormat $format, mixed $rawInput): bool
    {
        return $format === InputFormat::ArrayInput && is_array($rawInput);
    }

    /**
     * @throws CreateStudentFromInputException
     */
    public function map(mixed $rawInput): StudentInput
    {
        if (!is_array($rawInput)) {
            throw new CreateStudentFromInputException(
                'Expected array input for format: ' . InputFormat::ArrayInput->value
            );
        }

        if ($rawInput === []) {
            throw new CreateStudentFromInputException('Has no data to create student!');
        }

        $selectedProgramData = $this->requireArrayValue(
            $rawInput,
            HomeworkInputKeys::SELECTED_PROGRAM,
            HomeworkInputKeys::SELECTED_PROGRAM
        );

        $selectedProgram = new SelectedProgramInput(
            $this->requireStringValue(
                $selectedProgramData,
                HomeworkInputKeys::SELECTED_PROGRAM_UNIVERSITY,
                HomeworkInputKeys::SELECTED_PROGRAM . '.' . HomeworkInputKeys::SELECTED_PROGRAM_UNIVERSITY
            ),
            $this->requireStringValue(
                $selectedProgramData,
                HomeworkInputKeys::SELECTED_PROGRAM_FACULTY,
                HomeworkInputKeys::SELECTED_PROGRAM . '.' . HomeworkInputKeys::SELECTED_PROGRAM_FACULTY
            ),
            $this->requireStringValue(
                $selectedProgramData,
                HomeworkInputKeys::SELECTED_PROGRAM_COURSE,
                HomeworkInputKeys::SELECTED_PROGRAM . '.' . HomeworkInputKeys::SELECTED_PROGRAM_COURSE
            )
        );

        return new StudentInput(
            $selectedProgram,
            $this->mapGraduationResults($rawInput),
            $this->mapExtraPoints($rawInput)
        );
    }

    /**
     * @param array<array-key, mixed> $rawInput
     * @return list<GraduationResultInput>
     * @throws CreateStudentFromInputException
     */
    private function mapGraduationResults(array $rawInput): array
    {
        $graduationResultsData = $this->requireListValue(
            $rawInput,
            HomeworkInputKeys::GRADUATION_RESULTS,
            HomeworkInputKeys::GRADUATION_RESULTS
        );

        $graduationResults = [];
        foreach ($graduationResultsData as $index => $graduationResultData) {
            if (!is_array($graduationResultData)) {
                throw new CreateStudentFromInputException(
                    'The provided key does not point to an array value: ' . HomeworkInputKeys::GRADUATION_RESULTS . '[' . $index . ']'
                );
            }

            $subjectName = $this->requireStringValue(
                $graduationResultData,
                HomeworkInputKeys::GRADUATION_RESULT_SUBJECT,
                HomeworkInputKeys::GRADUATION_RESULTS . '[' . $index . '].' . HomeworkInputKeys::GRADUATION_RESULT_SUBJECT
            );
            $subjectTypeName = $this->requireStringValue(
                $graduationResultData,
                HomeworkInputKeys::GRADUATION_RESULT_TYPE,
                HomeworkInputKeys::GRADUATION_RESULTS . '[' . $index . '].' . HomeworkInputKeys::GRADUATION_RESULT_TYPE
            );
            $scoreValue = $this->requireStringOrIntValue(
                $graduationResultData,
                HomeworkInputKeys::GRADUATION_RESULT_SCORE,
                HomeworkInputKeys::GRADUATION_RESULTS . '[' . $index . '].' . HomeworkInputKeys::GRADUATION_RESULT_SCORE
            );

            try {
                $subject = $this->graduationSubjectMapper->map($subjectName);
            } catch (InvalidArgumentException) {
                throw new CreateStudentFromInputException(
                    'Invalid graduation subject. Value: ' . $subjectName
                    . ' Path: ' . HomeworkInputKeys::GRADUATION_RESULTS . '[' . $index . '].' . HomeworkInputKeys::GRADUATION_RESULT_SUBJECT
                );
            }

            try {
                $subjectType = $this->graduationSubjectTypeMapper->map($subjectTypeName);
            } catch (InvalidArgumentException) {
                throw new CreateStudentFromInputException(
                    'Invalid graduation subject type. Value: ' . $subjectTypeName
                    . ' Path: ' . HomeworkInputKeys::GRADUATION_RESULTS . '[' . $index . '].' . HomeworkInputKeys::GRADUATION_RESULT_TYPE
                );
            }

            $normalizedScoreValue = is_int($scoreValue) ? (string) $scoreValue : $scoreValue;
            if (preg_match('/^(100|[1-9]?\d)%$/', $normalizedScoreValue) === 0) {
                throw new CreateStudentFromInputException(
                    'Invalid graduation result value. Value: ' . $normalizedScoreValue
                    . ' Path: ' . HomeworkInputKeys::GRADUATION_RESULTS . '[' . $index . '].' . HomeworkInputKeys::GRADUATION_RESULT_SCORE
                );
            }

            $graduationResults[] = new GraduationResultInput(
                $subject,
                $subjectType,
                intval($normalizedScoreValue)
            );
        }

        return $graduationResults;
    }

    /**
     * @param array<array-key, mixed> $rawInput
     * @return list<ExtraPointInput>
     * @throws CreateStudentFromInputException
     */
    private function mapExtraPoints(array $rawInput): array
    {
        $extraPointsData = $this->requireListValue(
            $rawInput,
            HomeworkInputKeys::EXTRA_POINTS,
            HomeworkInputKeys::EXTRA_POINTS
        );

        $extraPoints = [];
        foreach ($extraPointsData as $index => $extraPointData) {
            if (!is_array($extraPointData)) {
                throw new CreateStudentFromInputException(
                    'The provided key does not point to an array value: ' . HomeworkInputKeys::EXTRA_POINTS . '[' . $index . ']'
                );
            }

            $categoryValue = $this->requireStringValue(
                $extraPointData,
                HomeworkInputKeys::EXTRA_POINT_CATEGORY,
                HomeworkInputKeys::EXTRA_POINTS . '[' . $index . '].' . HomeworkInputKeys::EXTRA_POINT_CATEGORY
            );
            try {
                $category = $this->extraPointCategoryMapper->map($categoryValue);
            } catch (InvalidArgumentException) {
                throw new CreateStudentFromInputException(
                    'Invalid extra point category. Value: ' . $categoryValue
                    . ' Path: ' . HomeworkInputKeys::EXTRA_POINTS . '[' . $index . '].' . HomeworkInputKeys::EXTRA_POINT_CATEGORY
                );
            }

            $languageValue = $this->requireStringValue(
                $extraPointData,
                HomeworkInputKeys::EXTRA_POINT_LANGUAGE,
                HomeworkInputKeys::EXTRA_POINTS . '[' . $index . '].' . HomeworkInputKeys::EXTRA_POINT_LANGUAGE
            );
            try {
                $language = $this->languageMapper->map($languageValue);
            } catch (InvalidArgumentException) {
                throw new CreateStudentFromInputException(
                    'Invalid extra point language. Value: ' . $languageValue
                    . ' Path: ' . HomeworkInputKeys::EXTRA_POINTS . '[' . $index . '].' . HomeworkInputKeys::EXTRA_POINT_LANGUAGE
                );
            }

            $typeValue = $this->requireStringValue(
                $extraPointData,
                HomeworkInputKeys::EXTRA_POINT_TYPE,
                HomeworkInputKeys::EXTRA_POINTS . '[' . $index . '].' . HomeworkInputKeys::EXTRA_POINT_TYPE
            );
            try {
                $type = $this->languageExamTypeMapper->map($typeValue);
            } catch (InvalidArgumentException) {
                throw new CreateStudentFromInputException(
                    'Invalid extra point type. Value: ' . $typeValue
                    . ' Path: ' . HomeworkInputKeys::EXTRA_POINTS . '[' . $index . '].' . HomeworkInputKeys::EXTRA_POINT_TYPE
                );
            }

            $extraPoints[] = new ExtraPointInput(
                $category,
                $language,
                $type
            );
        }

        return $extraPoints;
    }

    /**
     * @param array<array-key, mixed> $data
     * @return array<array-key, mixed>
     * @throws CreateStudentFromInputException
     */
    private function requireArrayValue(array $data, string $key, string $path): array
    {
        $value = $this->requireValue($data, $key, $path);

        if (!is_array($value)) {
            throw new CreateStudentFromInputException(
                'The provided key does not point to an array value: ' . $path
            );
        }

        return $value;
    }

    /**
     * @param array<array-key, mixed> $data
     * @return list<mixed>
     * @throws CreateStudentFromInputException
     */
    private function requireListValue(array $data, string $key, string $path): array
    {
        $value = $this->requireArrayValue($data, $key, $path);

        return array_values($value);
    }

    /**
     * @param array<array-key, mixed> $data
     * @throws CreateStudentFromInputException
     */
    private function requireStringValue(array $data, string $key, string $path): string
    {
        $value = $this->requireValue($data, $key, $path);

        if (!is_string($value)) {
            throw new CreateStudentFromInputException(
                'The provided key does not point to a string value: ' . $path
            );
        }

        return $value;
    }

    /**
     * @param array<array-key, mixed> $data
     * @return int|string
     * @throws CreateStudentFromInputException
     */
    private function requireStringOrIntValue(array $data, string $key, string $path): int|string
    {
        $value = $this->requireValue($data, $key, $path);

        if (!is_string($value) && !is_int($value)) {
            throw new CreateStudentFromInputException(
                'The provided key does not point to a string or int value: ' . $path
            );
        }

        return $value;
    }

    /**
     * @param array<array-key, mixed> $data
     * @throws CreateStudentFromInputException
     */
    private function requireValue(array $data, string $key, string $path): mixed
    {
        if (!array_key_exists($key, $data)) {
            throw new CreateStudentFromInputException('The provided key does not exist: ' . $path);
        }

        return $data[$key];
    }
}
