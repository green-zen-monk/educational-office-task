<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Infrastructure\Repository;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Repository\SchoolRepository;
use InvalidArgumentException;

final readonly class InMemorySchoolRepository implements SchoolRepository
{
    /** @var list<School> */
    private array $schools;

    /**
     * @param iterable<array-key, School> $schools
     */
    public function __construct(iterable $schools)
    {
        $normalizedSchools = [];
        foreach ($schools as $school) {
            if (!$school instanceof School) {
                throw new InvalidArgumentException(
                    'InMemorySchoolRepository expects iterable of School instances.'
                );
            }

            $normalizedSchools[] = $school;
        }

        $this->schools = $normalizedSchools;
    }

    public function findByProgram(string $university, string $faculty, string $course): ?School
    {
        foreach ($this->schools as $school) {
            if ($school->getName() === $university
                && $school->getFaculty() === $faculty
                && $school->getCourse()->getName() === $course
            ) {
                return $school;
            }
        }

        return null;
    }
}
