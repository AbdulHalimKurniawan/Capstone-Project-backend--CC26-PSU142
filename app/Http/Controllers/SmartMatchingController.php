<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SmartMatchingController extends Controller
{
    public function search(Request $request)
    {
        // 1. Tangkap data dari form Next.js
        $kriteria = $request->input('kriteria_influencer');
        $lokasi_target = $request->input('audience_location');
        $gender_target = $request->input('audience_gender'); // "L" atau "P"

        try {
            // 2. TEMBAK API PYTHON (AI MATCHMAKER)
            $aiResponse = Http::timeout(15)->post('http://127.0.0.1:5000/api/search-influencer', [
                'query' => $kriteria,
                'limit' => 30
            ]);

            if (!$aiResponse->successful()) {
                return response()->json(['status' => 'error', 'message' => 'AI Engine sedang sibuk.'], 500);
            }

            $aiData = $aiResponse->json()['data'];

            if (empty($aiData)) {
                return response()->json(['status' => 'success', 'total_found' => 0, 'data' => []]);
            }

            // 3. AMBIL SEMUA ID INFLUENCER DARI HASIL AI
            $influencerIds = array_column($aiData, 'influencer_id');

            // 4. SATPAM KEDUA: FILTER DEMOGRAFI PAKAI MYSQL (LARAVEL)
            $query = DB::table('influencers')
                ->whereIn('id', $influencerIds)
                ->select('id', 'audience_location');

            if (!empty($lokasi_target) && strtolower($lokasi_target) !== 'semua lokasi') {
                $query->where('audience_location', 'LIKE', '%' . strtolower($lokasi_target) . '%');
            }

            $lolosFilterIds = $query->pluck('id')->toArray();

            // 5. GABUNGKAN HASIL
            $finalResults = collect($aiData)
                ->whereIn('influencer_id', $lolosFilterIds)
                ->sortByDesc('match_score')
                ->values()
                ->take(10);

            // 6. KEMBALIKAN KE NEXT.JS!
            return response()->json([
                'status' => 'success',
                'campaign' => $request->input('campaign_name'),
                'total_found' => $finalResults->count(),
                'data' => $finalResults
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
