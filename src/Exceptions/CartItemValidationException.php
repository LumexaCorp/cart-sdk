<?php

declare(strict_types=1);

namespace Lumexa\CartSdk\Exceptions;

class CartItemValidationException extends ValidationException
{
    public static function invalidItemId(): self
    {
        return new self([
            'item_id' => ['The item ID must be a valid integer.'],
        ]);
    }

    public static function invalidVariantId(): self
    {
        return new self([
            'variant_id' => ['The product variant ID must be a valid integer.'],
        ]);
    }

    public static function invalidQuantity(): self
    {
        return new self([
            'quantity' => ['The quantity must be a positive integer.'],
        ]);
    }

    public static function invalidAttributes(): self
    {
        return new self([
            'attributes' => ['The attributes must be a valid array.'],
        ]);
    }

    public static function outOfStock(): self
    {
        return new self([
            'stock' => ['The requested quantity is not available in stock.'],
        ]);
    }

    public static function maxQuantityExceeded(): self
    {
        return new self([
            'quantity' => ['The maximum quantity per item has been exceeded.'],
        ]);
    }
}
