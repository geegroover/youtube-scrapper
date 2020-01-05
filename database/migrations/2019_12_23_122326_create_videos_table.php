<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('channel_id');
            $table->string('video_id');
            $table->string('tags')->default(null);
            $table->integer('cycle_no')->default(1);
            $table->integer('view_count');
            // $table->integer('like-count');
            // $table->integer('dislike-count');
            // $table->integer('comment-count');
            $table->integer('rating')->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('videos');
    }
}
