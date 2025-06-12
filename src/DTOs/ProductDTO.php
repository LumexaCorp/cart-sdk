<?php

declare(strict_types=1);

namespace Lumexa\CartSdk\DTOs;

class ProductDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly string $description,
        public readonly string $image,
        public readonly string $price,
        public readonly string $product_type,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            slug: $data['slug'],
            description: $data['description'],
            image: $data['image'],
            price: $data['price'],
            product_type: $data['product_type'],
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'image' => $this->image,
            'price' => $this->price,
            'product_type' => $this->product_type,
        ];
    }
}
