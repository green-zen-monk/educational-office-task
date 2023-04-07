<?php

use GreenZenMonk\SimplifiedScoreCalculator\Calculator\CalculatorResult;
use GreenZenMonk\SimplifiedScoreCalculator\Calculator\ValidatorResult;
use GreenZenMonk\SimplifiedScoreCalculator\Student;

$schools = require __DIR__ . '/schools.php';

$studentBuilder = Student::builder($schools);

return [
    'output: 470 (370 alappont + 100 többletpont)' => [
        $studentBuilder->build([
            'valasztott-szak' => [
                'egyetem' => 'ELTE',
                'kar' => 'IK',
                'szak' => 'Programtervező informatikus',
            ],
            'erettsegi-eredmenyek' => [
                [
                    'nev' => 'magyar nyelv és irodalom',
                    'tipus' => 'közép',
                    'eredmeny' => '70%',
                ],
                [
                    'nev' => 'történelem',
                    'tipus' => 'közép',
                    'eredmeny' => '80%',
                ],
                [
                    'nev' => 'matematika',
                    'tipus' => 'emelt',
                    'eredmeny' => '90%',
                ],
                [
                    'nev' => 'angol nyelv',
                    'tipus' => 'közép',
                    'eredmeny' => '94%',
                ],
                [
                    'nev' => 'informatika',
                    'tipus' => 'közép',
                    'eredmeny' => '95%',
                ],
            ],
            'tobbletpontok' => [
                [
                    'kategoria' => 'Nyelvvizsga',
                    'tipus' => 'B2',
                    'nyelv' => 'angol',
                ],
                [
                    'kategoria' => 'Nyelvvizsga',
                    'tipus' => 'C1',
                    'nyelv' => 'német',
                ],
            ],
        ]),
        new CalculatorResult(370, 100, 470)
    ],
    'output: 476 (376 alappont + 100 többletpont)' => [
        $studentBuilder->build([
            'valasztott-szak' => [
                'egyetem' => 'ELTE',
                'kar' => 'IK',
                'szak' => 'Programtervező informatikus',
            ],
            'erettsegi-eredmenyek' => [
                [
                    'nev' => 'magyar nyelv és irodalom',
                    'tipus' => 'közép',
                    'eredmeny' => '70%',
                ],
                [
                    'nev' => 'történelem',
                    'tipus' => 'közép',
                    'eredmeny' => '80%',
                ],
                [
                    'nev' => 'matematika',
                    'tipus' => 'emelt',
                    'eredmeny' => '90%',
                ],
                [
                    'nev' => 'angol nyelv',
                    'tipus' => 'közép',
                    'eredmeny' => '94%',
                ],
                [
                    'nev' => 'informatika',
                    'tipus' => 'közép',
                    'eredmeny' => '95%',
                ],
                [
                    'nev' => 'fizika',
                    'tipus' => 'közép',
                    'eredmeny' => '98%',
                ],
            ],
            'tobbletpontok' => [
                [
                    'kategoria' => 'Nyelvvizsga',
                    'tipus' => 'B2',
                    'nyelv' => 'angol',
                ],
                [
                    'kategoria' => 'Nyelvvizsga',
                    'tipus' => 'C1',
                    'nyelv' => 'német',
                ],
            ],
        ]),
        new CalculatorResult(376, 100, 476)
    ]
];
