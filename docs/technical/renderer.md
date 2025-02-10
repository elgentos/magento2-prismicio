---
title: Renderer
description: Technical documentation for Prismic content rendering in Magento 2
---

# Renderer

The module provides rendering functionality for Prismic content through various components and blocks.

## Components

### Page Renderer
Handles the main page rendering for Prismic content.

### Block Renderer
Manages individual block rendering within Prismic content.

### Template Integration
Integrates with Magento's template system.

## Implementation

### Page Rendering

```php
public function renderPageByUid(string $uid, string $contentType = null)
{
    // Fetch and render document
}
```

### Block Rendering

```php
public function fetchDocumentView(): string
{
    // Render document blocks
}
```

## Layout Integration

### Default Layout

```xml
<page>
    <body>
        <referenceContainer name="content">
            <container name="prismicio_content" />
        </referenceContainer>
    </body>
</page>
```

### Content Types

```xml
<handle id="prismicio_by_type_blog_post">
    <reference name="content">
        <block class="Elgentos\PrismicIO\Block\Template" 
               template="blog/post.phtml"/>
    </reference>
</handle>
```

## Best Practices

1. **Performance**
   - Implement caching
   - Optimize rendering
   - Handle large documents

2. **Template Structure**
   - Maintain hierarchy
   - Use proper blocks
   - Document templates

3. **Error Handling**
   - Graceful fallbacks
   - Clear error messages
   - Debug information 