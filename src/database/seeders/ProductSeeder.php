<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('products')->insert([
            [
                'name' => '商品名1',
                'information' => '商品概要',
                'price' => 1000,
                'is_selling' => true,
                'shop_id' => 1,
                'secondary_category_id' => 1,
                'image1' => 1,
            ],
            [
                'name' => '商品名2',
                'information' => '商品概要',
                'price' => 2000,
                'is_selling' => true,
                'shop_id' => 1,
                'secondary_category_id' => 2,
                'image1' => 2,
            ],
            [
                'name' => '商品名3',
                'information' => '商品概要',
                'price' => 3000,
                'is_selling' => true,
                'shop_id' => 1,
                'secondary_category_id' => 3,
                'image1' => 3,
            ],
            [
                'name' => '商品名4',
                'information' => '商品概要',
                'price' => 4000,
                'is_selling' => true,
                'shop_id' => 1,
                'secondary_category_id' => 4,
                'image1' => 3,
            ],
            [
                'name' => '商品名5',
                'information' => '商品概要',
                'price' => 5000,
                'is_selling' => true,
                'shop_id' => 1,
                'secondary_category_id' => 5,
                'image1' => 4,
            ],
        ]);
    }
}
