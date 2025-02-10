---
title: Routing
description: Understanding how URLs and routing work with Prismic content in Magento 2
---

# Routing

The Prismic module provides a flexible routing system that allows you to map Prismic documents to URLs in your Magento store. This system integrates with Magento's URL rewrite functionality to maintain SEO-friendly URLs and proper redirects.

## URL Rewrite Configuration

### Basic Setup

1. Go to Stores > Configuration > Elgentos > Prismic.IO
2. Navigate to the URL Rewrites section
3. Select which content types should generate automatic URL rewrites
4. Configure webhook settings to enable automatic URL generation

### Database Structure

The module uses two main tables for routing:
- `prismicio_route` - Stores the main route information
- `prismicio_route_store` - Maps routes to specific store views

## Automatic URL Generation

The module can automatically generate URLs based on your Prismic documents:

1. **Content Type Selection**
   ```xml
   <prismicio>
       <url_rewrites>
           <content_types>blog_post,landing_page</content_types>
       </url_rewrites>
   </prismicio>
   ```

2. **Webhook Integration**
   - Set up a webhook in Prismic pointing to your Magento instance
   - Configure the webhook secret in Magento
   - The module will automatically generate URLs when content is published

## Manual Route Management

You can manually manage routes through the Magento admin:

```xml
<route id="custom_route" frontName="blog">
    <module name="Elgentos_PrismicIO" />
</route>
```

## URL Structure

### Default Pattern
```
/{content-type}/{uid}
example: /blog/summer-fashion-trends
```

### Custom URL Fields

You can specify custom URL fields in your Prismic content types:
```json
{
  "Main": {
    "url_key": {
      "type": "Text",
      "config": {
        "label": "URL Key",
        "placeholder": "custom-url-key"
      }
    }
  }
}
```

## Multi-store Support

The routing system supports Magento's multi-store setup:

1. **Store-Specific Routes**
   - Routes can be assigned to specific store views
   - Different URLs can be used for the same content in different stores

2. **Language-Based Routing**
   ```php
   // Example store-specific URL pattern
   /en/blog/post-title    // English store
   /nl/blog/post-titel    // Dutch store
   ```

## Layout Handles

The module generates specific layout handles for Prismic routes:

```xml
<!-- For a blog post content type -->
<handle id="prismicio_by_type_blog_post">
    <reference name="content">
        <block class="Elgentos\PrismicIO\Block\Template" name="blog.content" template="blog/post.phtml"/>
    </reference>
</handle>
```

## Cache Management

The routing system integrates with Magento's cache:

1. **Cache Tags**
   - Routes are tagged appropriately for cache invalidation
   - Content updates trigger relevant cache flushes

2. **Configuration**
   ```xml
   <prismicio>
       <cache_flush>
           <content_types>blog_post,landing_page</content_types>
       </cache_flush>
   </prismicio>
   ```

## Troubleshooting

Common routing issues and solutions:

1. **URLs Not Generating**
   - Verify webhook configuration
   - Check content type is selected in URL rewrites configuration
   - Ensure proper permissions are set

2. **404 Errors**
   - Verify route exists in `prismicio_route` table
   - Check store assignment in `prismicio_route_store`
   - Confirm content type is properly configured

3. **Multiple Redirects**
   - Check for conflicting URL rewrites
   - Verify store view configuration
   - Review custom URL keys

## Best Practices

1. **URL Structure**
   - Use consistent patterns across content types
   - Keep URLs clean and meaningful
   - Consider SEO implications

2. **Performance**
   - Enable caching for routes
   - Use webhooks for automatic updates
   - Monitor URL rewrite table size

3. **Maintenance**
   - Regularly review and clean up unused routes
   - Maintain proper redirects for changed URLs
   - Document custom routing patterns 