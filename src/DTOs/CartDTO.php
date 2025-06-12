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
        public readonly ?string $guestId,
        public readonly ?int $userId,
        public readonly string $status,
        public readonly int $total_items,
        public readonly float $total_price,
        public readonly array $items,
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
            guestId: $data['guest_id'] ?? null,
            userId: $data['user_id'] ?? null,
            status: $data['status'],
            total_items: (int) ($data['total_items'] ?? 0),
            total_price: (float) ($data['total_price'] ?? 0),
            items: array_map(
                fn (array $item) => CartItemDTO::fromArray($item),
                $data['items'] ?? []
            ),
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
            'guest_id' => $this->guestId,
            'user_id' => $this->userId,
            'status' => $this->status,
            'total_items' => $this->total_items,
            'total_price' => $this->total_price,
            'items' => array_map(fn (CartItemDTO $item) => $item->toArray(), $this->items),
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
