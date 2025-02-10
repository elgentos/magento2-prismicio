---
title: Container Block
description: Technical documentation for Prismic container blocks in Magento 2
---

# Container Block

The Container block provides a way to group and organize Prismic content blocks.

## Implementation

### Container Block Class
```php
Elgentos\PrismicIO\Block\Container
```

Extends the abstract block to provide container functionality.

### Key Features

- Groups related content blocks
- Manages shared context
- Handles nested structures
- Provides scope isolation

## Usage

### Basic Container

```xml
<block class="Elgentos\PrismicIO\Block\Container" name="prismic.content">
    <arguments>
        <argument name="reference" xsi:type="string">data</argument>
    </arguments>
    <!-- Child blocks -->
</block>
```

### Nested Containers

```xml
<block class="Elgentos\PrismicIO\Block\Container" name="outer.container">
    <block class="Elgentos\PrismicIO\Block\Container" name="inner.container">
        <!-- Nested content -->
    </block>
</block>
```

## Context Management

### Reference Handling

Containers can specify their reference scope:

```xml
<arguments>
    <argument name="reference" xsi:type="string">data.section</argument>
</arguments>
```

### Context Inheritance

Child blocks inherit their parent container's context unless explicitly overridden.

## Best Practices

1. **Organization**
   - Group related content
   - Use meaningful names
   - Maintain clear hierarchy

2. **Performance**
   - Avoid deep nesting
   - Consider caching strategies
   - Monitor render times

3. **Maintenance**
   - Document container structure
   - Keep layouts organized
   - Use consistent patterns 