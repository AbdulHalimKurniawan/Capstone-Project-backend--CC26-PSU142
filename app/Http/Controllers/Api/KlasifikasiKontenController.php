<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Influencer;
use Illuminate\Http\Request;

class KlasifikasiKontenController extends Controller
{
    // 1. NGASIH KERJAAN KE COLAB (Ngambil data yang belum dianalisis)
    public function getQueue()
    {
        // Ambil 1 influencer teratas yang belum dianalisis, bawa juga 10 post-nya
        $influencer = Influencer::with('posts')
            ->where('is_analyzed', false)
            ->first();

        if (!$influencer) {
            return response()->json(['message' => 'Antrean kosong Bos! Semua udah dianalisis.'], 404);
        }

        return response()->json($influencer, 200);
    }

    // 2. NERIMA HASIL DARI COLAB (Menyimpan Tag Gaya Konten)
    public function updateResult(Request $request)
    {
        $data = $request->validate([
            'influencer_id' => 'required|exists:influencers,id',
            'ai_style_tags' => 'required|string', // Contoh isi: "High-Energy, Kasual, Gamer"
        ]);

        $influencer = Influencer::find($data['influencer_id']);
        $influencer->update([
            'ai_style_tags' => $data['ai_style_tags'],
            'is_analyzed' => true // Tandai selesai!
        ]);

        return response()->json(['message' => 'MANTAP! Hasil AI berhasil disimpan ke Database! 🤖✅'], 200);
    }
}
