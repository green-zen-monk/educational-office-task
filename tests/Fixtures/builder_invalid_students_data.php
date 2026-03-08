<?php

declare(strict_types=1);

return [
    'Student - has not erettsegi-eredmenyek->nev key' => [
        [
            'valasztott-szak' => [
                'egyetem' => 'ELTE',
                'kar' => 'IK',
                'szak' => 'Programtervező informatikus',
            ],
            'erettsegi-eredmenyek' => [
                [
                    'tipus' => 'közép',
                    'eredmeny' => '70%',
                ]
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
        ]
    ],
    'Student - invalid key name erettsegi-eredmenyek->eretti-eredmenyek' => [
        [
            'valasztott-szak' => [
                'egyetem' => 'PPKE',
                'kar' => 'BTK',
                'szak' => 'Anglisztika',
            ],
            'eretti-eredmenyek' => [
                [
                    'nev' => 'magyar nyelv és irodalom',
                    'tipus' => 'közép',
                    'eredmeny' => '70%',
                ]
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
                ]
            ]
        ]
    ],
    'Student - invalid tobbletpontok value' => [
        [
            'valasztott-szak' => [
                'egyetem' => 'PPKE',
                'kar' => 'BTK',
                'szak' => 'Anglisztika',
            ],
            'erettsegi-eredmenyek' => [
                [
                    'nev' => 'magyar nyelv és irodalom',
                    'tipus' => 'közép',
                    'eredmeny' => '70%',
                ]
            ],
            'tobbletpontok' => ''
        ]
    ]
];
