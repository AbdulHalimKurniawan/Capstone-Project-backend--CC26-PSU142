<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Influencer;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScrapeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validasi pastikan format dari Ekstensi bener
        $data = $request->validate([
            'influencer_username' => 'required|string',
            'full_name' => 'nullable|string',
            'profile_picture' => 'nullable|string',
            'bio' => 'nullable|string',
            'niche' => 'nullable|string',
            'followers' => 'required|integer',
            'engagement_rate' => 'required|numeric',
            'top_posts' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            // 2. Simpan atau Update Data Influencer
            $influencer = Influencer::updateOrCreate(
                ['username' => $data['influencer_username']], // Cari berdasarkan username
                [
                    'full_name' => $data['full_name'],
                    'profile_picture' => $data['profile_picture'],
                    'bio' => $data['bio'],
                    'niche' => $data['niche'],
                    'followers' => $data['followers'],
                    'engagement_rate' => $data['engagement_rate'],
                    'scraped_at' => now(),
                    'is_analyzed' => false // Set false lagi karena data baru masuk, AI harus ngecek ulang nanti
                ]
            );

            // 3. Simpan 10 Postingan Terbarunya
            foreach ($data['top_posts'] as $post) {
                Post::updateOrCreate(
                    ['post_url' => $post['post_url']], // Jangan duplikat post yang sama
                    [
                        'influencer_id' => $influencer->id,
                        'type' => $post['type'],
                        'likes' => $post['likes'],
                        'comments' => $post['comments'],
                        'image_url' => $post['image_url'],
                        'caption' => $post['caption'],
                    ]
                );
            }

            DB::commit();
            return response()->json(['message' => 'Data sukses dirampok dan disimpan! 🕵️‍♂️', 'status' => 'success'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menyimpan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
