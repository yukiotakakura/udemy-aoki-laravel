<?php

namespace App\Http\Controllers\User;

use App\Constants\Common;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * カート画面
     * http://localhost:8082/cart
     * @return void
     */
    public function index()
    {
        $user = User::findOrFail(Auth::id());
        $products = $user->products;

        $totalPrice = 0;
        foreach($products as $product){
            $totalPrice += $product->price * $product->pivot->quantity;
        }

        // dd($products, $totalPrice);

        return view('user.cart', compact('products', 'totalPrice'));
    }

    /**
     * カート追加処理
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

    /**
     * カート削除処理
     *
     * @param [type] $id
     * @return void
     */
    public function delete($id)
    {
        Cart::where('product_id', $id)->where('user_id', Auth::id())->delete();

        return redirect()->route('user.cart.index');
    }

    /**
     * 決済処理
     */
    public function checkout()
    {
        $user = User::findOrFail(Auth::id());
        $products = $user->products;
        
        // stripeに送信する変数$lineItems を初期化
        $lineItems = [];
        foreach($products as $product){
            $quantity = '';
            // 商品の在庫数を取得
            $quantity = Stock::where('product_id', $product->id)->sum('quantity');

            // カート内の商品個数 > 商品の在庫数
            if($product->pivot->quantity > $quantity){
                return redirect()->route('user.cart.index');
            } else {
                $lineItem = [
                    'name' => $product->name,
                    'description' => $product->information,
                    'amount' => $product->price,
                    'currency' => 'jpy',
                    'quantity' => $product->pivot->quantity,
                ];
                array_push($lineItems, $lineItem);    
            }
        }
        // 新しい在庫数を更新する
        foreach($products as $product){
            Stock::create([
                'product_id' => $product->id,
                'type' => Common::PRODUCT_LIST['reduce'],
                'quantity' => $product->pivot->quantity * -1
            ]);
        }

        // 公開鍵をセット
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'], // カード支払い
            'line_items' => [$lineItems], // 購入商品
            'mode' => 'payment', // 1回払い
            'success_url' => route('user.cart.success'), // 決済成功後の遷移先
            'cancel_url' => route('user.cart.cancel'), // 決済失敗後の遷移先
        ]);

        // 秘密鍵をセット
        $publicKey = env('STRIPE_PUBLIC_KEY');

        return view('user.checkout', compact('session', 'publicKey'));

    }

    public function success()
    {
        // Userのカートを取得
        $items = Cart::where('user_id', Auth::id())->get();
        // // 
        // $products = CartService::getItemsInCart($items);
        // $user = User::findOrFail(Auth::id());

        // SendThanksMail::dispatch($products, $user);
        // foreach($products as $product)
        // {
        //     SendOrderedMail::dispatch($product, $user);
        // }
        // // dd('ユーザーメール送信テスト');
        // ////
        // Cart::where('user_id', Auth::id())->delete();

        return redirect()->route('user.items.index');
    }

    /**
     * stripe画面でキャンセルされた場合の処理
     *
     * @return void
     */
    public function cancel()
    {
        $user = User::findOrFail(Auth::id());

        // strip画面でキャンセルされたら、在庫を戻す
        foreach($user->products as $product){
            Stock::create([
                'product_id' => $product->id,
                'type' => Common::PRODUCT_LIST['add'],
                'quantity' => $product->pivot->quantity
            ]);
        }

        return redirect()->route('user.cart.index');
    }
}
