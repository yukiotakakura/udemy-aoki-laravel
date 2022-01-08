<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:50',
            'information' => 'required|string|max:1000',
            'price' => 'required|integer',
            'sort_order' => 'nullable|integer',
            'quantity' => 'required|integer|between:0,99',
            // exists。参照先(親)が存在しているかどうか。 つまり、requestで送られたid(店舗)が、shopテーブルの「id」カラムに存在しているかどうか
            'shop_id' => 'required|exists:shops,id',
            // exists。参照先(親)が存在しているかどうか。 つまり、requestで送られたid(カテゴリid)が、secondary_categoriesテーブルの「id」カラムに存在しているかどうか
            'category' => 'required|exists:secondary_categories,id',
            'image1' => 'nullable|exists:images,id',
            'image2' => 'nullable|exists:images,id',
            'image3' => 'nullable|exists:images,id',
            'image4' => 'nullable|exists:images,id',
            'is_selling' => 'required|boolean'
        ];
    }
}
