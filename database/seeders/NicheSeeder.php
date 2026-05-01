<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Niche;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NicheSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Daftar lengkap Kategori Induk beserta Anak-anaknya
        $data = [
            'Beauty & Fashion' => [
                'Skincare',
                'Makeup & Cosmetics',
                'Haircare',
                'Modest Fashion (Hijab)',
                'Luxury Fashion',
                'Streetwear',
                'Thrifting & Sustainable',
                'Body Positivity'
            ],
            'Food & Beverage' => [
                'Street Food Reviewer',
                'Fine Dining & Cafe',
                'Home Cooking & Recipes',
                'Baking & Pastry',
                'Coffee & Barista',
                'Vegan & Healthy Food',
                'Mukbang'
            ],
            'Technology & Digital' => [
                'Smartphone & Gadget',
                'PC Building & Setup',
                'Software & AI Tools',
                'Programming & Web Dev',
                'Home Automation'
            ],
            'Finance & Business' => [
                'Personal Finance',
                'Stock Market & Crypto',
                'Entrepreneurship',
                'Affiliate Marketing',
                'Property & Real Estate'
            ],
            'Gaming & Esports' => [
                'Mobile Gaming',
                'PC & Console Gaming',
                'Esports News',
                'Retro Gaming'
            ],
            'Education & Career' => [
                'Self Development',
                'Career Advice',
                'Language Learning',
                'Scholarship Info',
                'Studygram'
            ],
            'Lifestyle & Travel' => [
                'Luxury Travel',
                'Backpacking & Budget Travel',
                'Glamping & Camping',
                'Hidden Gems',
                'Minimalist Living',
                'Travel Vlogger',
                'Daily Vlog'
            ],
            'Family & Parenting' => [
                'Parenting Tips',
                'MPASI & Baby Food',
                'Working Mom Lifestyle',
                'Home & Decor',
                'Family Vlog',
                'Couple & Relationship'
            ],
            'Health & Fitness' => [
                'Gym & Bodybuilding',
                'Yoga & Pilates',
                'Running & Marathon',
                'Mental Health Awareness',
                'Nutritionist'
            ],
            'Entertainment & Hobbies' => [
                'Comedy & Parody',
                'Movie & Series Reviewer',
                'Anime & Manga',
                'Photography & Videography',
                'Bookstagram',
                'K-Pop & Fandom',
                'Pets & Animals',
                'Automotive',
                'Musisian',
                'Actor',
                'Celebrity Lifestyle',
                'Prank & Challenge'
            ],
        ];

        // Looping untuk memasukkan data ke database
        foreach ($data as $categoryName => $niches) {
            // Buat Kategori Induk (atau ambil kalau sudah ada)
            $category = Category::firstOrCreate(['name' => $categoryName]);

            // Buat Anak-anak Niche di bawah Kategori tersebut
            foreach ($niches as $nicheName) {
                Niche::firstOrCreate([
                    'category_id' => $category->id,
                    'name' => $nicheName
                ]);
            }
        }

        $this->command->info('✅ Seluruh data Kategori & Niche berhasil di-seed!');
    }
}
