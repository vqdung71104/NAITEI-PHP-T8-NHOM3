<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
{
    $rules = [
        'address_option' => 'required|in:existing,new',
        'notes' => 'nullable|string',
    ];

    if ($this->address_option === 'existing') {
        $rules['address_id'] = 'required|exists:addresses,id';
    } else {
        $rules = array_merge($rules, [
            'full_name'    => 'required|string|max:255',
            'phone_number' => ['required', 'regex:/^(?:\+84|0)\d{9,10}$/'],
            'details'      => 'required|string|max:255',
            'ward'         => 'required|string|max:255',
            'district'     => 'required|string|max:255',
            'city'         => 'required|string|max:255',
            'postal_code'  => 'nullable|string|max:20',
            'country'      => 'required|string|max:255',
        ]);
    }

    return $rules;
}


    public function messages(): array
    {
        return [
            'address_option.required' => 'Vui lòng chọn loại địa chỉ.',
            'address_id.required_if'  => 'Vui lòng chọn một địa chỉ đã lưu.',
            'full_name.required_if'   => 'Vui lòng nhập họ và tên.',
            'phone_number.required_if'=> 'Vui lòng nhập số điện thoại.',
            'phone_number.regex'      => 'Số điện thoại không đúng định dạng (+84xxxxxxxxx hoặc 0xxxxxxxxx).',
            'details.required_if'     => 'Vui lòng nhập địa chỉ chi tiết.',
            'ward.required_if'        => 'Vui lòng nhập phường/xã.',
            'district.required_if'    => 'Vui lòng nhập quận/huyện.',
            'city.required_if'        => 'Vui lòng nhập tỉnh/thành phố.',
            'country.required_if'     => 'Vui lòng nhập quốc gia.',
            'payment_method.in'       => 'Phương thức thanh toán không hợp lệ.',
        ];
    }
}
