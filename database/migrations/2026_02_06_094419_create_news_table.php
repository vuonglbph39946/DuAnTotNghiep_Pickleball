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
        Schema::create('news', function (Blueprint $table) {
            $table->id();

            // Tiêu đề bài viết
            $table->string('title');

            // Slug URL duy nhất
            $table->string('slug')->unique();

            // Tóm tắt ngắn
            $table->text('summary')->nullable();

            // Nội dung chi tiết
            $table->longText('content')->nullable();

            // Đường dẫn ảnh thumbnail
            $table->string('thumbnail')->nullable();

            // Danh mục
            $table->unsignedBigInteger('category_id')->nullable()->index();

            // Tác giả (tham chiếu bảng users hoặc authors)
            $table->unsignedBigInteger('author_id')->nullable()->index();

            // Trạng thái bài viết (0: draft, 1: published, 2: archived ...)
            $table->tinyInteger('status')->default(0)->comment('0=draft,1=published,2=archived');

            // Thời gian publish
            $table->timestamp('published_at')->nullable();

            // Lượt xem
            $table->unsignedBigInteger('views')->default(0);

            // Thời gian tạo / cập nhật
            $table->timestamps();

            // Soft delete
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
