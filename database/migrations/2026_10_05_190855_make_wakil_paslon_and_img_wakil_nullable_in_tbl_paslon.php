<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tbl_paslon', function (Blueprint $table) {
            $table->string('wakil_paslon')->nullable()->default(null)->change();
            $table->string('img_wakil')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_paslon', function (Blueprint $table) {
            $table->string('wakil_paslon')->nullable(false)->change();
            $table->string('img_wakil')->nullable(false)->change();
        });
    }
};
