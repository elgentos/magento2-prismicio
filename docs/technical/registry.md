---
title: Registry
description: Technical documentation for Prismic registry classes in Magento 2
---

# Registry

The module uses registry classes to maintain state across requests and manage current document and route information.

## Core Registry Classes

### CurrentDocument
```php
Elgentos\PrismicIO\Registry\CurrentDocument
```

Maintains reference to the current Prismic document.

### CurrentRoute
```php
Elgentos\PrismicIO\Registry\CurrentRoute
```

Maintains reference to the current route.

## Implementation

### CurrentDocument

```php
public function setDocument(\stdClass $document): void
public function getDocument(): ?\stdClass
```

### CurrentRoute

```php
public function setRoute(RouteInterface $route): void
public function getRoute(): ?RouteInterface
```

## Usage Examples

### Document Registry

```php
/** @var $currentDocument CurrentDocument */
if ($document = $currentDocument->getDocument()) {
    // Process current document
}
```

### Route Registry

```php
/** @var $currentRoute CurrentRoute */
if ($route = $currentRoute->getRoute()) {
    // Process current route
}
```

## Best Practices

1. **State Management**
   - Clear registry when needed
   - Avoid circular references
   - Handle missing data

2. **Performance**
   - Minimize registry usage
   - Clear unnecessary data
   - Consider memory impact

3. **Development**
   - Document registry usage
   - Handle edge cases
   - Maintain consistency 