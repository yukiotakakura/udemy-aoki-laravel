<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PrimaryCategory;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:users');
        /**
         * ユーザがURLパラメータを直打ちした場合、販売停止中(is_sellingが0)の商品が見れてしまうので下記のミドルウェアで対応する
         * 正しい仕様は、ログインしているユーザは、販売中の商品のみしか閲覧できない
         * コンストラクタにミドルウェアを配置することでページ読込の際に処理を挟むことができる
         */
        $this->middleware(function ($request, $next) {
            $id = $request->route()->parameter('item'); 
            if(!is_null($id)){ 
            // 在庫数が1以上 & 販売中である商品を取得して、その中にurl直打ちされた商品idが存在しているかどうかを検索する
            $itemId = Product::availableItems()->where('products.id', $id)->exists();
            
            // 存在していない場合は
            if(!$itemId){ 
                abort(404);
            }
            }
            return $next($request);
        });
    }

    /**
     * 商品一覧画面表示
     * http://localhost:8082/
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        //dd($request);

        // 同期的に送信
        // Mail::to('test@example.com')
        // ->send(new TestMail());
        
        // 非同期に送信
        // SendThanksMail::dispatch();

        $categories = PrimaryCategory::with('secondary')->get();

        // 在庫数が1以上 & 販売中である商品を取得する
        $products = Product::availableItems()
                            ->selectCategory($request->category ?? '0') // カテゴリ絞り込み
                            ->searchKeyword($request->keyword) // キーワード検索する
                            ->sortOrder($request->sort) // 「おすすめ順、高い順、安い順、古い順、新しい順」
                            ->paginate($request->pagination ?? '20'); // 初期表示において表示件数を設定していないと商品件数がおかしくなる対策として、デフォルトを20件とする

        return view('user.index', compact('products', 'categories'));
    }

    /**
     * 商品詳細画面表示
     *
     * @param [type] $id
     * @return void
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);
        $quantity = Stock::where('product_id', $product->id)->sum('quantity');

        if($quantity > 9){
            $quantity = 9;
          }

        return view('user.show', compact('product', 'quantity'));
    }

}
