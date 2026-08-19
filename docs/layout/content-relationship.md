---
title: Content Relationship
description: Rendering Prismic Content Relationship fields in Magento 2
---

# Content Relationship

A Content Relationship field links one Prismic document to another. Unlike a rich text link, it
only carries the linked document's identity (`id`, `type`, `lang`) - not its content - so
rendering it means fetching the linked document and deciding how to display it based on its type.

`Elgentos\PrismicIO\Block\ContentRelationship` handles both steps: it resolves the field, fetches
the linked document, and dispatches to a named child block matching the linked document's content
type - the same pattern `Slices` uses to dispatch by slice type.

## Usage

Place the block wherever the content relationship field lives in your document, with one named
child per content type you want to handle:

```xml
<block class="Elgentos\PrismicIO\Block\ContentRelationship" name="prismicio_landingpage.related" template="primary.related_content">
    <block class="Elgentos\PrismicIO\Block\Template" name="landing_page" template="Vendor_Theme::content-relationship/landing-page.phtml"/>
    <block class="Elgentos\PrismicIO\Block\Template" name="blog_post" template="Vendor_Theme::content-relationship/blog-post.phtml"/>
</block>
```

The child block name must match the linked document's content type ID. Whichever type the field
actually resolves to picks its matching child; content types you haven't declared a child for
throw `ContentRelationshipNotFoundException` (logged, and only fatal when
`prismicio/content/throw_exceptions` is enabled - see [Debug](debug.md)), the same way an
unhandled slice type does.

A content relationship field that's empty or broken (no document selected, or the link points at
a deleted document) renders nothing rather than throwing - that's a normal content state, not a
layout misconfiguration.

## Caching

Note: if a block further up the tree already uses [`TemplateCacheable`](../advanced/block-cache.md),
don't use it on the child here too - a block's cache tags do not cover its children, so the parent
won't invalidate when the linked document changes.
