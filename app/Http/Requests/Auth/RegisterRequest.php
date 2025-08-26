<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required','string','max:255'],
            'email' => ['required','email','unique:users,email'],
            'password' => ['required','confirmed','min:8','regex:/[a-z]/','regex:/[A-Z]/','regex:/[0-9]/'],
            'phone' => ['nullable','regex:/^[+0-9\-\s]{10,}$/'],
            'dob' => ['nullable','date','before_or_equal:'.now()->subYears(18)->toDateString()],
            'role' => ['required','in:farmer,bank,cooperative,verifier,government,buyer'],
            'gps_location' => ['nullable','regex:/^-?\d{1,2}\.\d+,\s*-?\d{1,3}\.\d+$/'],
            'org_name' => ['nullable','string','max:255'],
        ];
    }
}


