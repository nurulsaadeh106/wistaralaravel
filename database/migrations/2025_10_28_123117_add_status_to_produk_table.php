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
    Schema::table('produk', function (Blueprint $table) {
        $table->enum('status', ['draft', 'aktif'])->default('draft')->after('gambar');
    });
}

public function down()
{
    Schema::table('produk', function (Blueprint $table) {
        $table->dropColumn('status');
    });
}
};
