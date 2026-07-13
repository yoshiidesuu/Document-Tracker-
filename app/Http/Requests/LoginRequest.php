<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'credential' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'credential.required' => 'Please enter your email address or ID number.',
            'password.required' => 'Please enter your password.',
        ];
    }

    public function findUser(): ?\App\Models\User
    {
        $value = $this->input('credential');
        return \App\Models\User::where('email', $value)
            ->orWhere('id_number', $value)
            ->first();
    }
}
