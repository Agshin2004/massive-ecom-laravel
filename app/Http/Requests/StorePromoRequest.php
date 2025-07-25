<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePromoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // using $this->user() instead of $this->user
        // because FormRequest classes are resolved before the auth middleware runs
        // so the $this->user property is not populated YET
        // The user() method runs on FormRequest TO GET user (basically same thing that auth middleware would to)
        // but we do it manually since auth middleware is no t resolved yet
        return $this->user()->role === 'admin';
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->discount_percent && !$this->discount_amount) {
                $validator->errors()->add('discount', 'Either discount_percent or discount_amount must be specified.');
            }
        });
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'unique:App\Models\Promo,code', 'alpha:ascii'],
            'discount_percent' => ['numeric', 'min:1', 'max:100'],
            'discount_amount' => ['numeric', 'min:1'],
            'valid_till' => ['required', 'date'],
            'usage_limit' => ['required', 'numeric'],
            'per_usage_limit' => ['required', 'numeric'],
        ];
    }

    public function messages()
    {
        return [
            'valid_till.date' => 'Invalid datetime format. It must be ISO 8601 (best) OR any date format that strtotime can parse'
        ];
    }

    public function attributes()
    {
        return [
            'valid_till' => 'Valid Until'
        ];
    }
}
