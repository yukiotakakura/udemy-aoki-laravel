<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // ID
            $table->string('name'); // 商品名
            $table->text('information'); // 情報
            $table->unsignedInteger('price'); // 値段 (データ型は符号なしの数値)
            $table->boolean('is_selling'); // 販売/停止
            $table->integer('sort_order')->nullable(); // 並び順
            // ★cascadeをつける理由
            // 親(Shopテーブル)を削除した時に、商品も合わせて削除したいのでcascadeを設定する
            $table->foreignId('shop_id')->constrained()->onUpdate('cascade')->onDelete('cascade'); // 店舗ID 
            // ★cascadeをつけない理由
            // 今回のこのudemy講座では、カテゴリを削除しないという仕様なので、cascadeは設定しない
            $table->foreignId('secondary_category_id')->constrained(); // 第2カテゴリ
            // null許容。商品1個に対して画像を4つまで登録することができる
            $table->foreignId('image1')->nullable()->constrained('images'); // 商品画像1
            $table->foreignId('image2')->nullable()->constrained('images'); // 商品画像2
            $table->foreignId('image3')->nullable()->constrained('images'); // 商品画像3
            $table->foreignId('image4')->nullable()->constrained('images'); // 商品画像4
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
        Schema::dropIfExists('products');
    }
}
