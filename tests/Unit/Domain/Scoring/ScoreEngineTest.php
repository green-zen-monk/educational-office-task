<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Scoring;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Graduation\GraduationSubject;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School\Course;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School\Course\RequiredGraduationSubject;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School\Course\RequiredGraduationSubjectCollection;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\ExtraPointCollection;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\GraduationResultCollection;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Scoring\Contract\ScoringPolicy;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Scoring\ScoreAccumulator;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Scoring\ScoreEngine;
use PHPUnit\Framework\TestCase;
use stdClass;

class ScoreEngineTest extends TestCase
{
    public function testReturnsZeroScoreWhenNoPoliciesAreProvided(): void
    {
        $engine = new ScoreEngine([]);

        $result = $engine->calculate($this->createStudent());

        $this->assertSame(0, $result->getBasicScore());
        $this->assertSame(0, $result->getBonusScore());
        $this->assertSame(0, $result->getTotalScore());
    }

    public function testAggregatesScoreFromAllPolicies(): void
    {
        $engine = new ScoreEngine([
            new class () implements ScoringPolicy {
                public function apply(Student $student, ScoreAccumulator $accumulator): void
                {
                    $accumulator->addBasicScore(120);
                }
            },
            new class () implements ScoringPolicy {
                public function apply(Student $student, ScoreAccumulator $accumulator): void
                {
                    $accumulator->addBasicScore(80);
                    $accumulator->addBonusScore(75);
                }
            },
            new class () implements ScoringPolicy {
                public function apply(Student $student, ScoreAccumulator $accumulator): void
                {
                    $accumulator->addBonusScore(50);
                }
            },
        ]);

        $result = $engine->calculate($this->createStudent());

        $this->assertSame(200, $result->getBasicScore());
        $this->assertSame(100, $result->getBonusScore());
        $this->assertSame(300, $result->getTotalScore());
    }

    public function testAppliesPoliciesInGivenOrder(): void
    {
        $state = new stdClass();
        $state->order = [];

        $engine = new ScoreEngine([
            new class ($state) implements ScoringPolicy {
                public function __construct(private stdClass $state)
                {
                }

                public function apply(Student $student, ScoreAccumulator $accumulator): void
                {
                    $this->state->order[] = 'first';
                }
            },
            new class ($state) implements ScoringPolicy {
                public function __construct(private stdClass $state)
                {
                }

                public function apply(Student $student, ScoreAccumulator $accumulator): void
                {
                    $this->state->order[] = 'second';
                }
            },
        ]);

        $engine->calculate($this->createStudent());

        $this->assertSame(['first', 'second'], $state->order);
    }

    private function createStudent(): Student
    {
        return new Student(
            new School(
                'ELTE',
                'IK',
                new Course(
                    'Programtervező informatikus',
                    new RequiredGraduationSubject(GraduationSubject::Mathematics),
                    new RequiredGraduationSubjectCollection([
                        new RequiredGraduationSubject(GraduationSubject::Physics),
                    ])
                )
            ),
            new GraduationResultCollection(),
            new ExtraPointCollection()
        );
    }
}
