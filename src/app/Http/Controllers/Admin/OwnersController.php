<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class OwnersController extends Controller
{

    /**
     * 新しいUserControllerインスタンスの生成
     *
     * @return void
     */
    public function __construct()
    {
        // guardの「admin」で認証しているかどうか
        $this->middleware('auth:admin');
    }
    /**
     * オーナー一覧表示
     * http://localhost:8082/admin/owners
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $owners = Owner::select(['id','name', 'email', 'created_at'])->get();
        
        return view('admin.owners.index', compact('owners'));
    }

    /**
     * オーナー登録画面表示
     * http://localhost:8082/admin/owners/create
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.owners.create');
    }

    /**
     * オーナー新規登録
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // バリデーション
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:owners',
            'password' => 'required|string|confirmed|min:8',
        ]);

        Owner::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()
                ->route('admin.owners.index')
                ->with(['message' => 'オーナー登録を実施しました。','status' => 'info']);
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
     * オーナー編集画面表示
     * http://localhost:8082/admin/owners/1/edit
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // idが存在しなければ404画面に飛ばす
        $owner = Owner::findOrFail($id);
        // dd($owner);
        return view('admin.owners.edit', compact('owner'));
    }

    /**
     * オーナー更新処理
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // idが存在しなければ404画面に飛ばす
        $owner = Owner::findOrFail($id);
        $owner->name = $request->name;
        $owner->email = $request->email;
        $owner->password = Hash::make($request->password);
        $owner->save();

        return redirect()
                ->route('admin.owners.index')
                ->with(['message' => 'オーナー情報を更新しました。',
                'status' => 'info']);
    }

    /**
     * オーナー削除処理(ソフトデリート:論理削除s)
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Owner::findOrFail($id)->delete(); //ソフトデリート

        return redirect()
                ->route('admin.owners.index')
                ->with(['message' => 'オーナー情報を削除しました。',
                'status' => 'alert']);
    }

    /**
     * 論理削除済み(ソフトデリート)オーナー一覧画面表示
     * http://localhost:8082/admin/expired-owners/index
     * @return void
     */
    public function expiredOwnerIndex(){
        // 論理削除済みオーナーを取得
        $expiredOwners = Owner::onlyTrashed()->get();
        return view('admin.expired-owners', compact('expiredOwners'));
    }
    
    /**
     * オーナー完全削除機能
     * @param [type] $id
     * @return void
     */
    public function expiredOwnerDestroy($id){
        // 物理削除する
        Owner::onlyTrashed()->findOrFail($id)->forceDelete();
        return redirect()->route('admin.expired-owners.index'); 
    }
}
