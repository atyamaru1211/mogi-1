<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; //return Auth::check()
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'profile-image' => ['mimes:png,jpeg'],
        ];
    }

    public function messages()
    {
        return [
            'profile-image.mimes:png,jpeg' => '「.png」または「.jpeg」形式でアップロードしてください',
        ];
    }
}
