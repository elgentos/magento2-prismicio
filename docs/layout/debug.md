---
title: Debugging Prismic Content
description: How to debug Prismic content in your Magento 2 store
---

# Debugging

The Prismic module includes debugging capabilities to help you develop and troubleshoot your Prismic integration.

## Debug Block

The module provides a debug block that can be useful during development. You can add this block to your layout to see the raw Prismic document data:

```xml
<block name="prismicio_debug" class="Elgentos\PrismicIO\Block\Debug"/>
```

This will display all available Prismic data for the current document, which can be helpful when:

- Developing new templates
- Troubleshooting content issues
- Understanding the document structure
- Verifying field values

## Enabling Debug Mode

To enable debug mode:

1. Go to the Magento admin panel
2. Navigate to Stores > Configuration > Elgentos > Prismic.IO
3. Under the Content section, enable "Debugging"
4. Clear the cache

When debug mode is enabled, you'll see additional information in your browser's developer tools and on the page if the debug block is present. 

## Debug Mode Features

When debug is enabled:
- Detailed error messages
- API request logging
- Template path information
- Context debugging tools

## Debug Output

Add the debug parameter to see detailed information:
```
?prismicio_debug=1
```

## Exception Handling

With exceptions enabled:
- Detailed stack traces
- API error information
- Context resolution errors
- Template processing issues