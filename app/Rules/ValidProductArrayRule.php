<?php
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidProductArrayRule implements Rule
{
    public function passes($attribute, $value)
    {
        if (!is_array($value)) {
            return false;
        }

        foreach ($value as $product) {
            if (!is_array($product)) {
                return false;
            }

            if (!isset($product['product_id']) || !is_int($product['product_id'])) {
                return false;
            }

            if (!isset($product['quantity']) || !is_int($product['quantity'])) {
                return false;
            }
        }

        return true;
    }

    public function message()
    {
        return ':attribute alanına sayı değerleri girmelisiniz!';
    }
}
