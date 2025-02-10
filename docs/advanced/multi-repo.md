---
title: Multiple Repositories
description: Working with multiple Prismic repositories in Magento 2
---

# Multiple Repositories

The module supports working with multiple Prismic repositories, allowing different stores or themes to use different content repositories.

## Configuration

### Enabling Multi-repo Support

1. Go to Stores > Configuration > Elgentos > Prismic.IO
2. Navigate to Multi Repository section
3. Set "Enable Multiple Repositories" to "Yes"
4. Configure the "Repository Field" setting

### Repository Field

The repository field determines which Prismic field contains the repository information:

```json
{
  "Main": {
    "repository": {
      "type": "Text",
      "config": {
        "label": "Repository"
      }
    }
  }
}
```

## Implementation

### Store-specific Repositories

Each store view can connect to a different Prismic repository:

1. Switch to specific store view in admin
2. Configure Prismic API endpoint for that store
3. Set appropriate API token

### Cache Considerations

When using multiple repositories:

1. **Cache Flush Settings**
   ```xml
   <prismicio>
       <cache_flush>
           <content_types>page,blog_post</content_types>
       </cache_flush>
   </prismicio>
   ```

2. **Cache Tags**
   - Each repository gets unique cache tags
   - Content types are prefixed with repository ID
   - Cache is cleared per repository

## Best Practices

1. **Repository Organization**
   - Use consistent content types across repos
   - Document repository purposes
   - Consider content sharing needs

2. **Performance**
   - Monitor API usage per repository
   - Configure appropriate cache settings
   - Consider CDN implementation

3. **Maintenance**
   - Keep repositories synchronized
   - Document repository relationships
   - Plan for content migrations

## Troubleshooting

1. **Repository Connection**
   - Verify API endpoints
   - Check access tokens
   - Confirm store assignments

2. **Cache Issues**
   - Review cache configuration
   - Check cache tags
   - Monitor cache storage

3. **Content Sync**
   - Validate webhook setup
   - Check repository field values
   - Review store-specific settings 