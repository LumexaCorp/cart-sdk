<?php

declare(strict_types=1);

namespace Lumexa\CartSdk;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Lumexa\CartSdk\DTOs\CartDTO;
use Lumexa\CartSdk\DTOs\CartItemDTO;
use Lumexa\CartSdk\Exceptions\CartException;
use Lumexa\CartSdk\Exceptions\CartValidationException;
use Lumexa\CartSdk\Exceptions\CartItemValidationException;
use Lumexa\CartSdk\Exceptions\ValidationException;

class CartClient
{
    private Client $client;

    public function __construct(
        private readonly string $baseUrl,
        private readonly string $apiKey,
        private readonly int $storeId,
    ) {
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Authorization' => "Bearer {$this->apiKey}",
                'X-Store-ID' => $this->storeId,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Handle API errors and transform them into appropriate exceptions
     *
     * @throws ValidationException|CartException
     */
    private function handleApiError(\Throwable $e): never
    {
        if ($e instanceof ClientException) {
            $response = $e->getResponse();
            $statusCode = $response->getStatusCode();
            $body = json_decode((string) $response->getBody(), true);

            if ($statusCode === 422 && isset($body['errors'])) {
                throw new ValidationException(
                    $body['errors'],
                    $body['message'] ?? 'Validation failed',
                    $statusCode,
                    $e
                );
            }

            if (isset($body['message'])) {
                throw new CartException($body['message'], $statusCode, $e);
            }
        }

        throw new CartException($e->getMessage(), (int) $e->getCode(), $e);
    }

    /**
     * Récupère un panier par son ID
     *
     * @throws CartException
     * @throws CartValidationException
     * @throws ValidationException
     */
    public function getCart(int $cartId): CartDTO
    {
        if ($cartId <= 0) {
            throw CartValidationException::invalidCartId();
        }

        try {
            $response = $this->client->get("/api/carts/{$cartId}");
            $data = json_decode((string) $response->getBody(), true);

            return CartDTO::fromArray($data);
        } catch (\Throwable $e) {
            $this->handleApiError($e);
        }
    }

    /**
     * Crée un nouveau panier
     *
     * @throws CartException
     * @throws CartValidationException
     * @throws ValidationException
     */
    public function createCart(?string $sessionId = null, ?int $userId = null): CartDTO
    {
        if ($sessionId === null && $userId === null) {
            throw CartValidationException::missingIdentifier();
        }

        if ($userId !== null && $userId <= 0) {
            throw CartValidationException::invalidUserId();
        }

        if ($sessionId !== null && trim($sessionId) === '') {
            throw CartValidationException::invalidSessionId();
        }

        try {
            $response = $this->client->post('/api/carts', [
                'json' => [
                    'session_id' => $sessionId,
                    'user_id' => $userId,
                ],
            ]);

            $data = json_decode((string) $response->getBody(), true);

            return CartDTO::fromArray($data);
        } catch (\Throwable $e) {
            $this->handleApiError($e);
        }
    }

    /**
     * Ajoute un produit au panier
     *
     * @throws CartException
     * @throws CartValidationException
     * @throws CartItemValidationException
     * @throws ValidationException
     */
    public function addItem(int $cartId, int $variantId, int $quantity, array $attributes = []): CartItemDTO
    {
        if ($cartId <= 0) {
            throw CartValidationException::invalidCartId();
        }

        if ($variantId <= 0) {
            throw CartItemValidationException::invalidVariantId();
        }

        if ($quantity <= 0) {
            throw CartItemValidationException::invalidQuantity();
        }

        try {
            $response = $this->client->post("/api/carts/{$cartId}/items", [
                'json' => [
                    'product_variant_id' => $variantId,
                    'quantity' => $quantity,
                    'attributes' => $attributes,
                ],
            ]);

            $data = json_decode((string) $response->getBody(), true);

            return CartItemDTO::fromArray($data);
        } catch (\Throwable $e) {
            $this->handleApiError($e);
        }
    }

    /**
     * Met à jour la quantité d'un produit dans le panier
     *
     * @throws CartException
     * @throws CartValidationException
     * @throws CartItemValidationException
     * @throws ValidationException
     */
    public function updateItemQuantity(int $cartId, int $itemId, int $quantity): CartItemDTO
    {
        if ($cartId <= 0) {
            throw CartValidationException::invalidCartId();
        }

        if ($itemId <= 0) {
            throw CartItemValidationException::invalidItemId();
        }

        if ($quantity <= 0) {
            throw CartItemValidationException::invalidQuantity();
        }

        try {
            $response = $this->client->put("/api/carts/{$cartId}/items/{$itemId}", [
                'json' => [
                    'quantity' => $quantity,
                ],
            ]);

            $data = json_decode((string) $response->getBody(), true);

            return CartItemDTO::fromArray($data);
        } catch (\Throwable $e) {
            $this->handleApiError($e);
        }
    }

    /**
     * Supprime un produit du panier
     *
     * @throws CartException
     * @throws CartValidationException
     * @throws CartItemValidationException
     * @throws ValidationException
     */
    public function removeItem(int $cartId, int $itemId): void
    {
        if ($cartId <= 0) {
            throw CartValidationException::invalidCartId();
        }

        if ($itemId <= 0) {
            throw CartItemValidationException::invalidItemId();
        }

        try {
            $this->client->delete("/api/carts/{$cartId}/items/{$itemId}");
        } catch (\Throwable $e) {
            $this->handleApiError($e);
        }
    }

    /**
     * Vide le panier
     *
     * @throws CartException
     * @throws CartValidationException
     * @throws ValidationException
     */
    public function clearCart(int $cartId): void
    {
        if ($cartId <= 0) {
            throw CartValidationException::invalidCartId();
        }

        try {
            $this->client->delete("/api/carts/{$cartId}/items");
        } catch (\Throwable $e) {
            $this->handleApiError($e);
        }
    }

    /**
     * Récupère les produits d'un panier
     *
     * @return CartItemDTO[]
     * @throws CartException
     * @throws CartValidationException
     * @throws ValidationException
     */
    public function getItems(int $cartId): array
    {
        if ($cartId <= 0) {
            throw CartValidationException::invalidCartId();
        }

        try {
            $response = $this->client->get("/api/carts/{$cartId}/items");
            $data = json_decode((string) $response->getBody(), true);

            return array_map(fn (array $item) => CartItemDTO::fromArray($item), $data);
        } catch (\Throwable $e) {
            $this->handleApiError($e);
        }
    }
}
