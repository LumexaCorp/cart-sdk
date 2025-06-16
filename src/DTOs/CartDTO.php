<?php

declare(strict_types=1);

namespace Lumexa\CartSdk\DTOs;

class CartDTO
{
    /**
     * @param CartItemDTO[] $items
     */
    public function __construct(
        public readonly int $id,
        public readonly ?string $guest_id,
        public readonly ?int $user_id,
        public readonly string $status,
        public readonly int $total_items,
        public readonly float $total_price,
        public readonly array $items,
        public readonly string $created_at,
        public readonly string $updated_at,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            guest_id: $data['guest_id'] ?? null,
            user_id: $data['user_id'] ?? null,
            status: $data['status'],
            total_items: (int) ($data['total_items'] ?? 0),
            total_price: (float) ($data['total_price'] ?? 0),
            items: array_map(
                fn (array $item) => CartItemDTO::fromArray($item),
                $data['items'] ?? []
            ),
            created_at: $data['created_at'],
            updated_at: $data['updated_at'],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'guest_id' => $this->guest_id,
            'user_id' => $this->user_id,
            'status' => $this->status,
            'total_items' => $this->total_items,
            'total_price' => $this->total_price,
            'items' => array_map(fn (CartItemDTO $item) => $item->toArray(), $this->items),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
