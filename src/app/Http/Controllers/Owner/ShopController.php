<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadImageRequest;
use App\Models\Shop;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:owners');

        /**
         * オーナがURLパラメータを直打ちした場合、他オーナのshopが見れてしまうので下記のミドルウェアで対応する
         * 正しい仕様は、ログインしているオーナは、自分のShopしか見れないようにする
         * コンストラクタにミドルウェアを配置することでページ読込の際に処理を挟むことができる
         */
        $this->middleware(function ($request, $next) {
            // dd($request->route()->parameter('shop')); //文字列
            // dd(Auth::id()); //数字

            $id = $request->route()->parameter('shop'); //shopのid取得
            if(!is_null($id)){ // null判定
            $shopsOwnerId = Shop::findOrFail($id)->owner->id;
                $shopId = (int)$shopsOwnerId; // キャスト 文字列→数値に型変換
                $ownerId = Auth::id();
                if($shopId !== $ownerId){ // 同じでなかったら
                    abort(404); // 404画面表示
                }
            }
            return $next($request);
        });
    } 

    /**
     * ログインしているオーナが作ったShop画面を表示
     * http://localhost:8082/owner/shops/index
     * @return void
     */
    public function index()
    {
        // Auth::id()でログイン済みidを取得できる
        $shops = Shop::where('owner_id', Auth::id())->get();

        return view('owner.shops.index', compact('shops'));
    }

	/**
	 * ログイン中オーナの店舗変更画面を表示
	 * http://localhost:8082/owner/shops/edit/1
	 * @param [type] $id
	 * @return void
	 */
    public function edit($id)
    {
        $shop = Shop::findOrFail($id);
        // dd(Shop::findOrFail($id));
        return view('owner.shops.edit', compact('shop'));
    }

	/**
	 * ログイン中オーナの店舗情報更新処理
	 * 
	 * @param UploadImageRequest $request
	 * @param [type] $id
	 * @return void
	 */
    public function update(UploadImageRequest $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'information' => 'required|string|max:1000',
            'is_selling' => 'required',
        ]);

        $imageFile = $request->image;
        if(!is_null($imageFile) && $imageFile->isValid() ){
            $fileNameToStore = ImageService::upload($imageFile, 'shops');    
        }

        $shop = Shop::findOrFail($id);
        $shop->name = $request->name;
        $shop->information = $request->information;
        $shop->is_selling = $request->is_selling;
        if(!is_null($imageFile) && $imageFile->isValid()){
            $shop->filename = $fileNameToStore;
        }

        $shop->save();

        return redirect()
                ->route('owner.shops.index')
                ->with(['message' => '店舗情報を更新しました。','status' => 'info']);

    }
}
