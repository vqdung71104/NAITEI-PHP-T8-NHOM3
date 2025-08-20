<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'firstName'    => 'required|string|max:255',
            'lastName'     => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'details'      => 'required|string|max:500',
            'ward'         => 'required|string|max:100',
            'district'     => 'required|string|max:100',
            'city'         => 'required|string|max:100',
            'postal_code'  => 'required|string|max:20',
            'country'      => 'required|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'firstName.required' => 'Vui lòng nhập Họ.',
            'lastName.required'  => 'Vui lòng nhập Tên.',
            'phone_number.required' => 'Vui lòng nhập số điện thoại.',
            'details.required'   => 'Vui lòng nhập địa chỉ chi tiết.',
            'ward.required'      => 'Vui lòng nhập Phường/Xã.',
            'district.required'  => 'Vui lòng nhập Quận/Huyện.',
            'city.required'      => 'Vui lòng nhập Thành phố.',
            'postal_code.required' => 'Vui lòng nhập mã bưu điện.',
            'country.required'   => 'Vui lòng nhập Quốc gia.',
        ];
    }
}
