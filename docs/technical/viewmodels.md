---
title: View Models
description: Technical documentation for Prismic View Models in Magento 2
---

# View Models

The module provides several view models for handling Prismic content rendering and URL resolution.

## Core View Models

### DocumentResolver
```php
Elgentos\PrismicIO\ViewModel\DocumentResolver
```

Handles document context and reference resolution.

### LinkResolver
```php
Elgentos\PrismicIO\ViewModel\LinkResolver
```

Resolves Prismic document links to Magento URLs.

### HtmlSerializer
```php
Elgentos\PrismicIO\ViewModel\HtmlSerializer
```

Handles custom HTML serialization for Rich Text content.

### RouteResolver
```php
Elgentos\PrismicIO\ViewModel\RouteResolver
```

Manages route resolution for Prismic documents.

## Implementation

### DocumentResolver

```php
public function hasDocument(): bool
public function getDocument(): \stdClass
public function hasContext(string $documentReference, ?\stdClass $document = null): bool
public function getContext(string $documentReference, ?\stdClass $document = null)
```

### LinkResolver

```php
public function resolve($link): ?string
public function getStore(\stdClass $link): StoreInterface
public function resolveRouteUrl(\stdClass $link): ?string
public function resolveDirectPage(\stdClass $link): ?string
```

### HtmlSerializer

```php
public function serialize(BlockInterface $object, string $content): ?string
```

## Usage Examples

### Document Resolution

```php
/** @var $documentResolver DocumentResolver */
if ($documentResolver->hasContext('data.title')) {
    $title = $documentResolver->getContext('data.title');
}
```

### Link Resolution

```php
/** @var $linkResolver LinkResolver */
$url = $linkResolver->resolve($document);
```

### Route Resolution

```php
/** @var $routeResolver RouteResolver */
if ($routeResolver->hasRoute()) {
    $route = $routeResolver->getRoute();
}
```

## Best Practices

1. **Document Handling**
   - Check document existence
   - Handle missing contexts
   - Use proper error handling

2. **Link Resolution**
   - Consider multi-store setup
   - Handle missing routes
   - Implement caching

3. **Route Management**
   - Validate routes
   - Handle store-specific routes
   - Document routing patterns 
