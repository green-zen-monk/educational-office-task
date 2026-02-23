# Educational Office Task

[![CI](https://img.shields.io/github/actions/workflow/status/green-zen-monk/educational-office-task/ci.yml?label=CI)](https://github.com/green-zen-monk/educational-office-task/actions/workflows/ci.yml)
[![PHPStan level: 8](https://img.shields.io/badge/PHPStan-level%208-31C652.svg?logo=php&logoColor=white)](https://phpstan.org/)
[![PHP-CS-Fixer: PSR-12](https://img.shields.io/badge/PHP--CS--Fixer-PSR--12-F7B93E.svg?logo=php&logoColor=white)](https://cs.symfony.com/)

A simplified higher-education admission score calculator PHP library.

## What Is This Library For?

This package calculates an applicant's admission score from input data
(selected program, graduation exam results, extra points).

- Validates required and required-selectable subjects.
- Validates the minimum score threshold of 20%.
- Calculates base score and bonus score.
- Caps bonus score at 100 points.

## Quick Install

As a dependency:

```bash
composer require green-zen-monk/educational_office_task
```

In this repository (for development):

```bash
composer install
```

## Full Usage Example

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use GreenZenMonk\SimplifiedScoreCalculator\ScoreCalculator;
use GreenZenMonk\SimplifiedScoreCalculator\ScoreCalculatorException;
use GreenZenMonk\SimplifiedScoreCalculator\StudentFactory;
use GreenZenMonk\SimplifiedScoreCalculator\StudentFactoryException;
use GreenZenMonk\SimplifiedScoreCalculator\School;
use GreenZenMonk\SimplifiedScoreCalculator\SchoolCollection;
use GreenZenMonk\SimplifiedScoreCalculator\SchoolCourse;
use GreenZenMonk\SimplifiedScoreCalculator\GraduationSubject;
use GreenZenMonk\SimplifiedScoreCalculator\SchoolCourse\RequiredGraduationSubject;
use GreenZenMonk\SimplifiedScoreCalculator\SchoolCourse\RequiredGraduationSubjectCollection;
use GreenZenMonk\SimplifiedScoreCalculator\Calculator\Middleware\BasicScore\RequiredGraduationSubjectCalculator;
use GreenZenMonk\SimplifiedScoreCalculator\Calculator\Middleware\BasicScore\BestRequiredSelectableGraduationSubjectCalculator;
use GreenZenMonk\SimplifiedScoreCalculator\Calculator\Middleware\BonusScore\GraduationSubjectTypeHighCalculator;
use GreenZenMonk\SimplifiedScoreCalculator\Calculator\Middleware\BonusScore\LanguageExamTypeCalculator;
use GreenZenMonk\SimplifiedScoreCalculator\Calculator\Validator\GraduationResultMinNotReachValidator;
use GreenZenMonk\SimplifiedScoreCalculator\Calculator\Validator\RequiredDefaultGraduationSubjectsValidator;
use GreenZenMonk\SimplifiedScoreCalculator\Calculator\Validator\RequiredGraduationSubjectValidator;
use GreenZenMonk\SimplifiedScoreCalculator\Calculator\Validator\RequiredSelectableGraduationSubjectsValidator;

// School catalog definition.
$schools = new SchoolCollection([
    new School(
        'ELTE',
        'IK',
        new SchoolCourse(
            'Programtervező informatikus',
            new RequiredGraduationSubject(GraduationSubject::MATHEMATICS),
            new RequiredGraduationSubjectCollection([
                new RequiredGraduationSubject(GraduationSubject::BIOLOGY),
                new RequiredGraduationSubject(GraduationSubject::PHYSICS),
                new RequiredGraduationSubject(GraduationSubject::IT),
                new RequiredGraduationSubject(GraduationSubject::CHEMISTRY),
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

$factory = new StudentFactory($schools);

$validator = new GraduationResultMinNotReachValidator();
$validator->linkWith(new RequiredDefaultGraduationSubjectsValidator())
    ->linkWith(new RequiredGraduationSubjectValidator())
    ->linkWith(new RequiredSelectableGraduationSubjectsValidator());

$middleware = new RequiredGraduationSubjectCalculator();
$middleware->linkWith(new BestRequiredSelectableGraduationSubjectCalculator())
    ->linkWith(new GraduationSubjectTypeHighCalculator())
    ->linkWith(new LanguageExamTypeCalculator());

$calculator = new ScoreCalculator($validator, $middleware);

try {
    $student = $factory->create($input);
    $result = $calculator->calculate($student);

    print_r([
        'basicScore' => $result->getBasicScore(),
        'bonusScore' => $result->getBonusScore(),
        'totalScore' => $result->getTotalScore(),
    ]);
} catch (StudentFactoryException $e) {
    echo 'Input error: ' . $e->getMessage() . PHP_EOL;
} catch (ScoreCalculatorException $e) {
    echo 'Applicant is not scoreable: ' . $e->getMessage() . PHP_EOL;
}
```

## Input/Output Format

### Input (`array`)

Required top-level keys:

- `valasztott-szak.egyetem` (`string`)
- `valasztott-szak.kar` (`string`)
- `valasztott-szak.szak` (`string`)
- `erettsegi-eredmenyek` (`array`)
- `tobbletpontok` (`array`)

`erettsegi-eredmenyek[]` items:

- `nev`: subject name (for example `matematika`, `angol nyelv`, `informatika`)
- `tipus`: `közép` or `emelt`
- `eredmeny`: percentage value (for example `85%`, `85`)

`tobbletpontok[]` items:

- `kategoria`: currently `Nyelvvizsga`
- `tipus`: `B2` or `C1`
- `nyelv`: for example `angol`, `német`, `francia`

### Output (successful calculation)

`ScoreCalculator::calculate()` returns a `CalculatorResult` object:

- `getBasicScore(): int`
- `getBonusScore(): int` (max. 100)
- `getTotalScore(): int`

## Exception Handling

### `StudentFactoryException`

`StudentFactory::create()` throws this when input structure is missing or invalid
(for example missing keys or invalid nested structure).

### `ScoreCalculatorException`

`ScoreCalculator::calculate()` throws this when the applicant fails validation.
The exception message is the validator error message.

Note: Invalid enum values (for example unknown subject or unsupported type)
can raise native PHP `ValueError`.
If the selected program does not exist in `SchoolCollection`, factory flow may
later fail with `TypeError`.

## Architecture (validator + middleware pipeline)

```text
raw input array
  |
  v
StudentFactory.php
  - selects School by (egyetem/kar/szak)
  - creates GraduationResultCollection
  - creates LanguageExamExtraPointCollection
  |
  v
Student
  |
  v
ScoreCalculator.php
  |
  +--> Validator chain (Chain of Responsibility)
  |      1) GraduationResultMinNotReachValidator
  |      2) RequiredDefaultGraduationSubjectsValidator
  |      3) RequiredGraduationSubjectValidator
  |      4) RequiredSelectableGraduationSubjectsValidator
  |         -> if any step fails: ScoreCalculatorException
  |
  +--> Middleware chain (accumulative calculation pipeline)
         1) RequiredGraduationSubjectCalculator
         2) BestRequiredSelectableGraduationSubjectCalculator
         3) GraduationSubjectTypeHighCalculator
         4) LanguageExamTypeCalculator
            -> CalculatorResult (basic, bonus, total)
```

## Developer Run With Docker

```bash
make docker-up
make composer-install
make test-run
make docker-down
```

## Code Quality

PHPStan (`level 8`) and PHP-CS-Fixer (`PSR-12`) are configured in this
repository.

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
