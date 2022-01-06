<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'name',
        'information',
        'price',
        'is_selling',
        'sort_order',
        'secondary_category_id',
        'image1',
        'image2',
        'image3',
        'image4',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function category()
    {
        return $this->belongsTo(SecondaryCategory::class, 'secondary_category_id');
    }

    /**
     * 商品画像1
     * メソッド名を「image1」にすると、バッティングしちゃうので「imageFirst」とする
     */
    public function imageFirst()
    {
        // リレーションを手動で設定する (基本的には命名があっていればlaravelが自動的にリレーションしてくれるが、今回は同じモデルを複数回リレーションしたい)
        // 第2引数に「FK」,第3引数に「親モデル名」を設定する
        return $this->belongsTo(Image::class, 'image1', 'id');
    }

    /**
     * 商品画像2
     * メソッド名を「image2」にすると、バッティングしちゃうので「imageSecond」とする
     */
    public function imageSecond()
    {
        // リレーションを手動で設定する (基本的には命名があっていればlaravelが自動的にリレーションしてくれるが、今回は同じモデルを複数回リレーションしたい)
        // 第2引数に「FK」,第3引数に「親モデル名」を設定する
        return $this->belongsTo(Image::class, 'image2', 'id');
    }

    /**
     * 商品画像3
     * メソッド名を「image3」にすると、バッティングしちゃうので「imageThird」とする
     */
    public function imageThird()
    {
        // リレーションを手動で設定する (基本的には命名があっていればlaravelが自動的にリレーションしてくれるが、今回は同じモデルを複数回リレーションしたい)
        // 第2引数に「FK」,第3引数に「親モデル名」を設定する
        return $this->belongsTo(Image::class, 'image3', 'id');
    }

    /**
     * 商品画像4
     * メソッド名を「image4」にすると、バッティングしちゃうので「imageFourth」とする
     */
    public function imageFourth()
    {
        // リレーションを手動で設定する (基本的には命名があっていればlaravelが自動的にリレーションしてくれるが、今回は同じモデルを複数回リレーションしたい)
        // 第2引数に「FK」,第3引数に「親モデル名」を設定する
        return $this->belongsTo(Image::class, 'image4', 'id');
    }

    public function stock()
    {
        return $this->hasMany(Stock::class);
    }

}
