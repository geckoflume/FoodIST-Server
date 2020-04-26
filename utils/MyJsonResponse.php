<?php

use Symfony\Component\HttpFoundation\JsonResponse;

class MyJsonResponse extends JsonResponse
{
    public function __construct($data = null, int $status = 200, array $headers = [], bool $json = false)
    {
        parent::__construct($data, $status, $headers, $json);
        $this->setEncodingOptions(JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES);
    }
}