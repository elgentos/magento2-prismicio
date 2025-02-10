---
title: Routes Management
description: Managing Prismic routes in the Magento admin interface
---

# Routes Management

The module provides an admin interface for managing Prismic routes under Content > PrismicIO > Routes.

## Accessing Routes

1. Log into your Magento admin panel
2. Navigate to Content > PrismicIO > Routes
3. Here you can view and manage all Prismic routes

## Managing Routes

### Creating a Route

1. Click "Add New Route"
2. Fill in the required fields:
   - Title: For admin reference
   - Content Type: The Prismic content type (e.g., blog_post)
   - Route: The URL path (must start with /, e.g., /blog)
   - Store View: Select which store views this route applies to

### Editing Routes

1. Find the route in the grid
2. Click "Edit" in the Action column
3. Modify the route settings
4. Click "Save Route"

### Route Settings

- **Title**: Internal reference name
- **Content Type**: Matches your Prismic custom type
- **Route**: URL path pattern
- **Store Views**: Which stores show this content
- **Status**: Enable/disable the route

## URL Structure

Routes define how Prismic content is accessed on your store:

```
/{route}/{uid}
Example: /blog/summer-fashion-trends
```

## Best Practices

1. **URL Structure**
   - Use clear, descriptive paths
   - Keep URLs SEO-friendly
   - Consider multi-store impacts

2. **Content Types**
   - Match Prismic custom types exactly
   - One route per content type per store
   - Consider language variations

3. **Store Views**
   - Assign routes appropriately
   - Consider URL conflicts
   - Plan for translations

## Troubleshooting

1. **404 Errors**
   - Verify route is enabled
   - Check store view assignment
   - Confirm content type exists

2. **URL Conflicts**
   - Check for duplicate routes
   - Review store view assignments
   - Verify URL patterns

3. **Content Not Showing**
   - Validate content type name
   - Check store view settings
   - Review route status 