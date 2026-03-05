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
        Schema::create('order_status_logs', function (Blueprint $table) {
            // 1. Khóa chính id (tương đương bigint UNSIGNED NOT NULL AUTO_INCREMENT)
            $table->id();

            // 2. Khóa ngoại order_id liên kết với bảng orders (xóa đơn hàng -> xóa luôn log)
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');

            // 3. Cột status (tương đương varchar(50) NOT NULL)
            $table->string('status', 50);

            // 4. Cột created_at (cho phép NULL và mặc định lấy giờ hiện tại CURRENT_TIMESTAMP)
            $table->timestamp('created_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_status_logs');
    }
};