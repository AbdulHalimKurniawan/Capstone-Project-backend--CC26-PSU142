<?php

namespace Database\Seeders;

use App\Models\Influencer;
use App\Models\Niche;
use App\Models\SocialAccount;
use App\Models\Post;
use App\Models\RateCard;
use Illuminate\Database\Seeder;

class BudgetOptimizationTestSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil Niche "Skincare" untuk testing
        $niche = Niche::where('name', 'Skincare')->first();
        
        if (!$niche) {
            $this->command->error('Niche Skincare tidak ditemukan. Jalankan NicheSeeder dulu!');
            return;
        }

        // 2. Buat Influencer A (Efisien: Harga murah, Engagement tinggi)
        $infA = Influencer::create(['full_name' => 'Influencer Efisien']);
        $infA->niches()->attach($niche->id, ['is_primary' => true]);
        
        $socA = SocialAccount::create([
            'influencer_id' => $infA->id,
            'platform' => 'instagram',
            'username' => 'efisien.id',
            'followers' => 10000,
            'engagement_rate' => 5.0
        ]);

        RateCard::create([
            'social_account_id' => $socA->id,
            'base_rate' => 500000 // 500rb
        ]);

        // Buat beberapa post agar engagement rata-ratanya tinggi
        for ($i=0; $i<5; $i++) {
            Post::create([
                'social_account_id' => $socA->id,
                'likes' => 1000,
                'comments' => 100,
                'post_url' => "https://ig.com/p/a$i"
            ]);
        }

        // 3. Buat Influencer B (Mahal: Harga tinggi, Engagement biasa)
        $infB = Influencer::create(['full_name' => 'Influencer Sultan']);
        $infB->niches()->attach($niche->id, ['is_primary' => true]);

        $socB = SocialAccount::create([
            'influencer_id' => $infB->id,
            'platform' => 'instagram',
            'username' => 'sultan.pisan',
            'followers' => 50000,
            'engagement_rate' => 2.0
        ]);

        RateCard::create([
            'social_account_id' => $socB->id,
            'base_rate' => 2000000 // 2jt
        ]);

        for ($i=0; $i<5; $i++) {
            Post::create([
                'social_account_id' => $socB->id,
                'likes' => 500,
                'comments' => 50,
                'post_url' => "https://ig.com/p/b$i"
            ]);
        }

        $this->command->info('✅ Data test berhasil dibuat! ID Niche Skincare: ' . $niche->id);
    }
}
