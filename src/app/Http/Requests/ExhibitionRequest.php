<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
            'name' => ['required'],
            'description' => ['required', 'max:255'],
            'price' => ['required', 'integer', 'min:0'],
            'image_upload' => ['required', 'mimes:jpeg,png'],
            'condition' => ['required'],
            'category' => ['required', 'min:1'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '商品名を入力してください',
            'description.required' => '商品説明を入力してください',
            'description.max' => '商品説明は最大255文字までで入力してください',
            'price.required' => '商品価格を入力してください',
            'price.integer' => '商品価格は数字で入力してください',
            'price.min' => '商品価格は0円以上で入力してください',
            'image_upload.required' => '商品画像をアップロードしてください',
            'image_upload.mimes' => '拡張子が.jpegもしくは.pngの画像を選択してください',
            'condition.required' => '商品の状態を選択してください',
            'category.required' => 'カテゴリーを選択してください',
            'category.min' => 'カテゴリーは1つ以上選択してください',
        ];
    }
}
