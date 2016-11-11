<?php

namespace App\Validators;

use Illuminate\Validation\Validator;

class RecaptchaValidator
{
    const RECAPTCHA_API_END_POINT = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * Validate a given attribute against a rule.
     *
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @param Validator $validator
     *
     * @return bool
     */
    public function validate($attribute, $value, $parameters, Validator $validator)
    {
        $response = json_decode($this->request($value), true);

        return $response['success'];
    }

    /**
     * Send the verify request.
     *
     * @param string $value
     *
     * @return string
     */
    protected function request($value)
    {
        $parameters = [
            'secret' => config('recaptcha.private_key'),
            'response' => $value,
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, self::RECAPTCHA_API_END_POINT);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }
}
