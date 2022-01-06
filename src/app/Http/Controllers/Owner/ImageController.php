<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadImageRequest;
use App\Models\Image;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:owners');

        /**
         * オーナがURLパラメータを直打ちした場合、他オーナの商品画像(Image)が見れてしまうので下記のミドルウェアで対応する
         * 正しい仕様は、ログインしているオーナは、自分の商品画像(Image)しか見れないようにする
         * コンストラクタにミドルウェアを配置することでページ読込の際に処理を挟むことができる
         */
        $this->middleware(function ($request, $next) {

            $id = $request->route()->parameter('image'); 
            if(!is_null($id)){ 
            $imagesOwnerId = Image::findOrFail($id)->owner->id;
                $imageId = (int)$imagesOwnerId; 
                if($imageId !== Auth::id()){ 
                    abort(404);
                }
            }
            return $next($request);
        });
    } 

    /**
     * ログイン中オーナの画像一覧画面表示
     * http://localhost:8082/owner/images
     * @return void
     */
    public function index()
    {
        $images = Image::where('owner_id', Auth::id())
                        ->orderBy('updated_at', 'desc')
                        ->paginate(20);

        return view('owner.images.index', 
        compact('images'));
    }

    /**
     * 【オーナー】画像登録画面表示
     * http://localhost:8082/owner/images/create
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('owner.images.create');
    }

    /**
     * 画像登録処理
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UploadImageRequest $request)
    {
        $imageFiles = $request->file('files');
        if(!is_null($imageFiles)){ // バリデーションで必須チェックしているが念の為、null判定
            foreach($imageFiles as $imageFile){ // 画像の数でループを回す
                $fileNameToStore = ImageService::upload($imageFile, 'products');    
                Image::create([
                    'owner_id' => Auth::id(),
                    'filename' => $fileNameToStore  
                ]);
            }
        }

        return redirect()
                ->route('owner.images.index')
                ->with(['message' => '画像登録を実施しました。','status' => 'info']);
    }
    
    /**
     * イメージ更新画面表示
     * http://localhost:8082/owner/images/3/edit
     * @param [type] $id
     * @return void
     */
    public function edit($id)
    {
        $image = Image::findOrFail($id);
        return view('owner.images.edit', compact('image'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'string|max:50'
        ]);

        $image = Image::findOrFail($id);
        $image->title = $request->title;
        $image->save();

        return redirect()
                ->route('owner.images.index')
                ->with(['message' => '画像情報を更新しました。','status' => 'info']);
    }


    public function destroy($id)
    {
        $image = Image::findOrFail($id);

        // $imageInProducts = Product::where('image1', $image->id)
        // ->orWhere('image2', $image->id)
        // ->orWhere('image3', $image->id)
        // ->orWhere('image4', $image->id)
        // ->get();

        // if($imageInProducts){
        //     $imageInProducts->each(function($product) use($image){
        //         if($product->image1 === $image->id){
        //             $product->image1 = null;
        //             $product->save();
        //         }
        //         if($product->image2 === $image->id){
        //             $product->image2 = null;
        //             $product->save();
        //         }
        //         if($product->image3 === $image->id){
        //             $product->image3 = null;
        //             $product->save();
        //         }
        //         if($product->image4 === $image->id){
        //             $product->image4 = null;
        //             $product->save();
        //         }
        //     });
        // }

        // 画像のファイルパスを取得
        $filePath = 'public/products/' . $image->filename;
        
        // 画像削除
        if(Storage::exists($filePath)){
            Storage::delete($filePath);
        }

        Image::findOrFail($id)->delete(); 

        return redirect()
                ->route('owner.images.index')
                ->with(['message' => '画像を削除しました。','status' => 'alert']);
    }
}
