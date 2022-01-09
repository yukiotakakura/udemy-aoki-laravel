<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 中間テーブル
        Schema::create('carts', function (Blueprint $table) {
            $table->id(); // ID
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade'); // user_id
            $table->foreignId('product_id')->constrained()->onUpdate('cascade')->onDelete('cascade'); // product_id
            $table->integer('quantity'); // カートの商品数
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
        Schema::dropIfExists('carts');
    }
}
