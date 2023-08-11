<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrdersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['products' => array_keys($this->request->all(), 'on')]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'products' => 'required'
        ];
    }
}
