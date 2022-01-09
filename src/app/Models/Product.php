<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    /**
     * 商品一覧を取得する
     * 在庫が1以上である商品(shops、カテゴリ、画像)を取得する
     *
     * @param [type] $query
     * @return void
     */
    public function scopeAvailableItems($query)
    {
        /** 
         * ①商品の在庫合計が1以上である「商品id」「在庫数」を取得するクエリ
         * SELECT `product_id` , sum(`quantity`) as `quantity`
         * FROM `t_stocks`
         * GROUP BY `product_id`
         * HAVING `quantity` >= 1
         */

        $stocks = DB::table('t_stocks')
                    ->select('product_id', DB::raw('sum(quantity) as quantity'))
                    ->groupBy('product_id')
                    ->having('quantity', '>=', 1);
                    return $query
                    // ここからサブクエリ。$stock変数の中に①の結果がある
                    // サブクエリ使う意図は、グルーピングした結果を軸にそこから紐づいている商品、shop、カテゴリ、画像を取得したい。グルーピングで取得できるのは、(1)グルーピングカラム (2)集約 である。
                    // つまり、在庫が1以上である商品(shops、カテゴリ、画像)を取得する
                    ->joinSub($stocks, 'stock', function($join){
                        $join->on('products.id', '=', 'stock.product_id');
                    })
                    ->join('shops', 'products.shop_id', '=', 'shops.id')
                    ->join('secondary_categories', 'products.secondary_category_id', '=', 'secondary_categories.id')
                    ->join('images as image1', 'products.image1', '=', 'image1.id')
                    ->where('shops.is_selling', true)
                    ->where('products.is_selling', true)
                    ->select('products.id as id', 'products.name as name', 'products.price','products.sort_order as sort_order'
                            ,'products.information', 'secondary_categories.name as category','image1.filename as filename');
    }

}
