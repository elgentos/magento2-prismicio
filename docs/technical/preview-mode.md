---
title: Preview Mode
description: Technical documentation for Prismic preview functionality in Magento 2
---

# Preview Mode

The preview mode functionality allows content editors to preview Prismic content changes before publishing them to the live site.

## Components

### PreviewMode Block

```php
Elgentos\PrismicIO\Block\PreviewMode
```

The PreviewMode block handles the integration of Prismic's preview script and is automatically added to pages when preview mode is enabled.

### Preview Controller

```php
Elgentos\PrismicIO\Controller\Preview\Index
```

Handles preview requests and redirects to the appropriate page.

## Configuration

Preview mode can be enabled in the Magento admin:

```xml
<prismicio>
    <content>
        <allow_preview>1</allow_preview>
    </content>
</prismicio>
```

## Implementation

### Preview Script Integration

The module automatically adds the Prismic preview script to your pages when preview is enabled:

```php
const PRISMICIO_PREVIEW_URL = 'https://static.cdn.prismic.io/prismic.js?new=true&repo=';
```

### Preview Flow

1. Editor clicks preview in Prismic
2. Request is sent to `/prismicio/preview/index`
3. PreviewController validates the token
4. Controller resolves the document URL
5. User is redirected to the preview page

### Security Considerations

The preview controller:
- Only works when preview mode is enabled
- Validates preview tokens
- Falls back to 404 for invalid requests

### Template Integration

```xml
<referenceBlock name="before.body.end">
    <block ifconfig="prismicio/content/allow_preview" 
           name="prismicio_preview_html" 
           class="Elgentos\PrismicIO\Block\PreviewMode" 
           template="Elgentos_PrismicIO::preview-mode.phtml" />
</referenceBlock>
```

## API Methods

### PreviewMode Block

```php
public function getApiEndpoint(): string
public function getRepoName(): string
public function getPreviewUrl(): string
```

### Preview Controller

```php
public function execute()
```

## Best Practices

1. **Security**
   - Only enable preview in necessary environments
   - Use HTTPS for preview requests
   - Implement proper access controls

2. **Performance**
   - Preview script is only loaded when preview is enabled
   - Preview requests are not cached
   - Preview mode is disabled by default

3. **Implementation**
   - Use proper preview URLs in Prismic configuration
   - Handle preview timeouts appropriately
   - Consider preview in multi-store setups

## Troubleshooting

Common issues and solutions:

1. **Preview Not Working**
   - Verify preview mode is enabled
   - Check preview URL configuration in Prismic
   - Validate preview token handling

2. **Preview Script Issues**
   - Check CSP configuration
   - Verify repository name resolution
   - Monitor browser console for errors

3. **Preview Routing**
   - Verify LinkResolver configuration
   - Check store-specific preview handling
   - Review preview URL generation 