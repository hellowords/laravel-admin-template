<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostUser extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize ()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules ()
    {
        return [
            'account' => 'bail|required|string|unique:users',
            'email'   => 'bail|required|email|unique:users',
            'name'    => 'bail|required|string',
        ];
    }

    public function attributes ()
    {
        return [
            'account' => '账户',
            'email'   => 'E-mail',
            'name'    => '姓名',
        ];
    }
}
