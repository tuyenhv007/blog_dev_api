<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('userId');
            $table->unsignedBigInteger('parentId');
            $table->string('title');
            $table->string('slug');
            $table->string('description');
            $table->longText('content');
            $table->tinyInteger('published');
            $table->timestamp('publishedAt');
            $table->timestamps();
        });

        Schema::create('post_tag', function (Blueprint $table) {
            $table->unsignedBigInteger('postId');
            $table->unsignedBigInteger('tagId');
            $table->timestamps();
        });

        Schema::create('post_category', function (Blueprint $table) {
            $table->unsignedBigInteger('postId');
            $table->unsignedBigInteger('categoryId');
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
        Schema::dropIfExists('posts');
        Schema::dropIfExists('post_tags');
        Schema::dropIfExists('post_category');
    }
}
