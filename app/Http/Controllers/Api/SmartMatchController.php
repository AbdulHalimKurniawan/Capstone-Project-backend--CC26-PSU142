<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SmartMatchController extends Controller
{
    public function search(Request $request)
    {
        // 1. Tangkap Input dari React
        $nicheInput = $request->input('industry_niche');
        $deskripsiProduk = $request->input('product_description');

        // MVP: Jika deskripsi kosong, kita kembalikan error
        if (!$deskripsiProduk) {
            return response()->json(['error' => 'Deskripsi produk wajib diisi untuk dianalisis AI!'], 400);
        }

        // 2. THE CTO HACK: Minta tolong Python ubah "Deskripsi Produk" jadi Vektor!
        try {
            $aiResponse = Http::post('http://127.0.0.1:5000/vectorize-query', [
                'text' => $deskripsiProduk
            ]);
            $queryVector = $aiResponse->json('vector');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Mesin AI sedang tidur. Nyalakan Python Engine!'], 500);
        }

        // 3. AMBIL SEMUA KANDIDAT (TIDAK ADA YANG DIELIMINASI / HARD FILTER)
        $kandidat = DB::table('influencers')->whereNotNull('embedding_vector')->get();

        // 4. THE SCORING ENGINE (Hitung Jodoh Berdasarkan 3 Pilar)
        $hasil = $kandidat->map(function ($inf) use ($queryVector, $nicheInput) {
            // A. Decode Vektor Influencer dari Database
            $infVector = json_decode($inf->embedding_vector, true);

            // --- PILAR 1: AI SEMANTIC (Porsi Utama) ---
            $aiScore = $this->cosineSimilarity($queryVector, $infVector);
            $aiScorePercent = max(0, $aiScore) * 100;

            // --- PILAR 2: ENGAGEMENT RATE ---
            $erScore = $inf->engagement_rate;
            $normalizedER = min($erScore * 10, 100); // ER 10% = 100 poin

            // --- PILAR 3: NICHE SYNERGY (BONUS) ---
            $nicheBonusPercent = 0;
            // Pastikan input niche tidak kosong dan kolom niche di DB ada isinya
            // (Catatan: Kalau nama kolom di database Bos itu 'category', ganti $inf->niche jadi $inf->category)
            if (!empty($nicheInput) && !empty($inf->niche)) {
                // Cek apakah Niche pilihan Brand ada di dalam data Niche Influencer (Case-Insensitive)
                if (stripos($inf->niche, $nicheInput) !== false) {
                    $nicheBonusPercent = 100; // Jackpot! Dapat bonus poin penuh
                }
            }

            // --- PERHITUNGAN SKOR FINAL ---
            if (!empty($nicheInput)) {
                // RUMUS LENGKAP: 60% AI + 20% ER + 20% Niche Bonus
                $finalScore = (0.60 * $aiScorePercent) + (0.20 * $normalizedER) + (0.20 * $nicheBonusPercent);
            } else {
                // RUMUS ADAPTIF: Kalau Brand gak milih Niche, kita pakai 75% AI + 25% ER
                $finalScore = (0.75 * $aiScorePercent) + (0.25 * $normalizedER);
            }

            return [
                'id' => $inf->id,
                'username' => $inf->username,
                'full_name' => $inf->full_name,
                'profile_picture' => $inf->profile_picture,
                'niche' => $inf->niche,
                'followers' => $inf->followers,
                'engagement_rate' => $inf->engagement_rate,
                'match_score' => round($finalScore, 2), 
                'ai_similarity' => round($aiScorePercent, 2) . '%',
            ];
        });

        // 5. Urutkan dari Skor Tertinggi & Ambil Top 10
        $hasilTerbaik = $hasil->sortByDesc('match_score')->take(10)->values();

        return response()->json([
            'status' => 'success',
            'total_found' => $hasilTerbaik->count(),
            'data' => $hasilTerbaik
        ]);
    }

    // --- FUNGSI MATEMATIKA TRIGONOMETRI (COSINE SIMILARITY) ---
    private function cosineSimilarity($vecA, $vecB)
    {
        $dotProduct = 0;
        $normA = 0;
        $normB = 0;

        $count = min(count($vecA), count($vecB));

        for ($i = 0; $i < $count; $i++) {
            $dotProduct += $vecA[$i] * $vecB[$i];
            $normA += $vecA[$i] ** 2;
            $normB += $vecB[$i] ** 2;
        }

        if ($normA == 0 || $normB == 0) return 0;
        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }
}