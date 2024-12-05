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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->string('file_url');
            $table->enum('status', ['FREE', 'RESERVED'])->default('FREE');
            $table->timestamp('check_in_time')->nullable();
            $table->unsignedBigInteger('current_reserver_id')->nullable();
            $table->unsignedBigInteger('publisher_id');
            $table->foreignId('group_id')->cascadeOnDelete();
            $table->integer('is_accepted')->default(0);
            $table->timestamps();
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
       });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
};
