---
title: Alternate Languages
description: Managing multi-language content with Prismic in Magento 2
---

# Alternate Languages

The Prismic module provides support for multi-language content through the alternate language system, which integrates with Magento's multi-store functionality to serve content in different languages.

## Understanding Alternate Languages

The alternate language system:
- Generates proper `hreflang` tags for SEO
- Works with Magento's multi-store setup
- Supports language fallbacks
- Handles x-default language specification
- Works across multiple Prismic repositories

## Usage Example

Add the alternate language block to your layout:

```xml
<block class="Elgentos\PrismicIO\Block\AlternateLanguage" name="alternate.language" template="Elgentos_PrismicIO::alternate-language.phtml"/>
```

Example template file (`alternate-language.phtml`):

```php
<?php
/** @var $block \Elgentos\PrismicIO\Block\AlternateLanguage */
foreach ($block->getAlternateData() as $alternate): ?>
    <link rel="alternate" 
          hreflang="<?= $alternate['hreflang'] ?>"
          href="<?= $alternate['href'] ?>"
          type="<?= $alternate['type'] ?>"/>
<?php endforeach; ?>
```

## Configuration

The system uses several configuration points:

1. Store Configuration:
   - Enable/disable Prismic for each store view
   - Set content language per store view
   - Configure language fallbacks

2. Magento Locale Settings:
   - Uses store locale settings for proper `hreflang` tags
   - Automatically maps store locales to Prismic languages

## Language Mapping

The system handles language mapping in several ways:

1. Direct mapping:
   ```
   Magento Locale -> Prismic Language
   en_US -> en-us
   nl_NL -> nl-nl
   ```

2. Fallback mapping:
   - If a language isn't available, falls back to configured fallback language
   - Can be configured per store view

3. Dynamic mapping:
   - Use '*' as language code to use store's locale
   - Automatically converts Magento locale format (en_US) to Prismic format (en-us)

## Technical Details

The alternate language system:

1. Checks for multi-store mode
   - Returns empty if in single store mode
   - Processes alternates only for active stores

2. Generates alternate data including:
   - Language code
   - Store code
   - hreflang attribute
   - URL for the alternate version
   - Content type
   - Link object

3. Handles special cases:
   - Generates x-default for default store view
   - Respects store activity status
   - Considers Prismic enablement per store

## Example Output

The system generates HTML like this:

```html
<link rel="alternate" hreflang="en-us" href="https://example.com/page" type="text/html"/>
<link rel="alternate" hreflang="nl-nl" href="https://example.com/nl/pagina" type="text/html"/>
<link rel="alternate" hreflang="x-default" href="https://example.com/page" type="text/html"/>
```

## Best Practices

1. Always include the alternate language block in your head section
2. Configure language fallbacks appropriately
3. Ensure consistent content across languages
4. Use proper locale codes in Magento store configuration
5. Test alternate links in multi-store setups
6. Consider SEO implications when setting up language variants
7. Maintain consistent URL structures across languages 