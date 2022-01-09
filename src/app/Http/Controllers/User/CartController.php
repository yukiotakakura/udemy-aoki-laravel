<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * カート保存処理
     *
     * @param Request $request
     * @return void
     */
    public function add(Request $request)
    {
        // 追加された商品がカートに存在するかどうかを検索
        $itemInCart = Cart::where('product_id', $request->product_id)->where('user_id', Auth::id())->first();

        // 追加された商品が既にカートに商品がある場合
        if($itemInCart){
            // 数量を+する
            $itemInCart->quantity += $request->quantity;
            $itemInCart->save();

        } else {
            // カートを登録
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'quantity' => $request->quantity
            ]);
        }
        
        return redirect()->route('user.cart.index');
    }
}
