<?php

declare(strict_types=1);

namespace Lumexa\CartSdk\Exceptions;

class CartValidationException extends ValidationException
{
    public static function invalidCartId(): self
    {
        return new self([
            'cart_id' => ['The cart ID must be a valid integer.'],
        ]);
    }

    public static function invalidUserId(): self
    {
        return new self([
            'user_id' => ['The user ID must be a valid integer.'],
        ]);
    }

    public static function invalidSessionId(): self
    {
        return new self([
            'session_id' => ['The session ID must be a valid string.'],
        ]);
    }

    public static function missingIdentifier(): self
    {
        return new self([
            'identifier' => ['Either user_id or session_id must be provided.'],
        ]);
    }
}
