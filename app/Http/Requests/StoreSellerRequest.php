<?php

namespace App\Http\Requests;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSellerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;  // TODO: Fix
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge($this->sellerRules(), $this->userRules());
    }

    public function sellerRules(): array
    {
        return [
            // regex allows ascii characters (a-z A-Z + white spaces)
            // tried to use alpha:ascii but since it does not allow whitespaces decided to write custom regex
            'store_name' => ['required', 'unique:App\Models\Seller,store_name', 'regex:/^[A-Za-z\s]+$/u'],
            'role' => ['prohibited'],
        ];
    }

    public function userRules(): array
    {
        return (new StoreUserRequest())->rules();
    }
}
