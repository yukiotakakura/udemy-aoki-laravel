<?php

namespace App\Models;

use App\Constants\Common;
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

    // 中間テーブルのリレーションナルメソッド
    public function users()
    {
        // belongsToMany()メソッド
        // 第1引数に「相手モデル」、第2引数に「中間テーブル名」
        // withPivot()メソッドで、引数に中間テーブルのカラム名を指定して取得する
        return $this->belongsToMany(User::class, 'carts')->withPivot(['id', 'quantity']);
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

    /**
     * 商品の並び順をセットする
     *
     * @param [type] $query
     * @param [type] $sortOrder
     * @return void
     */
    public function scopeSortOrder($query, $sortOrder)
    {
        // null || おすすめ順
        if($sortOrder === null || $sortOrder === Common::SORT_ORDER['recommend']){
            return $query->orderBy('sort_order', 'asc') ;
         }
        // 高い順
        if($sortOrder === Common::SORT_ORDER['higherPrice']){
            return $query->orderBy('price', 'desc') ;
        }
        // 安い順
        if($sortOrder === Common::SORT_ORDER['lowerPrice']){
            return $query->orderBy('price', 'asc') ;
        }
        // 古い順
        if($sortOrder === Common::SORT_ORDER['later']){
            return $query->orderBy('products.created_at', 'desc') ;
        }
        // 新しい順
        if($sortOrder === Common::SORT_ORDER['older']){
            return $query->orderBy('products.created_at', 'asc') ;
        }

    }

    /**
     * カテゴリを絞り込む
     *
     * @param [type] $query
     * @param [type] $categoryId
     * @return void
     */
    public function scopeSelectCategory($query, $categoryId)
    {
        if($categoryId !== '0')
        {
            // カテゴリが指定されている場合は検索
            return $query->where('secondary_category_id', $categoryId);
        } else {
            return ;
        }
    }

    /**
     * キーワード検索する
     *
     * @param [type] $query
     * @param [type] $keyword
     * @return void
     */
    public function scopeSearchKeyword($query, $keyword)
    {
        if(!is_null($keyword))
        {
           //全角スペースを半角に
           $spaceConvert = mb_convert_kana($keyword,'s');

           //空白で区切る
           $keywords = preg_split('/[\s]+/', $spaceConvert,-1,PREG_SPLIT_NO_EMPTY);

           //単語をループで回す
           foreach($keywords as $word)
           {
               $query->where('products.name','like','%'.$word.'%');
           }

           return $query;  

        } else {
            return;
        }
    }

}
