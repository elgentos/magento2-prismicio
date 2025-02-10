---
title: Overview Block
description: Technical documentation for the Prismic Overview block in Magento 2
---

# Overview Block

The Overview block (`Elgentos\PrismicIO\Block\Overview`) provides functionality for displaying lists or collections of Prismic documents.

## Features

- Fetches multiple documents of the same type
- Supports filtering and sorting
- Handles language fallbacks
- Manages document merging

## Implementation

### Core Methods

```php
public function getDocuments(): array
public function getDocumentsWithoutLanguage(): array
public function buildFilters(): array
public function buildQuery(): array
public function mergeDocuments(\stdClass ...$allDocuments): array
```

### Configuration

```xml
<block class="Elgentos\PrismicIO\Block\Overview" name="blog.list">
    <arguments>
        <argument name="document_type" xsi:type="string">blog_post</argument>
    </arguments>
</block>
```

## Usage Examples

### Basic Document List

```php
/** @var $block \Elgentos\PrismicIO\Block\Overview */
foreach ($block->getDocuments() as $document) {
    // Process each document
}
```

### Filtered List

```php
$block->setAllowedFilters([
    'category' => 'my.blog_post.category'
]);
```

### Custom Options

```php
$block->setOptions([
    'pageSize' => 10,
    'orderings' => '[document.first_publication_date desc]'
]);
```

## Best Practices

1. **Performance**
   - Use appropriate page sizes
   - Implement caching where possible
   - Consider lazy loading

2. **Language Handling**
   - Use proper language fallbacks
   - Handle missing translations
   - Consider multi-store setups

3. **Query Building**
   - Keep filters simple
   - Use meaningful predicates
   - Document query structure 