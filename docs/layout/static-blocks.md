---
title: Static Blocks
description: Working with Prismic static blocks in Magento 2
---

# Static Blocks

Static blocks in Prismic provide a way to manage reusable content blocks that can be placed throughout your Magento store. They function similarly to Magento's CMS blocks but are managed through Prismic.

## Understanding Static Blocks

A static block in Prismic:
- Has a unique identifier (UID)
- Belongs to a specific content type (default: 'static_block')
- Can be referenced and rendered anywhere in your layouts
- Supports multiple languages

## Usage Examples

### Basic Static Block

```xml
<!-- Basic static block with default content type -->
<block class="Elgentos\PrismicIO\Block\StaticBlock" name="my.static.block">
    <arguments>
        <argument name="identifier" xsi:type="string">my-block-uid</argument>
    </arguments>
</block>
```

### Custom Content Type Block

```xml
<!-- Static block with custom content type -->
<block class="Elgentos\PrismicIO\Block\StaticBlock" name="custom.block">
    <arguments>
        <argument name="content_type" xsi:type="string">custom_block_type</argument>
        <argument name="identifier" xsi:type="string">custom-block-uid</argument>
    </arguments>
</block>
```

### Using Reference Notation

```xml
<!-- Using dot notation to specify content type and identifier -->
<block class="Elgentos\PrismicIO\Block\StaticBlock" name="footer.block">
    <arguments>
        <argument name="reference" xsi:type="string">footer_block.main</argument>
    </arguments>
</block>
```

## Reference Format

You can reference static blocks in two ways:

1. Using separate arguments:
```xml
<arguments>
    <argument name="content_type" xsi:type="string">static_block</argument>
    <argument name="identifier" xsi:type="string">my-block</argument>
</arguments>
```

2. Using dot notation in the reference:
```xml
<arguments>
    <argument name="reference" xsi:type="string">static_block.my-block</argument>
</arguments>
```

The format is: `content_type.identifier`

## Technical Details

The static block system:
1. Creates a Prismic document based on the provided identifier and content type
2. Fetches the document content from Prismic
3. Renders all child blocks within the context of the fetched document

## Error Handling

The block will:
- Return an empty string if no identifier is provided
- Throw a `StaticBlockNotFoundException` if the referenced document cannot be found
- Include helpful debug information when exceptions occur, such as:
  - UID
  - Content type
  - Language

## Best Practices

1. Use meaningful identifiers that reflect the block's purpose
2. Keep content types organized and consistent
3. Consider using the dot notation reference for cleaner XML
4. Handle potential missing blocks gracefully in your templates
5. Use appropriate language settings for multi-language stores
6. Cache static block output when possible for better performance 