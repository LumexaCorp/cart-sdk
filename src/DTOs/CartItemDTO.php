<?php

declare(strict_types=1);

namespace Lumexa\CartSdk\DTOs;

class CartItemDTO
{
    public function __construct(
        public readonly int $id,
        public readonly int $cartId,
        public readonly int $quantity,
        public readonly float $unitPrice,
        public readonly float $totalPrice,
        public readonly array $attributes,
        public readonly ?array $variant,
        public readonly string $createdAt,
        public readonly string $updatedAt,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            cartId: $data['cart_id'],
            quantity: $data['quantity'],
            unitPrice: (float) $data['unit_price'],
            totalPrice: (float) $data['total_price'],
            attributes: $data['attributes'] ?? [],
            variant: $data['variant'] ?? null,
            createdAt: $data['created_at'],
            updatedAt: $data['updated_at'],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'cart_id' => $this->cartId,
            'quantity' => $this->quantity,
            'unit_price' => $this->unitPrice,
            'total_price' => $this->totalPrice,
            'attributes' => $this->attributes,
            'variant' => $this->variant,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
