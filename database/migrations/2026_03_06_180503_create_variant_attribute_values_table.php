<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('variant_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('variant_id');
            $table->unsignedBigInteger('attribute_value_id');
            
            $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('cascade');
            $table->foreign('attribute_value_id')->references('id')->on('attribute_values')->onDelete('cascade');
            
            // Đảm bảo không bị lặp dữ liệu (1 biến thể không thể lưu 2 lần "Màu Đỏ")
            $table->unique(['variant_id', 'attribute_value_id'], 'variant_attr_unique');
        });
    }
    public function down(): void { Schema::dropIfExists('variant_attribute_values'); }
};