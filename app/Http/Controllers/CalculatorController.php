<?php

namespace App\Http\Controllers;

use App\Models\Calculation;
use Illuminate\Http\Request;

class CalculatorController extends Controller
{
    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'expression' => 'required|string',
            'result' => 'required|numeric'
        ]);

        $calculation = Calculation::create([
            'expression' => $validated['expression'],
            'result' => $validated['result']
        ]);

        return response()->json($calculation);
    }

    public function index()
    {
        $calculations = Calculation::orderBy('created_at', 'desc')
            ->get();

        return response()->json($calculations);
    }

    public function destroy(Calculation $calculation)
    {
        $calculation->delete();

        return response()->json(['message' => 'Calculation deleted']);
    }

    public function clearAll()
    {
        Calculation::truncate();

        return response()->json(['message' => 'History cleared']);
    }
}