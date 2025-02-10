---
title: Webhooks and Debug Settings
description: Configuring webhooks and debug options for the Prismic Magento 2 integration
---

# Webhooks and Debug Settings

The module provides webhook support for content updates and debug options for development.

## Webhook Configuration

### Setting Up Webhooks

1. Go to Stores > Configuration > Elgentos > Prismic.IO
2. Navigate to Webhook Settings
3. Configure the webhook secret

### In Prismic

1. Go to your Prismic repository settings
2. Navigate to Webhooks
3. Add a new webhook:
   ```
   URL: https://your-store.com/prismicio/webhook
   Secret: [Your configured secret]
   ```

### Webhook Events

The webhook handles:
- Content publication
- Content deletion
- Content updates
- URL rewrite generation
- Cache invalidation
