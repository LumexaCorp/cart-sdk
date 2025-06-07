<?php

declare(strict_types=1);

namespace Lumexa\CartSdk\Exceptions;

class ValidationException extends CartException
{
    /**
     * @param array<string, array<int, string>> $errors
     */
    public function __construct(
        private readonly array $errors,
        string $message = 'The given data was invalid.',
        int $code = 422,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): string
    {
        $firstField = array_key_first($this->errors);
        return $this->errors[$firstField][0] ?? $this->getMessage();
    }
}
