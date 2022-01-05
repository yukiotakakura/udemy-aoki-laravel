<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageService
{
	public static function upload($imageFile, $folderName){
		//dd($imageFile['image']);
		if(is_array($imageFile)){ // $imageFileが配列だった場合 (画像を複数登録すると画像は配列でrequestされる)
			$file = $imageFile['image'];
		} else {
			$file = $imageFile;
		}
		// ランダムなファイル名を生成
		$fileName = uniqid(rand().'_');
		// 拡張子を取得する
		$extension = $file->extension();
		$fileNameToStore = $fileName. '.' . $extension;
		// 画像をリサイズしてエンコード
		$resizedImage = Image::make($file)->resize(1920, 1080)->encode();
		// 画像をアップロード
		Storage::put('public/' . $folderName . '/' . $fileNameToStore, $resizedImage );

		return $fileNameToStore;
	}
}