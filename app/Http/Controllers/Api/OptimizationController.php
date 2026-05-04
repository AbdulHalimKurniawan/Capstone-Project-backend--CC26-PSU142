<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BudgetOptimizerService;
use Illuminate\Http\Request;

class OptimizationController extends Controller
{
    /**
     * Handle the budget optimization request.
     */
    public function __invoke(Request $request, BudgetOptimizerService $service)
    {
        $validated = $request->validate([
            'budget' => 'required|numeric|min:1000',
            'niche_id' => 'required|exists:niches,id',
            'platforms' => 'nullable|array',
            'platforms.*' => 'string'
        ]);

        $result = $service->getRecommendations(
            (float) $validated['budget'],
            (int) $validated['niche_id'],
            $validated['platforms'] ?? []
        );

        return response()->json([
            'status' => 'success',
            'data' => $result
        ]);
    }
}
