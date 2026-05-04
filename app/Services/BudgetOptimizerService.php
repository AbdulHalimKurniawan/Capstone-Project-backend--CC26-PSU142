<?php

namespace App\Services;

use App\Models\SocialAccount;
use App\Models\CampaignStrategy;

class BudgetOptimizerService
{
    public function getRecommendations(float $budget, int $nicheId, array $platforms = [])
    {
        // 1. Ambil akun sosial berdasarkan Niche & Eager Load data terkait
        $query = SocialAccount::with(['posts', 'rateCard', 'influencer'])
            ->whereHas('influencer.niches', function($q) use ($nicheId) {
                $q->where('niches.id', $nicheId);
            });

        if (!empty($platforms)) {
            $query->whereIn('platform', $platforms);
        }

        $accounts = $query->get();

        // 2. Hitung rekomendasi menggunakan strategi default (Efficient Greedy)
        $accounts->each(function ($acc) use ($nicheId) {
            $isPrimary = $acc->influencer->niches->where('id', $nicheId)->first()->pivot->is_primary ?? false;
            $acc->accuracy_match = $isPrimary ? 1.0 : 0.8;
            $acc->final_efficiency_score = $acc->efficiency_score * $acc->accuracy_match;
        });

        $efficientStrategy = $this->generateStrategy($accounts, $budget, 'Efficient Selection');
        
        // 3. Generate alternative strategies
        $strategies = [
            $efficientStrategy,
            $this->generateStrategy($accounts->sortBy('followers'), $budget, 'Micro-Power'),
            $this->generateStrategy($accounts->sortByDesc('followers'), $budget, 'Big Bang')
        ];

        return [
            'total_budget' => $budget,
            'niche_id' => $nicheId,
            'platforms' => $platforms,
            'strategies' => $strategies
        ];
    }

    private function generateStrategy($sortedAccounts, float $budget, string $strategyName)
    {
        // Ambil Metadata Pusat dari Model
        $meta = CampaignStrategy::getStrategyMetadata($strategyName);

        // 2. Sortir berdasarkan strategi jika diperlukan
        if ($strategyName === 'Efficient Selection') {
            $sortedAccounts = $sortedAccounts->sortByDesc(fn($acc) => $acc->final_efficiency_score);
        }

        $selected = [];
        $remainingBudget = $budget;
        $totalEngagement = 0;
        $totalFollowers = 0;
        
        $totalBoost = 0;
        $totalOwning = 0;
        $totalYellowCart = 0;

        foreach ($sortedAccounts as $acc) {
            $cost = $acc->rateCard->base_rate ?? 0;

            if ($cost > 0 && $remainingBudget >= $cost) {
                $posts = $acc->posts;
                $avgEngagement = $posts->isNotEmpty() 
                    ? ($posts->avg('likes') + $posts->avg('comments')) 
                    : 0;

                $selected[] = [
                    'influencer_id' => $acc->influencer_id,
                    'name' => $acc->influencer->full_name,
                    'username' => $acc->username,
                    'platform' => $acc->platform,
                    'cost' => $cost,
                    'followers' => $acc->followers,
                    'engagement_rate' => $acc->engagement_rate,
                    'avg_engagement' => $avgEngagement,
                    'profile_picture' => $acc->profile_picture,
                    'last_post_image' => $posts->first()->image_url ?? null,
                ];
                
                $remainingBudget -= $cost;
                $totalEngagement += $avgEngagement;
                $totalFollowers += $acc->followers;

                // Hitung Add-ons dari Rate Card
                $totalBoost += $acc->rateCard->addon_boost ?? 0;
                $totalOwning += $acc->rateCard->addon_owning ?? 0;
                $totalYellowCart += $acc->rateCard->addon_link ?? 0;
            }
        }

        // 3. Estimasi performa
        $estReach = (int) ($totalFollowers * 0.2);
        $salesMin = (int) ($estReach * 0.001);
        $salesMax = (int) ($estReach * 0.005);

        return [
            'strategy_name' => $strategyName,
            'tagline' => $meta['tagline'],
            'budget_used' => $budget - $remainingBudget,
            'remaining_budget' => $remainingBudget,
            'influencer_count' => count($selected),
            'est_reach' => $estReach,
            'est_sales_range' => "{$salesMin}-{$salesMax}",
            'est_roi' => $meta['est_roi'],
            'influencers' => $selected,
            'ig_deliverables' => [
                'video' => count(array_filter($selected, fn($i) => strtolower($i['platform']) === 'instagram')),
                'story' => count(array_filter($selected, fn($i) => strtolower($i['platform']) === 'instagram')) * 2
            ],
            'tiktok_deliverables' => [
                'video' => count(array_filter($selected, fn($i) => strtolower($i['platform']) === 'tiktok')),
                'story' => 0 
            ],
            'addons' => [
                'boost_code_ads' => $totalBoost,
                'owning_content' => $totalOwning,
                'yellow_cart' => $totalYellowCart
            ]
        ];
    }
}
