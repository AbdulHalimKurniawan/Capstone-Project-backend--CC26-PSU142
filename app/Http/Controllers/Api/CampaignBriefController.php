<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CampaignBrief;
use Illuminate\Http\Request;

class CampaignBriefController extends Controller
{
    /**
     * Display all briefs for a campaign.
     */
    public function index($campaignId)
    {
        $briefs = CampaignBrief::where('campaign_id', $campaignId)->get();

        return response()->json([
            'status' => 'success',
            'data' => $briefs
        ]);
    }

    /**
     * Store a new brief for a campaign.
     */
    public function store(Request $request, $campaignId)
    {
        $validated = $request->validate([
            'brand_name'       => 'required|string|max:255',
            'brief_name'       => 'required|string|max:255',
            'hashtags'         => 'nullable|string|max:500',
            'tag_account'      => 'nullable|string|max:255',
            'link_yellow_cart' => 'nullable|string|max:500',
            'draft_submission' => 'nullable|date',
            'draft_post'       => 'nullable|date',
            'dos'              => 'nullable|string',
            'donts'            => 'nullable|string',
        ]);

        $brief = CampaignBrief::create([
            'campaign_id'      => $campaignId,
            'brand_name'       => $validated['brand_name'],
            'brief_name'       => $validated['brief_name'],
            'hashtags'         => $validated['hashtags'] ?? null,
            'tag_account'      => $validated['tag_account'] ?? null,
            'link_yellow_cart' => $validated['link_yellow_cart'] ?? null,
            'draft_submission' => $validated['draft_submission'] ?? null,
            'draft_post'       => $validated['draft_post'] ?? null,
            'dos'              => $validated['dos'] ?? null,
            'donts'            => $validated['donts'] ?? null,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Campaign brief created successfully',
            'data'    => $brief
        ], 201);
    }

    /**
     * Display a specific brief.
     */
    public function show($campaignId, $briefId)
    {
        $brief = CampaignBrief::where('campaign_id', $campaignId)
            ->where('id', $briefId)
            ->first();

        if (!$brief) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Brief not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $brief
        ]);
    }

    /**
     * Update an existing brief.
     */
    public function update(Request $request, $campaignId, $briefId)
    {
        $brief = CampaignBrief::where('campaign_id', $campaignId)
            ->where('id', $briefId)
            ->first();

        if (!$brief) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Brief not found'
            ], 404);
        }

        $validated = $request->validate([
            'brand_name'       => 'sometimes|string|max:255',
            'brief_name'       => 'sometimes|string|max:255',
            'hashtags'         => 'nullable|string|max:500',
            'tag_account'      => 'nullable|string|max:255',
            'link_yellow_cart' => 'nullable|string|max:500',
            'draft_submission' => 'nullable|date',
            'draft_post'       => 'nullable|date',
            'dos'              => 'nullable|string',
            'donts'            => 'nullable|string',
        ]);

        $brief->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Campaign brief updated successfully',
            'data'    => $brief
        ]);
    }

    /**
     * Delete a brief.
     */
    public function destroy($campaignId, $briefId)
    {
        $brief = CampaignBrief::where('campaign_id', $campaignId)
            ->where('id', $briefId)
            ->first();

        if (!$brief) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Brief not found'
            ], 404);
        }

        $brief->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Campaign brief deleted successfully'
        ]);
    }
}
