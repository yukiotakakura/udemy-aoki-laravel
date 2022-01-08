<?php

namespace App\Http\Controllers\Owner;

use App\Constants\Common;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Image;
use App\Models\Owner;
use App\Models\PrimaryCategory;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

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
     * 商品登録処理
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        try{
            DB::transaction(function () use($request) {
                $product = Product::create([
                    'name' => $request->name,
                    'information' => $request->information,
                    'price' => $request->price,
                    'sort_order' => $request->sort_order,
                    'shop_id' => $request->shop_id,
                    'secondary_category_id' => $request->category,
                    'image1' => $request->image1,
                    'image2' => $request->image2,
                    'image3' => $request->image3,
                    'image4' => $request->image4,
                    'is_selling' => $request->is_selling
                ]);

                Stock::create([
                    'product_id' => $product->id,
                    'type' => 1,
                    'quantity' => $request->quantity
                ]);
            }, 2);
        }catch(Throwable $e){
            Log::error($e);
            throw $e;
        }

        return redirect()->route('owner.products.index')->with(['message' => '商品登録しました。','status' => 'info']);
    }

    /**
     * 商品更新画面表示
     * http://localhost:8082/owner/products/7/edit
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);

        $quantity = Stock::where('product_id', $product->id)->sum('quantity');

        $shops = Shop::where('owner_id', Auth::id())->select('id', 'name')->get();

        $images = Image::where('owner_id', Auth::id())->select('id', 'title', 'filename')->orderBy('updated_at', 'desc')->get();

        $categories = PrimaryCategory::with('secondary')->get();

        return view('owner.products.edit', compact('product', 'quantity', 'shops', 'images', 'categories'));
    }

    /**
     * 商品更新処理
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, $id)
    {
        $request->validate([
            'current_quantity' => 'required|integer',
        ]);

        $product = Product::findOrFail($id);
        $quantity = Stock::where('product_id', $product->id)->sum('quantity');

        // 在庫数不整合チェック
        if($request->current_quantity !== $quantity){
            $id = $request->route()->parameter('product');
            return redirect()->route('owner.products.edit', [ 'product' => $id])
                    ->with(['message' => '在庫数が変更されています。再度確認してください。','status' => 'alert']);            

        } else {

            try{
                DB::transaction(function () use($request, $product) {
                    
                        $product->name = $request->name;
                        $product->information = $request->information;
                        $product->price = $request->price;
                        $product->sort_order = $request->sort_order;
                        $product->shop_id = $request->shop_id;
                        $product->secondary_category_id = $request->category;
                        $product->image1 = $request->image1;
                        $product->image2 = $request->image2;
                        $product->image3 = $request->image3;
                        $product->image4 = $request->image4;
                        $product->is_selling = $request->is_selling;
                        $product->save();

                    // 在庫数の追加の場合は (ラジオボタンの値が1)
                    if($request->type === Common::PRODUCT_LIST['add']){
                        $newQuantity = $request->quantity;
                    }
                    // 在庫数の削減の場合は (ラジオボタンの値が2)
                    if($request->type === Common::PRODUCT_LIST['reduce']){
                        $newQuantity = $request->quantity * -1;
                    }
                    Stock::create([
                        'product_id' => $product->id,
                        'type' => $request->type,
                        'quantity' => $newQuantity
                    ]);
                }, 2);
            }catch(Throwable $e){
                Log::error($e);
                throw $e;
            }
    
            return redirect()->route('owner.products.index')->with(['message' => '商品情報を更新しました。','status' => 'info']);
        }
    }

    /**
     * 商品削除処理
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Product::findOrFail($id)->delete(); 

        return redirect()->route('owner.products.index')->with(['message' => '商品を削除しました。','status' => 'alert']);
    }
}
