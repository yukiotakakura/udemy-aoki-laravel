<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PrimaryCategory;
use App\Models\Product;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:users');

        // $this->middleware(function ($request, $next) {

        //     $id = $request->route()->parameter('item'); 
        //     if(!is_null($id)){ 
        //     $itemId = Product::availableItems()->where('products.id', $id)->exists();
        //         if(!$itemId){ 
        //             abort(404);
        //         }
        //     }
        //     return $next($request);
        // });
    }
    public function index(Request $request)
    {
        // dd($request);

        // 同期的に送信
        // Mail::to('test@example.com')
        // ->send(new TestMail());
        
        // 非同期に送信
        // SendThanksMail::dispatch();

        $categories = PrimaryCategory::with('secondary')->get();

        // 在庫数が1以上である商品を取得する
        $products = Product::availableItems()
                            // ->selectCategory($request->category ?? '0')
                            // ->searchKeyword($request->keyword)
                            // ->sortOrder($request->sort)
                            ->paginate($request->pagination ?? '20');

        return view('user.index', compact('products', 'categories'));
    }

}
