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
        Schema::table('news', function (Blueprint $table) {
            // Thêm cột deleted_at nếu chưa tồn tại
            if (!Schema::hasColumn('news', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            // Xóa cột deleted_at nếu tồn tại
            if (Schema::hasColumn('news', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
