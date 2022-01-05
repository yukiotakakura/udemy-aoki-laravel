<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id(); // ID
            $table->foreignId('owner_id') // 外部参照 オーナーID
            ->constrained()
            ->onUpdate('cascade')
            ->onDelete('cascade');
            $table->string('filename'); // ファイル名
            $table->string('title')->nullable(); // タイトル
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
        Schema::dropIfExists('images');
    }
}
