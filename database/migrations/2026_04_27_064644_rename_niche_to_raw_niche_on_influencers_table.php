<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('influencers', function (Blueprint $table) {
            // Ubah nama kolom agar fungsinya jelas jadi sekadar data mentah
            $table->renameColumn('niche', 'raw_niche');
        });
    }

    public function down()
    {
        Schema::table('influencers', function (Blueprint $table) {
            $table->renameColumn('raw_niche', 'niche');
        });
    }
};
