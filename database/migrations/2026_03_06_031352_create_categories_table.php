<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Chạy migration (Tạo bảng categories)
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id(); // Tự động tạo id (bigint, unsigned, auto_increment, primary key)
            
            $table->string('name'); // varchar(255) NOT NULL
            $table->string('slug')->unique(); // varchar(255) NOT NULL + Ràng buộc UNIQUE
            $table->string('image')->nullable(); // varchar(255) DEFAULT NULL
            
            // Cột parent_id (Dùng để làm danh mục con)
            $table->unsignedBigInteger('parent_id')->nullable(); // bigint UNSIGNED DEFAULT NULL
            
            $table->boolean('status')->default(1); // tinyint(1) mặc định là 1 (Hiển thị)
            
            $table->timestamps(); // Tự động tạo 2 cột created_at và updated_at

            // Tạo khóa ngoại (Foreign Key): parent_id liên kết với id của chính bảng categories
            $table->foreign('parent_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('set null'); // Nếu xóa danh mục cha, danh mục con sẽ thành null chứ không bị xóa theo
        });
    }

    /**
     * Lùi lại migration (Xóa bảng)
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};