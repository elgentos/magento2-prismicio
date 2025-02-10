---
title: Sitemap Integration
description: How to include Prismic content in your Magento sitemap
---

# Sitemap Integration

The Prismic module integrates with Magento's sitemap functionality to automatically include your Prismic content in your store's XML sitemap.

## Configuration

### Basic Setup

1. Go to Stores > Configuration > Elgentos > Prismic.IO
2. Navigate to the Sitemap section
3. Select which content types to include in the sitemap

### Sitemap Settings

Configure sitemap-specific settings under Stores > Configuration > Catalog > XML Sitemap:

1. **Frequency**
   - How often the content is updated
   - Default: 'daily'
   - Options: always, hourly, daily, weekly, monthly, yearly, never

2. **Priority**
   - Priority relative to other URLs
   - Default: '0.25'
   - Valid values: 0.0 to 1.0

```xml
<sitemap>
    <prismic>
        <priority>0.25</priority>
        <changefreq>daily</changefreq>
    </prismic>
</sitemap>
```

## Content Type Selection

### Including Content Types

Specify which content types should appear in the sitemap:

```xml
<prismicio>
    <sitemap>
        <content_types>blog_post,landing_page,product_story</content_types>
    </sitemap>
</prismicio>
```

### Multi-store Support

The module handles multi-store setups:
- Generates store-specific URLs
- Respects store-specific language settings
- Handles store-specific content types

## Technical Details

### URL Generation

The module:
1. Queries each configured content type
2. Generates proper URLs using the LinkResolver
3. Includes last publication date from Prismic
4. Applies store-specific URL formatting

### Example Output

```xml
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>https://example.com/blog/summer-trends</loc>
        <lastmod>2024-01-15T10:30:00+00:00</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.25</priority>
    </url>
    <!-- Additional URLs... -->
</urlset>
```

## Implementation

### Automatic Integration

The module automatically integrates with Magento's sitemap generation:

```php
// PrismicPages implements ItemProviderInterface
class PrismicPages implements ItemProviderInterface
{
    public function getItems($storeId): array
    {
        // Fetches all configured content types
        // Generates sitemap items
        // Returns formatted array for sitemap
    }
}
```

### Custom Implementation

You can extend the default behavior:

```php
class CustomPrismicPages extends PrismicPages
{
    protected function getSitemapContentTypes($store): array
    {
        // Your custom logic for content types
        return array_filter($customContentTypes);
    }
}
```

## Best Practices

1. **Content Type Selection**
   - Include only publicly accessible content
   - Consider content update frequency
   - Exclude temporary or test content

2. **Priority Setting**
   - Set higher priority for main content (0.8-1.0)
   - Use medium priority for regular content (0.4-0.7)
   - Lower priority for auxiliary content (0.1-0.3)

3. **Update Frequency**
   - Match changefreq to actual content updates
   - Use appropriate frequency for content type
   - Consider content lifecycle

4. **Performance**
   - Monitor sitemap size
   - Use pagination for large content sets
   - Consider caching strategies

## Troubleshooting

1. **Missing Content**
   - Verify content type is enabled in configuration
   - Check content is published in Prismic
   - Confirm proper store assignment

2. **Invalid URLs**
   - Check LinkResolver configuration
   - Verify store URL settings
   - Review content type routing

3. **Generation Issues**
   - Check Magento cron is running
   - Verify file permissions
   - Monitor memory usage for large sitemaps

## Maintenance

1. **Regular Tasks**
   - Review included content types
   - Update priorities based on analytics
   - Monitor sitemap size and generation time

2. **Optimization**
   - Remove unnecessary content types
   - Adjust update frequencies
   - Clean up old/unused URLs 