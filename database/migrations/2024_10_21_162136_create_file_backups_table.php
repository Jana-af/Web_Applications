<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_backups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->cascadeOnDelete();
            $table->string('file_url');
            $table->integer('version');
            $table->unsignedBigInteger('modifier_id');
            $table->dateTime('version_date');
            $table->timestamps();
            $table->foreign('modifier_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file_backups');
    }
};
