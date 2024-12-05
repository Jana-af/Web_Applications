<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('file_actions_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('file_id')->cascadeOnDelete();
            $table->unsignedBigInteger('user_id')->cascadeOnDelete();
            $table->enum('action', ['UPLOAD', 'UPDATE', 'DELETE', 'MOVE', 'COPY', 'RENAME', 'CHECKIN', 'CHECKOUT']);
            $table->enum('status', ['STARTING', 'FAILED','SUCCESS']);
            $table->string('exception')->nullable();
            $table->unsignedBigInteger('to_group')->nullable();
            $table->string('old_file_name')->nullable();
            $table->string('new_file_name')->nullable();
            $table->timestamp('created_at');
            $table->foreign('to_group')->references('id')->on('groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file_actions_log');
    }
};
