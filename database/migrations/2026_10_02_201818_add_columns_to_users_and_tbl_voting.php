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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'nama_panjang')) {
                $table->string('nama_panjang')->nullable()->after('username');
            }
            if (!Schema::hasColumn('users', 'kelas')) {
                $table->string('kelas')->nullable()->after('nama_panjang');
            }
        });

        // Make email nullable in users table if needed
        DB::statement("ALTER TABLE `users` MODIFY `email` VARCHAR(255) NULL");

        // Ensure unique index on tbl_voting(id_user) to prevent double voting at db level
        $existing = DB::select("SHOW INDEX FROM `tbl_voting` WHERE Key_name = 'tbl_voting_id_user_unique'");
        if (empty($existing)) {
            // Drop existing non-unique index if present
            $nonUnique = DB::select("SHOW INDEX FROM `tbl_voting` WHERE Key_name = 'id_user' AND Non_unique = 1");
            if (!empty($nonUnique)) {
                DB::statement("ALTER TABLE `tbl_voting` DROP INDEX `id_user`");
            }
            DB::statement("ALTER TABLE `tbl_voting` ADD UNIQUE `tbl_voting_id_user_unique` (`id_user`)");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $existing = DB::select("SHOW INDEX FROM `tbl_voting` WHERE Key_name = 'tbl_voting_id_user_unique'");
        if (!empty($existing)) {
            DB::statement("ALTER TABLE `tbl_voting` DROP INDEX `tbl_voting_id_user_unique`");
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'kelas')) {
                $table->dropColumn('kelas');
            }
            if (Schema::hasColumn('users', 'nama_panjang')) {
                $table->dropColumn('nama_panjang');
            }
        });
    }
};
