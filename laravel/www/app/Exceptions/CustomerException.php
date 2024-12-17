<?php

namespace App\Exceptions;

use Exception;

class CustomerException extends Exception
{
    protected $message;
    protected $code;
    protected array $errors;

    public function __construct(string $message , int $code, array $errors)
    {
        $this->message = $message;
        $this->code = $code;
        $this->errors = $errors;
        parent::__construct($message, $code);
    }

    /**
     * Render the exception as an HTTP response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function render()
    {
        return response()->json([
            'status' => 'FAILED',
            'status_code' => $this->code,
            'message' => $this->message,
            'errors' => $this->errors,
        ], $this->code);
    }
}
