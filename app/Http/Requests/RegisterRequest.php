<?php

namespace App\Http\Requests;

use App\Rules\StrongPassword;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'firstname' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\-\.\']+$/'],
            'middlename' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z\s\-\.\']*$/'],
            'lastname' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\-\.\']+$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => array_merge(
                ['required', 'string', 'confirmed'],
                [new StrongPassword]
            ),
            'id_number' => ['nullable', 'string', 'max:100', 'unique:users,id_number', 'regex:/^[a-zA-Z0-9\-]+$/'],
            'age' => ['nullable', 'integer', 'min:1', 'max:150'],
            'gender' => ['nullable', 'string', Rule::in(['male', 'female', 'other', 'prefer-not-to-say'])],
            'bday' => ['nullable', 'date', 'before:today'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
            'terms_accepted' => ['required', 'accepted'],
            'privacy_accepted' => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'firstname.regex' => 'First name may only contain letters, spaces, hyphens, dots, and apostrophes.',
            'middlename.regex' => 'Middle name may only contain letters, spaces, hyphens, dots, and apostrophes.',
            'lastname.regex' => 'Last name may only contain letters, spaces, hyphens, dots, and apostrophes.',
            'id_number.regex' => 'ID number may only contain letters, numbers, and hyphens.',
            'gender.in' => 'Please select a valid gender option.',
            'bday.before' => 'Birthday must be a date before today.',
            'terms_accepted.accepted' => 'You must accept the terms of service.',
            'privacy_accepted.accepted' => 'You must accept the privacy policy.',
        ];
    }
}
