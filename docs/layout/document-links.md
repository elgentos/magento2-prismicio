---
title: Document Links
description: Understanding and working with Prismic document links in Magento 2
---

# Document Links

Document links are a special type of link in Prismic that allow you to create references between different Prismic documents. The Prismic module provides specialized blocks for handling these document links.

## Understanding Document Links

A document link in Prismic contains:
- A reference to another Prismic document
- The ability to resolve to a proper URL in your Magento store
- Optional link attributes (target, etc.)

## Available Block Types

The module provides two main block types for document links:

- `Elgentos\PrismicIO\Block\Document\Link` - Renders a URL for a document link
- `Elgentos\PrismicIO\Block\Dom\Link` - Base link block that handles all link types

## Usage Examples

### Basic Document Link

```xml
<!-- Render a document link URL -->
<block class="Elgentos\PrismicIO\Block\Document\Link" name="my.document.link" template="link_field"/>
```

### Complete Link with Attributes

```xml
<!-- Render a complete document link with attributes -->
<block class="Elgentos\PrismicIO\Block\Dom\ClickableLink" name="my.document.link" template="link_field">
    <block class="Elgentos\PrismicIO\Block\Dom\Target" name="link.target" template="link_field"/>
</block>
```

## Template Example

Here's how you might use a document link in a template file:

```php
<?php
/** @var $block \Elgentos\PrismicIO\Block\Template */
?>
<a href="<?= $block->getChildHtml('my.document.link') ?>" 
   <?= $block->getChildHtml('link.target') ?>>
    <?= __('Read More') ?>
</a>
```

## Best Practices

1. Use document links when you need to reference other Prismic documents
2. Always consider the possibility that a linked document might not exist
3. Use the appropriate block type based on your needs:
   - `Document\Link` for just the URL
   - `Dom\ClickableLink` for a complete `<a>` tag
4. Include proper target attributes when needed
5. Consider accessibility when implementing document links

## Technical Details

The document link system works by:
1. Resolving the document reference to a proper URL
2. Maintaining the original link context
3. Applying any necessary transformations (like adding trailing slashes if configured)

When using `Document\Link`, you can get the URL specifically for document views using the `getUrlForDocumentView()` method, which ensures the link is treated as a document reference. 