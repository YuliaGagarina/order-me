<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PIDMigrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'migration_modifier' => ['nullable', 'string', 'not_regex:/[<>]/'],
            'old_product_id' => ['required', 'string', 'not_regex:/[<>]/'],
            'new_product_id' => ['required', 'string', 'not_regex:/[<>]/'],
            'vendor_id' => ['required', 'integer'],
            'campaign_id' => ['nullable', 'integer'],
            'review_id' => ['nullable', 'integer'],
        ];
    }
}
