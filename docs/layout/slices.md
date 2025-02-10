---
title: Slices
description: Working with Prismic slices in Magento 2
---

# Slices

Slices in Prismic are reusable content blocks that can be dynamically arranged in your documents. The module provides specialized handling for Prismic slices.

## Understanding Slices

Slices allow content editors to:
- Build flexible page layouts
- Reuse content components
- Maintain consistent styling
- Create dynamic content zones

## Implementation

### Slice Block

```php
Elgentos\PrismicIO\Block\Slices
```

The Slices block handles rendering of slice content from Prismic documents.

### Basic Usage

```xml
<block class="Elgentos\PrismicIO\Block\Slices" name="page.slices" template="body">
    <block class="Elgentos\PrismicIO\Block\Container" name="slice.container">
        <arguments>
            <argument name="reference" xsi:type="string">primary</argument>
        </arguments>
        <!-- Slice content blocks -->
    </block>
</block>
```

## Slice Machine Integration

The module provides Slice Machine support through console commands:

```bash
# Initialize Slice Machine
bin/magento elgentos:prismic:slice-machine:init --store-code=<store>

# Start Slice Machine development
bin/magento elgentos:prismic:slice-machine:start --store-code=<store>
```

## Configuration

### Slice Template Structure

```
templates/
├── slices/
│   ├── text_block.phtml
│   ├── image_gallery.phtml
│   └── product_showcase.phtml
└── slice-wrapper.phtml
```

### Slice Layout Example

```xml
<block class="Elgentos\PrismicIO\Block\Slices" name="content.slices" template="content">
    <!-- Text Block Slice -->
    <block class="Elgentos\PrismicIO\Block\Container" name="text.block">
        <block class="Elgentos\PrismicIO\Block\Dom\RichText" 
               name="text.content" 
               template="content"/>
    </block>
    
    <!-- Image Gallery Slice -->
    <block class="Elgentos\PrismicIO\Block\Container" name="gallery">
        <block class="Elgentos\PrismicIO\Block\Dom\Image" 
               name="gallery.images" 
               template="gallery"/>
    </block>
</block>
```

## Creating Custom Slices

1. Define slice in Slice Machine:
```json
{
  "text_block": {
    "type": "Slice",
    "fieldset": "Text Block",
    "description": "A basic text block",
    "icon": "text_fields",
    "non-repeat": {
      "content": {
        "type": "StructuredText",
        "config": {
          "multi": "paragraph,heading1,heading2",
          "label": "Content"
        }
      }
    }
  }
}
```

2. Create template file:
```php
<?php
/** @var $block \Elgentos\PrismicIO\Block\Template */
$slice = $block->getContext();
?>
<div class="text-block">
    <?= $block->getChildHtml('text.content') ?>
</div>
```

3. Add layout configuration:
```xml
<block class="Elgentos\PrismicIO\Block\Container" name="slice.text_block">
    <block class="Elgentos\PrismicIO\Block\Dom\RichText" 
           name="text.content" 
           template="slices/text_block"/>
</block>
```

## Best Practices

1. **Slice Organization**
   - Group related fields
   - Use clear naming conventions
   - Document slice purpose
   - Keep slices focused

2. **Development**
   - Use Slice Machine for development
   - Test slices across viewports
   - Maintain consistent styling
   - Consider reusability

3. **Performance**
   - Optimize slice loading
   - Cache where appropriate
   - Monitor resource usage
   - Handle large slice zones

4. **Maintenance**
   - Document slice variations
   - Keep styling consistent
   - Update slice schemas
   - Monitor slice usage

## Troubleshooting

1. **Slices Not Rendering**
   - Verify slice configuration
   - Check template paths
   - Validate slice data
   - Review block structure

2. **Styling Issues**
   - Check CSS scope
   - Verify class names
   - Review responsive behavior
   - Test cross-browser

3. **Development Issues**
   - Validate Slice Machine setup
   - Check store configuration
   - Review console errors
   - Verify slice schemas 