# Admission Score Calculator (Educational Office Task)

[![CI](https://img.shields.io/github/actions/workflow/status/green-zen-monk/educational-office-task/ci.yml?label=CI)](https://github.com/green-zen-monk/educational-office-task/actions/workflows/ci.yml)
[![PHPStan level: 8](https://img.shields.io/badge/PHPStan-level%208-31C652.svg?logo=php&logoColor=white)](https://phpstan.org/)
[![PHP-CS-Fixer: PSR-12](https://img.shields.io/badge/PHP--CS--Fixer-PSR--12-F7B93E.svg?logo=php&logoColor=white)](https://cs.symfony.com/)

PHP library for calculating Hungarian higher-education admission scores from structured input data.

## Requirements

- PHP `^8.2`

## What The Library Does

- Builds a `Student` domain object from raw input (`CreateStudentFromInput` use case).
- Builds a `School` domain object from raw catalog input (`CreateSchoolFromInput` use case).
- Checks eligibility with rule chain (`EligibilityRule`).
- Calculates scores with pluggable scoring policies (`ScoreEngine`).
- Returns `AdmissionScore` with `basic + bonus = total`.
- Caps bonus score at `100`.
- Stops eligibility evaluation at the first failing rule.

Current default business rules in the provided setup:

- Mandatory subjects must include:
  - `magyar nyelv és irodalom`
  - `történelem`
  - `matematika`
- Every graduation result must be at least `20%`.
- Course required subject must be present (and optionally required at `emelt` level).
- At least one required-selectable subject must be present.
- If a graduation subject appears multiple times, the highest matching result is used.
- High-level (`emelt`) graduation results add `50` bonus points each.
- Language exams add bonus (`B2 = 28`, `C1 = 40`) per language, keeping only the best per language.

## Breaking Changes (Legacy API)

Compared to the previous root orchestration API:

- Root-level orchestration moved into `Application\UseCase\*` classes.
- Core models and business rules moved into `Domain\*`.
- Repository implementations moved into `Infrastructure\*`.
- `ScoreCalculator` was replaced by `Application\UseCase\CalculateAdmissionScore`.
- `StudentFactory` was replaced by `Application\UseCase\CreateStudentFromInput`.
- `SchoolCollection` orchestration was replaced by `Domain\Repository\SchoolRepository` with `Infrastructure\Repository\InMemorySchoolRepository`.
- `ScoreCalculatorException` was replaced by `Application\Exception\CalculateAdmissionScoreException`.
- `StudentFactoryException` was replaced by `Application\Exception\CreateStudentFromInputException`.
- Validator middleware classes were replaced by `EligibilityRule` classes.
- Calculator middleware classes were replaced by `ScoreEngine` + `ScoringPolicy` classes.

## Install

As a dependency:

```bash
composer require green-zen-monk/admission-score-calculator
```

In this repository (development):

```bash
composer install
```

## Public API Overview

- `Application\UseCase\CreateStudentFromInput`: maps raw applicant input to `Domain\Model\Student`.
- `Application\UseCase\CreateSchoolFromInput`: maps raw school catalog input to `Domain\Model\School`.
- `Application\UseCase\CalculateAdmissionScore`: checks eligibility and returns `Domain\Model\Scoring\AdmissionScore`.
- `Application\Input\InputFormat`: selects `array`, `json`, or custom `object` mappers.
- `Infrastructure\Repository\InMemorySchoolRepository`: simple in-memory `SchoolRepository` example for setup and tests; production code can use any adapter, including one backed by an ORM/entity repository.
- `Domain\Eligibility\Contract\EligibilityRule`: chainable eligibility rule contract.
- `Domain\Scoring\ScoreEngine`: runs scoring policies for base and bonus scores.
- `Domain\Model\Scoring\AdmissionScore`: immutable calculation result object.

## Usage Example

```php
<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use GreenZenMonk\AdmissionScoreCalculator\Domain\Eligibility\Rule\GraduationResultMinNotReachRule;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Eligibility\Rule\RequiredDefaultGraduationSubjectsRule;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Eligibility\Rule\RequiredGraduationSubjectRule;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Eligibility\Rule\RequiredSelectableGraduationSubjectsRule;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Graduation\GraduationSubject;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School\Course;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School\Course\RequiredGraduationSubject;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School\Course\RequiredGraduationSubjectCollection;
use GreenZenMonk\AdmissionScoreCalculator\Infrastructure\Repository\InMemorySchoolRepository;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Scoring\Policy\BasicScore\BestRequiredSelectableGraduationSubjectPolicy;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Scoring\Policy\BasicScore\RequiredGraduationSubjectPolicy;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Scoring\Policy\BonusScore\GraduationSubjectTypeHighPolicy;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Scoring\Policy\BonusScore\LanguageExamTypePolicy;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Scoring\ScoreEngine;
use GreenZenMonk\AdmissionScoreCalculator\Application\Exception\CalculateAdmissionScoreException;
use GreenZenMonk\AdmissionScoreCalculator\Application\Exception\CreateStudentFromInputException;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\InputFormat;
use GreenZenMonk\AdmissionScoreCalculator\Application\UseCase\CalculateAdmissionScore;
use GreenZenMonk\AdmissionScoreCalculator\Application\UseCase\CreateStudentFromInput;

$schools = new InMemorySchoolRepository([
    new School(
        'ELTE',
        'IK',
        new Course(
            'Programtervező informatikus',
            new RequiredGraduationSubject(GraduationSubject::Mathematics),
            new RequiredGraduationSubjectCollection([
                new RequiredGraduationSubject(GraduationSubject::Biology),
                new RequiredGraduationSubject(GraduationSubject::Physics),
                new RequiredGraduationSubject(GraduationSubject::InformationTechnology),
                new RequiredGraduationSubject(GraduationSubject::Chemistry),
            ])
        )
    ),
]);

$input = [
    'valasztott-szak' => [
        'egyetem' => 'ELTE',
        'kar' => 'IK',
        'szak' => 'Programtervező informatikus',
    ],
    'erettsegi-eredmenyek' => [
        ['nev' => 'magyar nyelv és irodalom', 'tipus' => 'közép', 'eredmeny' => '70%'],
        ['nev' => 'történelem', 'tipus' => 'közép', 'eredmeny' => '80%'],
        ['nev' => 'matematika', 'tipus' => 'emelt', 'eredmeny' => '90%'],
        ['nev' => 'angol nyelv', 'tipus' => 'közép', 'eredmeny' => '94%'],
        ['nev' => 'informatika', 'tipus' => 'közép', 'eredmeny' => '95%'],
    ],
    'tobbletpontok' => [
        ['kategoria' => 'Nyelvvizsga', 'tipus' => 'B2', 'nyelv' => 'angol'],
        ['kategoria' => 'Nyelvvizsga', 'tipus' => 'C1', 'nyelv' => 'német'],
    ],
];

$createStudent = new CreateStudentFromInput($schools);

$eligibilityRule = new GraduationResultMinNotReachRule();
$eligibilityRule
    ->setNext(new RequiredDefaultGraduationSubjectsRule())
    ->setNext(new RequiredGraduationSubjectRule())
    ->setNext(new RequiredSelectableGraduationSubjectsRule());

$scoreEngine = new ScoreEngine([
    new RequiredGraduationSubjectPolicy(),
    new BestRequiredSelectableGraduationSubjectPolicy(),
    new GraduationSubjectTypeHighPolicy(),
    new LanguageExamTypePolicy(),
]);

$calculateScore = new CalculateAdmissionScore($eligibilityRule, $scoreEngine);

try {
    $student = $createStudent->execute($input, InputFormat::ArrayInput);
    $result = $calculateScore->execute($student);

    print_r([
        'basicScore' => $result->getBasicScore(),
        'bonusScore' => $result->getBonusScore(),
        'totalScore' => $result->getTotalScore(),
    ]);
} catch (CreateStudentFromInputException $e) {
    echo 'Input error: ' . $e->getMessage() . PHP_EOL;
} catch (CalculateAdmissionScoreException $e) {
    echo 'Applicant is not scoreable: ' . $e->getMessage() . PHP_EOL;
}
```

## Student Input Format

`CreateStudentFromInput::execute(mixed $rawInput, InputFormat $format = InputFormat::ArrayInput)`
supports:

- `InputFormat::ArrayInput`: associative array input (default)
- `InputFormat::Json`: JSON string input
- `InputFormat::Object`: requires a custom injected mapper implementation

Required top-level keys:

- `valasztott-szak.egyetem` (`string`)
- `valasztott-szak.kar` (`string`)
- `valasztott-szak.szak` (`string`)
- `erettsegi-eredmenyek` (`list<array>`)
- `tobbletpontok` (`list<array>`)

`erettsegi-eredmenyek[]` fields:

- `nev` (for example: `matematika`, `angol nyelv`, `informatika`)
- `tipus`: `közép` or `emelt`
- `eredmeny`: `0-100` with optional `%` suffix (`85` or `85%`)
- Duplicate subject entries are allowed; scoring uses the highest applicable result.

`tobbletpontok[]` fields:

- `kategoria`: currently `Nyelvvizsga`
- `tipus`: `B2` or `C1`
- `nyelv`: `angol`, `német`, `francia`, `olasz`, `orosz`, `spanyol`

Student input intentionally follows the original homework payload shape and Hungarian field names.
School input uses domain-oriented English keys and enum-like values.

## School Input Format

`CreateSchoolFromInput::execute(mixed $rawInput, InputFormat $format = InputFormat::ArrayInput)`
supports:

- `InputFormat::ArrayInput`: associative array input (default)
- `InputFormat::Json`: JSON string input
- `InputFormat::Object`: requires a custom injected mapper implementation

Required top-level keys:

- `university` (`string`)
- `faculty` (`string`)
- `course.name` (`string`)
- `course.required_graduation_subject` (`array`)
- `course.required_selectable_graduation_subjects` (`list<array>`)

`course.required_graduation_subject` fields:

- `subject`: domain graduation subject value (for example `mathematics`)
- `type`: `medium` or `high`

`course.required_selectable_graduation_subjects[]` fields:

- `subject`: domain graduation subject value (for example `physics`, `biology`)
- `type`: `medium` or `high`

Example payload:

```php
[
    'university' => 'ELTE',
    'faculty' => 'IK',
    'course' => [
        'name' => 'Programtervezo informatikus',
        'required_graduation_subject' => [
            'subject' => 'mathematics',
            'type' => 'medium',
        ],
        'required_selectable_graduation_subjects' => [
            ['subject' => 'physics', 'type' => 'high'],
            ['subject' => 'biology', 'type' => 'medium'],
        ],
    ],
]
```

## Extension Points

- `Domain\Repository\SchoolRepository`: plug in any repository adapter that can return a `School`, including implementations backed by ORM/entity repositories or external services.
- `Application\Input\Mapping\Student\Contract\StudentInputMapperInterface`: add support for custom applicant input formats.
- `Application\Input\Mapping\School\Contract\SchoolInputMapperInterface`: add support for custom school catalog input formats.
- `Application\Contract\ViolationMessageResolver`: customize eligibility error messages returned by `CalculateAdmissionScore`.

## Output

`CalculateAdmissionScore::execute()` returns `AdmissionScore`:

- `getBasicScore(): int`
- `getBonusScore(): int` (`0..100`)
- `getTotalScore(): int`

`CalculateAdmissionScore::check()` returns `EligibilityResult` without calculating score.

`EligibilityResult`:

- `isEligible(): bool`
- `violations(): list<Violation>`

Possible `ViolationCode` values:

- `required_graduation_subject_missing`
- `mandatory_subjects_missing`
- `selectable_subject_missing`
- `subject_below_minimum`

## Exceptions

### `CreateStudentFromInputException`

Thrown when input data is missing or invalid (missing key, wrong structure, invalid subject/type/result format, unknown selected school, unsupported extra point category).

### `CreateSchoolFromInputException`

Thrown when school catalog input is missing or invalid (missing key, wrong structure, invalid graduation subject value, invalid graduation subject type, unsupported mapper format).

### `CalculateAdmissionScoreException`

Thrown by `CalculateAdmissionScore::execute()` when eligibility fails.  
Default exception message is the first violation code value (for example `required_graduation_subject_missing`).
You can override this by passing a custom `ViolationMessageResolver` implementation to `CalculateAdmissionScore`.

## Architecture (Current)

```text
optional school-catalog input (array/json/object)
  |
  v
CreateSchoolFromInput
  - resolves mapper by InputFormat
  - creates Course
  - creates RequiredGraduationSubjectCollection
  |
  v
School
  |
  v
usable by any SchoolRepository implementation

raw input (array/json/object)
  |
  v
CreateStudentFromInput
  - resolves mapper by InputFormat
  - looks up selected program via SchoolRepository
  - accepts any repository implementation that returns a School
  - repository may wrap ORM/entity repositories or other external data sources
  - repository may return preloaded or externally constructed School data
  - creates GraduationResultCollection
  - creates ExtraPointCollection
  |
  v
Student
  |
  v
CalculateAdmissionScore
  |
  +--> Eligibility chain (Chain of Responsibility)
  |      1) GraduationResultMinNotReachRule
  |      2) RequiredDefaultGraduationSubjectsRule
  |      3) RequiredGraduationSubjectRule
  |      4) RequiredSelectableGraduationSubjectsRule
  |
  +--> ScoreEngine policies
         1) RequiredGraduationSubjectPolicy
         2) BestRequiredSelectableGraduationSubjectPolicy
         3) GraduationSubjectTypeHighPolicy
         4) LanguageExamTypePolicy
            -> AdmissionScore (basic, bonus, total)
```

## Development (Docker)

```bash
make docker-build
make docker-up
make composer-install
make test-run
make docker-down
```

Additional targets:

```bash
make test-debug-run
make test-coverage-run
make docker-shell
```

## Testing

Composer scripts:

```bash
composer test
composer test:debug
composer test:coverage
```

PHPUnit configuration:

- `tests/Unit` is registered as the `Unit` suite.
- `tests/Integration` is registered as the `Integration` suite.
- `composer.json` exposes `Tests\\` through `autoload-dev` for the current test namespace layout.

Docker Makefile equivalents:

```bash
make test-run
make test-debug-run
make test-coverage-run
```

Coverage report output (`phpunit.coverage.xml`):

- `build/coverage/html`
- `build/coverage/clover.xml`
- `build/coverage/cobertura.xml`
- `build/coverage/xml`

## Code Quality

PHPStan (`level 8`) and PHP-CS-Fixer (`PSR-12`) are configured.

```bash
make phpstan
make cs-check
make cs-fix
```

Equivalent Composer scripts:

```bash
composer phpstan
composer cs-check
composer cs-fix
```
