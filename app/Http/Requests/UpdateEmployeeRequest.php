<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|string',
            'gender' => 'required|string|in:MALE,FEMALE',
            'age' => 'required|integer',
            'phone' => 'required|string',
            'photo' => 'nullable|image|mimes:png,jpg,jpeg,svg,gif|max:2048',
            'role_id' => 'required|integer|exists:roles,id',
            'team_id' => 'required|integer|exists:teams,id'
        ];
    }
}
