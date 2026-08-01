<?php

return [
    'pepper' => env('HRM_PEPPER', ''),
    'tax_brackets' => [
        ['min' => 0, 'max' => 300000, 'rate' => 0],
        ['min' => 300001, 'max' => 600000, 'rate' => 0.05],
        ['min' => 600001, 'max' => 1200000, 'rate' => 0.10],
        ['min' => 1200001, 'max' => 2400000, 'rate' => 0.15],
        ['min' => 2400001, 'max' => null, 'rate' => 0.20],
    ],
    'leave_types' => ['annual' => 20, 'sick' => 14, 'other' => 5],
    'default_deduction_rate' => 0.10,
    'performance_bonus_rates' => [
        5 => 0.10,
        4 => 0.05,
        3 => 0.02,
        2 => 0.0,
        1 => 0.0,
    ],
];
