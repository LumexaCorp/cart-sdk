<?php

declare(strict_types=1);

namespace Lumexa\CartSdk\DTOs;

class CartItemDTO
{
    public function __construct(
        public readonly int $id,
        public readonly int $cartId,
        public readonly int $quantity,
        public readonly float $unit_price,
        public readonly float $total_price,
        public readonly array $attributes,
        public readonly ?ProductVariantDTO $product_variant,
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
            unit_price: (float) $data['unit_price'],
            total_price: (float) $data['total_price'],
            attributes: $data['attributes'] ?? [],
            product_variant: ProductVariantDTO::fromArray($data['product_variant']),
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
            'unit_price' => $this->unit_price,
            'total_price' => $this->total_price,
            'attributes' => $this->attributes,
            'product_variant' => $this->product_variant->toArray(),
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
