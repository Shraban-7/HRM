<?php

namespace App\Http\Controllers\Performance;

use App\Http\Controllers\Controller;
use App\Services\Performance\PerformanceBonusCalculator;

class BonusController extends Controller
{
    public function index(PerformanceBonusCalculator $calculator)
    {
        $bonuses = $calculator->calculateBonusesForAll();
        $total = array_sum(array_column($bonuses, 'bonus'));

        return view('performance.bonuses', compact('bonuses', 'total'));
    }
}
