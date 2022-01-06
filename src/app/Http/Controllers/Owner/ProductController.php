<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Owner;
use App\Models\PrimaryCategory;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:owners');

        /**
         * オーナがURLパラメータを直打ちした場合、他オーナの商品(Product)が見れてしまうので下記のミドルウェアで対応する
         * 正しい仕様は、ログインしているオーナは、自分が作成した商品(Product)しか見れないようにする
         * コンストラクタにミドルウェアを配置することでページ読込の際に処理を挟むことができる
         */
        $this->middleware(function ($request, $next) {
            // パラメーターURLから値を取得
            $id = $request->route()->parameter('product'); 
            if(!is_null($id)){ 
            $productsOwnerId = Product::findOrFail($id)->shop->owner->id;
                $productId = (int)$productsOwnerId; 
                if($productId !== Auth::id()){ 
                    abort(404);
                }
            }
            return $next($request);
        });
    }

    /**
     * ログイン中オーナが登録した商品一覧画面表示
     * http://localhost:8082/owner/products
     * @return void
     */
    public function index()
    {
        // EagerLoadingなし
        //$ownerInfo = Owner::findOrFail(Auth::id())->shop->product;
        
        // N+1問題対策
        // Owner->shop->productのimageFirstカラムを取得する
        $ownerInfo = Owner::with('shop.product.imageFirst')->where('id', Auth::id())->get();

        // dd($ownerInfo);
        // foreach($ownerInfo as $owner){
        // //    dd($owner->shop->product);
        //     foreach($owner->shop->product as $product){
        //         dd($product->imageFirst->filename);
        //     }
        // }

        return view('owner.products.index',compact('ownerInfo'));
    }

    /**
     * 商品登録画面表示
     * http://localhost:8082/owner/products/create
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $shops = Shop::where('owner_id', Auth::id())->select('id', 'name')->get();

        $images = Image::where('owner_id', Auth::id())->select('id', 'title', 'filename')->orderBy('updated_at', 'desc')->get();

        // eager loadingで子テーブルも一緒に取得する
        // N+1問題対策
        $categories = PrimaryCategory::with('secondary')->get();

        return view('owner.products.create', compact('shops', 'images', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
