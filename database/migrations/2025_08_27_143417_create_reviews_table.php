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
    Schema::create('reviews', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');   // người viết review
        $table->unsignedBigInteger('book_id');   // sách được review
        $table->tinyInteger('rating')->default(0); // số sao 1–5
        $table->text('comment')->nullable();       // nội dung đánh giá
        $table->timestamps();

        // Khóa ngoại
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
    });
}

};
