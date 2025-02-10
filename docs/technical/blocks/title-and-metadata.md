---
title: Title and Metadata
description: Technical documentation for handling page titles and metadata in Prismic content
---

# Title and Metadata

The module provides functionality for managing page titles, meta tags, and other metadata from Prismic content.

## Components

### Canonical Block
```php
Elgentos\PrismicIO\Block\Canonical
```
Generates canonical URLs for Prismic content.

### AlternateLanguage Block
```php
Elgentos\PrismicIO\Block\AlternateLanguage
```
Handles hreflang tags for multilingual content.

## Implementation

### Layout Integration

```xml
<referenceBlock name="head.additional">
    <block name="prismicio_alternate_links" 
           class="Elgentos\PrismicIO\Block\AlternateLanguage" 
           template="alternate_links.phtml" />
    <block name="prismicio_canonical" 
           class="Elgentos\PrismicIO\Block\Canonical" 
           template="canonical.phtml" />
</referenceBlock>
```

### Metadata Handling

The module can handle various metadata:
- Page titles
- Meta descriptions
- Open Graph tags
- Twitter cards
- Canonical URLs
- Alternate language links

## Usage Examples

### Page Title

```xml
<referenceBlock name="page.main.title">
    <block class="Elgentos\PrismicIO\Block\Dom\Plain" 
           template="data.meta_title"/>
</referenceBlock>
```

### Meta Description

```xml
<referenceBlock name="head.additional">
    <block class="Elgentos\PrismicIO\Block\Metadata" 
           name="prismic.metadata">
        <arguments>
            <argument name="reference" xsi:type="string">data.meta_description</argument>
        </arguments>
    </block>
</referenceBlock>
```

## Best Practices

1. **SEO Optimization**
   - Always provide meta descriptions
   - Use proper canonical URLs
   - Implement hreflang correctly

2. **Multi-store Support**
   - Handle store-specific metadata
   - Consider language variations
   - Maintain consistent URLs

3. **Performance**
   - Cache metadata appropriately
   - Optimize metadata generation
   - Monitor page load impact 