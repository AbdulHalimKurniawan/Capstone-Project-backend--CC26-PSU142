<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignStrategy;

use Illuminate\Http\Request;

class CampaignController extends Controller
{
    /**
     * Store a new campaign and its strategies.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'niche_id' => 'required|exists:niches,id',
            'budget' => 'required|numeric',
            'platforms' => 'required|array',
            
            // Kolom baru
            'product_description' => 'nullable|string',
            'target_age_min' => 'nullable|integer',
            'target_age_max' => 'nullable|integer',
            'target_location' => 'nullable|string',
            'target_gender' => 'nullable|string',
            'campaign_objective' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            
            'strategies' => 'required|array',
        ]);

        $campaign = Campaign::create([
            'name' => $validated['name'],
            'niche_id' => $validated['niche_id'],
            'budget' => $validated['budget'],
            'platforms' => $validated['platforms'],
            
            'product_description' => $validated['product_description'] ?? null,
            'target_age_min' => $validated['target_age_min'] ?? null,
            'target_age_max' => $validated['target_age_max'] ?? null,
            'target_location' => $validated['target_location'] ?? null,
            'target_gender' => $validated['target_gender'] ?? null,
            'campaign_objective' => $validated['campaign_objective'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
        ]);

        foreach ($validated['strategies'] as $strategyData) {
            $campaign->strategies()->create([
                'strategy_name' => $strategyData['strategy_name'],
                'tagline' => $strategyData['tagline'] ?? null, // Mengambil dinamis dari Frontend
                'ig_deliverables' => $strategyData['ig_deliverables'] ?? null,
                'tiktok_deliverables' => $strategyData['tiktok_deliverables'] ?? null,
                'est_reach' => $strategyData['est_reach'] ?? 0,
                'est_sales_range' => $strategyData['est_sales_range'] ?? '',
                'est_roi' => $strategyData['est_roi'] ?? 0, // Mengambil hasil hitungan AI (dinamis)
                'addons' => $strategyData['addons'] ?? null,
                'selected_influencers' => $strategyData['influencers'] ?? null,
                'allocation_text' => $strategyData['allocation_text'] ?? null,
                'fee_influencer' => $strategyData['fee_influencer'] ?? null,
                'is_selected' => $strategyData['is_selected'] ?? false,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Campaign created successfully',
            'data' => $campaign->load('strategies')
        ], 201);
    }


    /**
     * Select a specific strategy for a campaign.
     */
    public function selectStrategy(Request $request, $campaignId, $strategyId)
    {
        CampaignStrategy::where('campaign_id', $campaignId)->update(['is_selected' => false]);
        
        $strategy = CampaignStrategy::where('campaign_id', $campaignId)
            ->where('id', $strategyId)
            ->firstOrFail();
            
        $strategy->update(['is_selected' => true]);

        return response()->json([
            'status' => 'success',
            'message' => 'Strategy selected successfully',
            'data' => $strategy
        ]);
    }
}
