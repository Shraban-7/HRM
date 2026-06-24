<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    public function index()
    {
        $taxBrackets = config('hrm.tax_brackets', [
            ['min' => 0, 'max' => 300000, 'rate' => 0],
            ['min' => 300001, 'max' => 600000, 'rate' => 0.05],
            ['min' => 600001, 'max' => 1200000, 'rate' => 0.10],
            ['min' => 1200001, 'max' => 2400000, 'rate' => 0.15],
            ['min' => 2400001, 'max' => null, 'rate' => 0.20],
        ]);

        return view('payroll.tax', compact('taxBrackets'));
    }
}