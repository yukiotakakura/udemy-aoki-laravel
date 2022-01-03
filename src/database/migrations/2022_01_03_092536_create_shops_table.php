<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopsTable extends Migration
{
    /**
     * Shopテーブル定義
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id(); // id
            // 参照先(ownerテーブル)が更新or削除されたときにこのテーブル(shopテーブル)も一緒に更新or削除するように設定する
            $table->foreignId('owner_id')->constrained()->onUpdate('cascade')->onDelete('cascade'); // オーナID shopテーブルの「id」カラムを外部参照する
            $table->string('name'); // 名前
            $table->text('information'); // 情報
            $table->string('filename'); // 画像
            $table->boolean('is_selling'); // 販売/停止
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
        Schema::dropIfExists('shops');
    }
}
