<?php

use App\Http\Controllers\User\ItemController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('user.welcome');
});
// 【User】はログイン中のみ商品一覧画面を表示
Route::middleware('auth:users')->group(function(){
        Route::get('/', [ItemController::class, 'index'])->name('items.index');
        Route::get('show/{item}', [ItemController::class, 'show'])->name('items.show');
});


// Route::get('/dashboard', function () {
//     return view('user.dashboard');
// })->middleware(['auth:users'])->name('dashboard');

require __DIR__.'/auth.php';
