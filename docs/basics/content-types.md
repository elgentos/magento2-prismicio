---
title: Content Types
description: Understanding and configuring Prismic content types with Magento 2
---

# Content Types

Content types in Prismic define the structure of your content. They are like templates that specify which fields are available for your content editors. In the Magento-Prismic integration, content types are used to organize and display different kinds of content.

## Understanding Content Types

A content type in Prismic might include:
- Basic fields (Text, Number, Boolean, etc.)
- Rich Text fields for formatted content
- Link fields for internal/external links
- Media fields for images and other assets
- Slices for flexible content blocks
- Integration fields for Magento product data

## Setting Up Content Types in Prismic

1. Go to your Prismic dashboard
2. Navigate to Custom Types
3. Click "Create new"
4. Choose between Repeatable Type or Single Type:
   - **Repeatable Type**: For content that can have multiple instances (e.g., blog posts, products)
   - **Single Type**: For unique content (e.g., homepage, about page)
5. Define your fields:
   ```json
   {
     "Main" : {
       "title" : {
         "type" : "Text",
         "config" : {
           "label" : "Title"
         }
       },
       "content" : {
         "type" : "StructuredText",
         "config" : {
           "label" : "Content"
         }
       },
       "featured_image" : {
         "type" : "Image",
         "config" : {
           "label" : "Featured Image"
         }
       }
     }
   }
   ```

## Configuring Content Types in Magento

### Default Content Type

Set your default content type in Magento admin:
1. Go to Stores > Configuration > Elgentos > Prismic.IO
2. Under Content Settings, select your "Default Content Type"
3. This will be used when no specific content type is specified

### Layout-Specific Content Types

You can specify different content types for different layouts:

```xml
<block class="Elgentos\PrismicIO\Block\Template" name="prismic.content">
    <arguments>
        <argument name="content_type" xsi:type="string">blog_post</argument>
    </arguments>
</block>
```

## Best Practices

1. **Naming Conventions**
   - Use clear, descriptive names
   - Follow a consistent pattern (e.g., `page_`, `blog_`, `product_`)
   - Avoid spaces and special characters

2. **Field Organization**
   - Group related fields together
   - Use tabs for better organization
   - Consider the editing experience

3. **Integration Fields**
   - Use Integration Fields for Magento product data
   - Configure which product attributes to expose
   - Consider performance implications

4. **Content Type Planning**
   ```
   my_page/
   ├── Main
   │   ├── title (Text)
   │   ├── description (Rich Text)
   │   └── featured_image (Image)
   ├── SEO
   │   ├── meta_title (Text)
   │   ├── meta_description (Text)
   │   └── og_image (Image)
   └── Slices
       ├── text_block
       ├── image_gallery
       └── product_showcase
   ```

## Technical Implementation

### Fetching Content by Type

```php
// In your block class
public function getContent()
{
    $contentType = $this->getContentType() ?? 'default_type';
    $api = $this->getPrismicApi();
    
    return $api->query([
        Predicates::at('document.type', $contentType)
    ]);
}
```

### Content Type-Specific Templates

```xml
<!-- Layout XML -->
<block class="Elgentos\PrismicIO\Block\Template" name="prismic.content">
    <arguments>
        <argument name="content_type" xsi:type="string">blog_post</argument>
        <argument name="template" xsi:type="string">prismic/content/blog-post.phtml</argument>
    </arguments>
</block>
```

## Common Content Types

1. **Pages**
   - Homepage
   - About page
   - Contact page
   - Landing pages

2. **Marketing Content**
   - Banners
   - Promotions
   - Campaign pages

3. **Blog/News**
   - Blog posts
   - News articles
   - Press releases

4. **Product Enhancement**
   - Extended descriptions
   - Usage guides
   - Product stories

## Troubleshooting

- **Content Not Displaying**: Verify content type name matches exactly
- **URL Rewrites Not Generating**: Check webhook configuration
- **Integration Fields Not Working**: Verify product attribute configuration
- **Preview Not Working**: Ensure preview mode is enabled for content type 