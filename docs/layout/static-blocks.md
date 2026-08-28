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

## Loading by Tag

`Elgentos\PrismicIO\Block\StaticBlockByTag` resolves the first document of a content type
carrying a given Prismic tag, instead of an explicit uid - useful for binding a block to
"whichever document is currently tagged for this spot" rather than a fixed document. It's a
drop-in `StaticBlock` swap; the `identifier` argument (or reference dot-notation) is the tag to
search for instead of a uid:

```xml
<block class="Elgentos\PrismicIO\Block\StaticBlockByTag" name="category.promo">
    <arguments>
        <argument name="content_type" xsi:type="string">promo_banner</argument>
        <argument name="identifier" xsi:type="string">category-25</argument>
    </arguments>
</block>
```

If [block caching](../advanced/block-cache.md) is enabled, this is invalidated correctly even
when a *different* document gets tagged to take the current one's place - the webhook re-tags
every changed document by its own current tags on each publish, not just by its own identity.

Before querying, the tag is checked against the repository's full tag list (cached, refreshed
every 48 hours) - so a category or product with nothing tagged for it doesn't cost a Prismic
query on every page view. If that check itself fails, it fails open (assumes the tag might exist)
rather than risk hiding real content over a transient error.

Enable the **Tags** triggers ("A tag is created" / "A tag is deleted") on the webhook alongside
the document ones, so the cached tag list refreshes as soon as a tag is added or removed rather
than waiting out its TTL.

### Category and Product

`CategoryStaticBlockByTag` and `ProductStaticBlockByTag` extend `StaticBlockByTag` to derive the
tag from the page being viewed, instead of a fixed `identifier`:

```xml
<!-- catalog_category_view.xml -->
<block class="Elgentos\PrismicIO\Block\CategoryStaticBlockByTag" name="category.promo">
    <arguments>
        <argument name="content_type" xsi:type="string">promo_banner</argument>
    </arguments>
</block>

<!-- catalog_product_view.xml -->
<block class="Elgentos\PrismicIO\Block\ProductStaticBlockByTag" name="product.promo">
    <arguments>
        <argument name="content_type" xsi:type="string">promo_banner</argument>
    </arguments>
</block>
```

`CategoryStaticBlockByTag` searches for a document tagged `category_{id}` for the category being
viewed. `ProductStaticBlockByTag` searches for `product_sku_{sku}` or `product_id_{id}` for the
product being viewed - either one matches, so you can tag content by whichever is more
convenient. Neither needs an `identifier` argument at all; both fall back to it if given, same as
the base `StaticBlockByTag`.

A document isn't limited to one category/product tag either - tag it with several
(`category_4`, `category_9`, `category_12`) to show the same content on exactly those
categories, not every category, without duplicating the document. Which specific pages a piece
of content appears on is managed entirely from Prismic's tag field.

Both `resolveTag()` and `buildTag()` are public, so a different tagging convention doesn't need a
subclass at all - a plugin on `buildTag(Category $category)` / `buildTag(Product $product)` gets
the category/product itself as an argument, so it's a natural place to add more tags (a promo-type
attribute, for example) alongside the defaults, or replace them entirely.

## Best Practices

1. Use meaningful identifiers that reflect the block's purpose
2. Keep content types organized and consistent
3. Consider using the dot notation reference for cleaner XML
4. Handle potential missing blocks gracefully in your templates
5. Use appropriate language settings for multi-language stores
6. Cache static block output when possible for better performance 