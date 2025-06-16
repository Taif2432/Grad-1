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
        $table->unsignedBigInteger('professional_id')->after('id');

        // Optional: add foreign key constraint
        $table->foreign('professional_id')->references('id')->on('users')->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('contents', function (Blueprint $table) {
        $table->dropForeign(['professional_id']);
        $table->dropColumn('professional_id');
    });
}
};
