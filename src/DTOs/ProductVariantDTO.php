<?php

declare(strict_types=1);

namespace Lumexa\CartSdk\DTOs;

class ProductVariantDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $sku,
        public readonly int $stock,
        public readonly array $attributes,
        public readonly ProductDTO $product,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            sku: $data['sku'],
            stock: $data['stock'],
            attributes: $data['attributes'],
            product: ProductDTO::fromArray($data['product']),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'attributes' => $this->attributes,
            'stock' => $this->stock,
            'product' => $this->product->toArray(),
        ];
    }
}
