---
title: Integration Fields
description: Using Prismic Integration Fields with Magento 2 products and categories
---

# Integration Fields

Integration Fields allow you to use Magento product and category data directly in your Prismic documents. This creates a dynamic connection between your Magento catalog and Prismic content.

## Configuration

### Basic Setup

1. Go to Stores > Configuration > Elgentos > Prismic.IO
2. Navigate to Integration Fields section
3. Configure:
   - Product Attributes to sync
   - Product Visibility settings
   - Access Token for security
   - Disabled Products sync option

### Default Product Attributes

The following attributes are always available:
- name
- image
- status
- short_description
- updated_at

## Setting Up in Prismic

### Creating Integration Fields

1. Go to your Prismic dashboard
2. Navigate to Settings > Integration fields
3. Create a new Custom Integration
4. Configure the endpoints:
   - Products: `https://your-store.com/prismicio/integration/products`
   - Categories: `https://your-store.com/prismicio/integration/categories`
5. Add the Access Token if configured in Magento

### Adding to Content Types

```json
{
  "Main": {
    "featured_product": {
      "type": "IntegrationFields",
      "config": {
        "catalog": "product_catalog",
        "label": "Featured Product"
      }
    }
  }
}
```

## Available Endpoints

### Products Endpoint

```
GET /prismicio/integration/products
```

Returns product data in Prismic-compatible format:

```json
{
  "results_size": 100,
  "results": [
    {
      "id": "123",
      "title": "Product Name",
      "description": "Short description",
      "image_url": "https://example.com/media/catalog/product/image.jpg",
      "last_update": 1673891234,
      "blob": {
        // All selected product attributes
      }
    }
  ]
}
```

### Categories Endpoint

```
GET /prismicio/integration/categories
```

Returns category data:

```json
{
  "results_size": 50,
  "results": [
    {
      "id": "4",
      "title": "Category Name",
      "description": "Category description",
      "image_url": "https://example.com/media/catalog/category/image.jpg",
      "last_update": 1673891234,
      "blob": {
        // All category attributes
      }
    }
  ]
}
```

## Security

### Access Token Protection

1. Configure an access token in Magento:
   ```xml
   <prismicio>
       <integration_fields>
           <access_token>your-secret-token</access_token>
       </integration_fields>
   </prismicio>
   ```

2. Use the same token in Prismic's Integration Fields setup

### Authentication Headers

The endpoints use Basic Authentication:
- Username: Your access token
- Password: Can be left empty

## Configuration Options

### Product Sync Options

1. **Visibility Settings**
   ```xml
   <visibility>4</visibility>  <!-- Catalog, Search -->
   ```

2. **Disabled Products**
   ```xml
   <sync_disabled_products>0</sync_disabled_products>
   ```

3. **Custom Attributes**
   ```xml
   <attributes>sku,price,special_price</attributes>
   ```

## Implementation Examples

### Basic Product Integration

```json
{
  "Main": {
    "related_products": {
      "type": "Group",
      "config": {
        "fields": {
          "product": {
            "type": "IntegrationFields",
            "config": {
              "catalog": "product_catalog"
            }
          }
        }
      }
    }
  }
}
```

### Category Selection

```json
{
  "Main": {
    "featured_category": {
      "type": "IntegrationFields",
      "config": {
        "catalog": "category_catalog"
      }
    }
  }
}
```

## Best Practices

1. **Performance**
   - Limit the number of attributes synced
   - Use pagination for large catalogs
   - Consider caching strategies

2. **Security**
   - Always use access tokens in production
   - Limit exposed attributes to necessary data
   - Regularly rotate access tokens

3. **Data Management**
   - Sync only required product visibility types
   - Consider impact of disabled products
   - Keep attribute selection minimal

4. **Maintenance**
   - Monitor API response times
   - Review and update synced attributes
   - Check for outdated product references

## Troubleshooting

1. **Authentication Issues**
   - Verify access token configuration
   - Check authentication headers
   - Confirm Prismic integration setup

2. **Missing Data**
   - Check product visibility settings
   - Verify attribute configuration
   - Confirm product/category status

3. **Performance Problems**
   - Review number of synced attributes
   - Check pagination settings
   - Monitor API response times 