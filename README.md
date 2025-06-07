# Cart SDK

SDK pour interagir avec l'API Cart de Lumexa.

## Installation

```bash
composer require lumexa/cart-sdk
```

## Configuration

Pour utiliser le SDK, vous devez créer une instance de `CartClient` avec les paramètres suivants :

```php
use Lumexa\CartSdk\CartClient;

$client = new CartClient(
    baseUrl: 'https://api.lumexa.io',
    storeToken: 'votre-token-de-store'
);
```

## Utilisation

### Créer un panier

```php
// Créer un panier pour un utilisateur connecté
$cart = $client->createCart(userId: 123);

// Créer un panier pour un utilisateur anonyme
$cart = $client->createCart(sessionId: 'session-unique-id');
```

### Récupérer un panier

```php
$cart = $client->getCart(cartId: 123);
```

### Ajouter un produit au panier

```php
$cartItem = $client->addItem(
    cartId: 123,
    variantId: 456,
    quantity: 2,
    attributes: ['color' => 'blue', 'size' => 'M']
);
```

### Mettre à jour la quantité d'un produit

```php
$cartItem = $client->updateItemQuantity(
    cartId: 123,
    itemId: 456,
    quantity: 3
);
```

### Supprimer un produit du panier

```php
$client->removeItem(cartId: 123, itemId: 456);
```

### Vider le panier

```php
$client->clearCart(cartId: 123);
```

### Récupérer les produits d'un panier

```php
$items = $client->getItems(cartId: 123);
```

## Gestion des erreurs

Le SDK peut lever plusieurs types d'exceptions :

### CartException

Exception générique pour les erreurs de l'API :

```php
use Lumexa\CartSdk\Exceptions\CartException;

try {
    $cart = $client->getCart(123);
} catch (CartException $e) {
    // Gérer l'erreur
    echo $e->getMessage();
}
```

### ValidationException

Exception pour les erreurs de validation génériques :

```php
use Lumexa\CartSdk\Exceptions\ValidationException;

try {
    $cart = $client->createCart();
} catch (ValidationException $e) {
    // Accéder à toutes les erreurs
    $errors = $e->errors();

    // Accéder à la première erreur
    $firstError = $e->firstError();
}
```

### CartValidationException

Exception pour les erreurs de validation spécifiques au panier :

```php
use Lumexa\CartSdk\Exceptions\CartValidationException;

try {
    $cart = $client->createCart();
} catch (CartValidationException $e) {
    // Erreurs possibles :
    // - invalidCartId()
    // - invalidUserId()
    // - invalidSessionId()
    // - missingIdentifier()
}
```

### CartItemValidationException

Exception pour les erreurs de validation spécifiques aux articles du panier :

```php
use Lumexa\CartSdk\Exceptions\CartItemValidationException;

try {
    $item = $client->addItem(cartId: 123, variantId: 456, quantity: 2);
} catch (CartItemValidationException $e) {
    // Erreurs possibles :
    // - invalidItemId()
    // - invalidVariantId()
    // - invalidQuantity()
    // - invalidAttributes()
    // - outOfStock()
    // - maxQuantityExceeded()
}
```

## Types de données

### CartDTO

```php
class CartDTO {
    public readonly int $id;
    public readonly int $storeId;
    public readonly ?string $sessionId;
    public readonly ?int $userId;
    public readonly string $status;
    public readonly float $total;
    public readonly array $items; // CartItemDTO[]
    public readonly string $createdAt;
    public readonly string $updatedAt;
}
```

### CartItemDTO

```php
class CartItemDTO {
    public readonly int $id;
    public readonly int $cartId;
    public readonly int $quantity;
    public readonly float $unitPrice;
    public readonly float $totalPrice;
    public readonly array $attributes;
    public readonly ?array $variant;
    public readonly string $createdAt;
    public readonly string $updatedAt;
}
```
