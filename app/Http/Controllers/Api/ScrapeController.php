<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Influencer;
use App\Models\SocialAccount;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScrapeController extends Controller
{
    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        // 1. Validasi format dari Ekstensi Scraper
        $data = $request->validate([
            'platform' => 'nullable|string|in:instagram,tiktok',
            'influencer_username' => 'required|string',
            'full_name' => 'nullable|string',
            'profile_picture' => 'nullable|string',
            'bio' => 'nullable|string',
            'niche' => 'nullable|string',
            'followers' => 'required|integer',
            'engagement_rate' => 'required|numeric',
            'average_views' => 'nullable|integer', // 👈 TAMBAHAN: Validasi metrik AI
            'top_posts' => 'required|array',
        ]);

        // Default platform ke 'instagram' jika ekstensi tidak mengirimkan parameternya
        $platform = $data['platform'] ?? 'instagram';

        DB::beginTransaction();
        try {
            // 2. CEK APAKAH AKUN SOSMED INI SUDAH ADA?
            $existingSocialAccount = SocialAccount::where('platform', $platform)
                ->where('username', $data['influencer_username'])
                ->first();

            if ($existingSocialAccount) {
                // JIKA SUDAH ADA: Ambil Jantungnya (Influencer)
                $influencer = $existingSocialAccount->influencer;

                // Set is_analyzed = false agar model AI membaca ulang caption/bio barunya
                $influencer->update([
                    'is_analyzed' => false,
                    'niche' => $data['niche'] ?? $influencer->niche
                ]);
            } else {
                // JIKA BELUM ADA: Bikin Jantung (Influencer) Baru
                $influencer = Influencer::create([
                    'full_name' => $data['full_name'] ?? $data['influencer_username'],
                    'niche' => $data['niche'],
                    'is_analyzed' => false,
                ]);
            }

            // 3. SIMPAN / UPDATE DATA CABANG (SOCIAL ACCOUNTS)
            // 🐛 FIX: Simpan hasil updateOrCreate ke variabel baru biar ID-nya pasti ada
            $savedSocialAccount = SocialAccount::updateOrCreate(
                [
                    'platform' => $platform,
                    'username' => $data['influencer_username']
                ],
                [
                    'influencer_id' => $influencer->id, // Ikat ke Jantungnya
                    'profile_picture' => $data['profile_picture'],
                    'bio' => $data['bio'],
                    'followers' => $data['followers'],
                    'engagement_rate' => $data['engagement_rate'],
                    'average_views' => $data['average_views'] ?? 0, // 👈 TAMBAHAN: Simpan ke DB
                    'scraped_at' => now(),
                ]
            );

            // 4. SIMPAN POSTINGAN TERBARUNYA
            foreach ($data['top_posts'] as $post) {
                Post::updateOrCreate(
                    ['post_url' => $post['post_url']], // Jangan duplikat post yang sama
                    [
                        'social_account_id' => $savedSocialAccount->id, // 👈 FIX: Pakai ID dari model yang barusan di-save
                        'type' => $post['type'] ?? 'image',
                        'likes' => $post['likes'] ?? 0,
                        'comments' => $post['comments'] ?? 0,
                        'views' => $post['views'] ?? 0, // 👈 TAMBAHAN: Simpan views tiap post
                        'image_url' => $post['image_url'] ?? null,
                        'caption' => $post['caption'] ?? '',
                    ]
                );
            }

            DB::commit();
            return response()->json([
                'message' => 'Data sukses dirampok dan disimpan dengan arsitektur baru! 🕵️‍♂️',
                'status' => 'success'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal menyimpan: ' . $e->getMessage()
            ], 500);
        }
    }
}
