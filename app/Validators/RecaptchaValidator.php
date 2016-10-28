<?php

namespace App\Validators;

use GuzzleHttp\Client;
use Illuminate\Validation\Validator;

class RecaptchaValidator
{
    const RECAPTCHA_API_END_POINT = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * @var Client
     */
    protected $client;

    /**
     * Constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

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
        $response = json_decode(
            $this->request($value)->getBody()->getContents(),
            true
        );

        return $response['success'];
    }

    /**
     * Send the verify request.
     *
     * @param string $value
     *
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    protected function request($value)
    {
        return $this->client
            ->post(self::RECAPTCHA_API_END_POINT, [
                'body' => [
                    'secret' => config('recaptcha.private_key'),
                    'response' => $value,
                ],
            ]);
    }
}
