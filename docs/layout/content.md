---
title: Working with Prismic Content
description: Understanding how to work with Prismic content in Magento 2
---

# Content Blocks

The Prismic module provides several specialized block types for rendering Prismic content.

## Available Block Types

### Basic Blocks
- `Elgentos\PrismicIO\Block\Template` - Basic template block for Prismic content
- `Elgentos\PrismicIO\Block\Slices` - Handles slice content
- `Elgentos\PrismicIO\Block\Container` - Container block for grouping content

### DOM Blocks

The module provides specialized DOM blocks for rendering different types of Prismic content:

#### Text Content
- `Elgentos\PrismicIO\Block\Dom\Plain` - Renders plain content fields with HTML escaping
- `Elgentos\PrismicIO\Block\Dom\Raw` - Renders content without HTML escaping
- `Elgentos\PrismicIO\Block\Dom\Text` - Renders Rich Text as plain text
- `Elgentos\PrismicIO\Block\Dom\RichText` - Renders Rich Text with HTML formatting

#### Links
- `Elgentos\PrismicIO\Block\Dom\Link` - Renders a URL from a Prismic link field
- `Elgentos\PrismicIO\Block\Dom\ClickableLink` - Renders a complete `<a>` tag with the link
- `Elgentos\PrismicIO\Block\Dom\LinkWithTrailingSlash` - Renders a URL with a trailing slash (deprecated)
- `Elgentos\PrismicIO\Block\Dom\Target` - Renders the target attribute of a link (e.g., "_blank")

#### Media
- `Elgentos\PrismicIO\Block\Dom\Image` - Renders an `<img>` tag with proper dimensions and alt text

#### Other Types
- `Elgentos\PrismicIO\Block\Dom\Boolean` - Renders boolean fields
- `Elgentos\PrismicIO\Block\Dom\Date` - Renders date fields with proper formatting

## Block Usage Examples

### Text Content

```xml
<!-- Plain text with HTML escaping -->
<block class="Elgentos\PrismicIO\Block\Dom\Plain" name="my.text" template="text_field"/>

<!-- Raw content without escaping -->
<block class="Elgentos\PrismicIO\Block\Dom\Raw" name="my.html" template="html_field"/>

<!-- Rich Text field -->
<block class="Elgentos\PrismicIO\Block\Dom\RichText" name="my.rich.text" template="rich_text_field"/>
```

### Links

```xml
<!-- Basic URL -->
<block class="Elgentos\PrismicIO\Block\Dom\Link" name="my.link" template="link_field"/>

<!-- Complete clickable link -->
<block class="Elgentos\PrismicIO\Block\Dom\ClickableLink" name="my.clickable.link" template="link_field"/>

<!-- Link target -->
<block class="Elgentos\PrismicIO\Block\Dom\Target" name="my.link.target" template="link_field"/>
```

### Images

```xml
<!-- Image with dimensions -->
<block class="Elgentos\PrismicIO\Block\Dom\Image" name="my.image" template="image_field">
    <arguments>
        <argument name="css_class" xsi:type="string">my-image-class</argument>
    </arguments>
</block>
```

### Dates

```xml
<!-- Formatted date -->
<block class="Elgentos\PrismicIO\Block\Dom\Date" name="my.date" template="date_field">
    <arguments>
        <argument name="format" xsi:type="const">IntlDateFormatter::LONG</argument>
        <argument name="showTime" xsi:type="boolean">true</argument>
    </arguments>
</block>
```

## Working with References

When working with Prismic content, you can specify references to Prismic fields using the `reference` argument:

```xml
<arguments>
    <argument name="reference" xsi:type="string">data</argument>
</arguments>
```

The reference determines which part of the Prismic document structure the block will access.

## Best Practices

1. Use the specialized block types provided by the module instead of generic Magento blocks
2. Group related content using container blocks
3. Leverage Prismic's block types appropriately (Plain, Link, etc.) for different content types
4. Keep your content structure organized and maintainable
5. Use the appropriate DOM block for each content type to ensure proper rendering and escaping
6. Consider accessibility when using image and link blocks by providing proper alt text and ARIA attributes
7. Use RichText blocks for formatted content and Plain blocks for simple text to maintain proper security 