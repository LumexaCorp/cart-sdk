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
    private Client $httpClient;

    public function __construct(
        private readonly string $baseUrl,
        private readonly string $storeToken,
        ?Client $httpClient = null
    ) {
        $this->httpClient = $httpClient ?? new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'X-Store-Token' => $this->storeToken,
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
            $response = $this->httpClient->get("/api/carts/{$cartId}");
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
            $response = $this->httpClient->post('/api/carts', [
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
     * @param array $data ['product_variant_id' => int, 'quantity' => int, 'attributes' => array, 'unit_price' => float]
     * @return CartItemDTO
     *
     * @throws CartException
     * @throws CartValidationException
     * @throws CartItemValidationException
     * @throws ValidationException
     */
    public function addItem(int $cartId, array $data): CartItemDTO
    {
        if ($cartId <= 0) {
            throw CartValidationException::invalidCartId();
        }

        if ($data['product_variant_id'] <= 0) {
            throw CartItemValidationException::invalidVariantId();
        }

        if ($data['quantity'] <= 0) {
            throw CartItemValidationException::invalidQuantity();
        }

        try {
            $response = $this->httpClient->post("/api/carts/{$cartId}/items", [
                'json' => [
                    'product_variant_id' => $data['product_variant_id'],
                    'quantity' => $data['quantity'],
                    'attributes' => $data['attributes'],
                ],
            ]);

            $data = json_decode((string) $response->getBody(), true);

            return CartItemDTO::fromArray($data['data']);
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
            $response = $this->httpClient->put("/api/carts/{$cartId}/items/{$itemId}", [
                'json' => [
                    'quantity' => $quantity,
                ],
            ]);

            $data = json_decode((string) $response->getBody(), true);

            return CartItemDTO::fromArray($data['data']);
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
            $this->httpClient->delete("/api/carts/{$cartId}/items/{$itemId}");
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
            $this->httpClient->delete("/api/carts/{$cartId}/items");
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
            $response = $this->httpClient->get("/api/carts/{$cartId}/items");
            $data = json_decode((string) $response->getBody(), true);

            return array_map(fn (array $item) => CartItemDTO::fromArray($item), $data['data']);
        } catch (\Throwable $e) {
            $this->handleApiError($e);
        }
    }

    public function getCartByGuestId(string $guestId): ?CartDTO
    {
        try {
            $response = $this->httpClient->get("/api/carts/guest/{$guestId}");

            $data = json_decode((string) $response->getBody(), true);

            return CartDTO::fromArray($data['data']);
        } catch (\Throwable $e) {
            $this->handleApiError($e);
        }
    }
}
