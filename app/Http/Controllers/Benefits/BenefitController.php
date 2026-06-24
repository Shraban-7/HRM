<?php

namespace App\Http\Controllers\Benefits;

use App\Http\Controllers\Controller;
use App\Models\Benefit;
use Illuminate\Http\Request;

class BenefitController extends Controller
{
    public function index()
    {
        $benefits = Benefit::where('is_active', true)->paginate(15);
        return view('benefits.index', compact('benefits'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'cost' => 'required|numeric|min:0',
            'type' => 'required|in:health,dental,vision,life,retirement,other',
        ]);

        Benefit::create($request->all());
        return redirect()->route('benefits.index')->with('success', 'Benefit created.');
    }

    public function update(Request $request, Benefit $benefit)
    {
        $benefit->update($request->validate(['name' => 'required|string', 'cost' => 'required|numeric|min:0', 'is_active' => 'boolean']));
        return redirect()->route('benefits.index')->with('success', 'Benefit updated.');
    }
}