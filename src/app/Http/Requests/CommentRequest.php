<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CommentRequest extends FormRequest
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
            'content' => ['required','max:255'],
        ];
    }

    public function messages()
    {
        return [
            'content.required' => '商品コメントを入力してください',
            'content.max' => '商品コメントは255文字以内で入力してください',
        ];
    }
}
