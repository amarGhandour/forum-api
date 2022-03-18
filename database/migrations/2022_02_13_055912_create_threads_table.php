<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThreadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('threads', function (Blueprint $table) {
            $table->id();
            $table->string('title')->index();
            $table->string('slug')->unique()->nullable();
            $table->text('body');
            $table->boolean('locked')->default(0);
            $table->unsignedBigInteger('visits')->default(0);
            $table->unsignedBigInteger('replies_count')->default(0);
            $table->unsignedBigInteger('best_reply_id')->nullable();
            $table->foreign('best_reply_id')
                ->references('id')
                ->on('replies')
                ->onDelete('set null');
            $table->timestamps();
            $table->foreignId('user_id');
            $table->foreignId('channel_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('threads');
    }
}
