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
    Schema::table('contents', function (Blueprint $table) {
        $table->string('category')->nullable()->after('type');
    });
}

public function down()
{
    Schema::table('contents', function (Blueprint $table) {
        $table->dropColumn('category');
    });
}

};
