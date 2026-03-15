<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('attribute_values', function (Blueprint $table) {
            // Thêm cột lưu mã màu Hex (VD: #FF0000), nullable vì Size thì không cần màu
            $table->string('color_code')->nullable()->after('value');
        });
    }

    public function down()
    {
        Schema::table('attribute_values', function (Blueprint $table) {
            $table->dropColumn('color_code');
        });
    }
};