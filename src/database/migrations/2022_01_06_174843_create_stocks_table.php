<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_stocks', function (Blueprint $table) {
            $table->id(); // ID
            $table->foreignId('product_id')->constrained()->onUpdate('cascade')->onDelete('cascade'); // 商品ID 商品が消えたらストックも全部消すという仕様にする
            $table->tinyInteger('type'); // 入庫/出庫
            $table->integer('quantity'); // 量
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
        Schema::dropIfExists('t_stocks');
    }
}
