<?php

namespace App\Http\Requests;

use App\Enums\Role;
use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // $authHeader = $this->header('authorization');
        // if ($authHeader) {
        //     throw new \Exception('')
        // }

        return true;  // TODO: Fix
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'alpha:ascii', 'unique:App\Models\User,username'],
            'email' => ['required', 'email', 'unique:App\Models\User,email'],
            'password' => ['required', 'min:8'],
            'phone_number' => ['required', 'unique:App\Models\User,phone_number'],
            // Get all enum values like ['user', 'moderator', 'admin']
            'role' => ['prohibited']
        ];
    }
}
