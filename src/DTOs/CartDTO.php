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
        public readonly int $storeId,
        public readonly ?string $sessionId,
        public readonly ?int $userId,
        public readonly string $status,
        public readonly float $total,
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
            storeId: $data['store_id'],
            sessionId: $data['session_id'] ?? null,
            userId: $data['user_id'] ?? null,
            status: $data['status'],
            total: (float) $data['total'],
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
            'store_id' => $this->storeId,
            'session_id' => $this->sessionId,
            'user_id' => $this->userId,
            'status' => $this->status,
            'total' => $this->total,
            'items' => array_map(fn (CartItemDTO $item) => $item->toArray(), $this->items),
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
