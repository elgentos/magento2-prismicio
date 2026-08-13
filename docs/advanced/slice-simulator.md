---
title: Slice Simulator
description: Live-previewing slices from Prismic's own editor using this module's Slice Simulator support
---

# Slice Simulator

[Prismic's Slice Simulator](https://prismic.io/docs/slice-simulator) lets editors preview a slice
live, inside Prismic's own dashboard, while they're still building or editing it - before it's
ever attached to a real document. This module renders that preview using your site's actual slice
templates, so what an editor sees while building content is what will actually render on the
storefront.

## How it works

Two routes make this up:

- `prismicio/slice/slicesimulator` - the page Prismic's dashboard iframes. It loads
  [`@prismicio/simulator`](https://www.npmjs.com/package/@prismicio/simulator) client-side, which
  handshakes with Prismic and receives the slice currently being edited.
- `prismicio/slice/render` - called by that page for every slice update. It loads the
  `prismicio_slices_{slice_type}` layout handle (the same convention used throughout this
  module's own slice layouts, alongside `prismicio_by_type_*` - see [Layout Handles](../layout/handles.md)),
  finds that slice's root block, calls `setDocument()` on it, and returns the rendered HTML.

A dedicated router also serves both of these at `/slice-simulator` (and, if a secret is
configured, `/secret/{secret}/slice-simulator`) - Prismic's own tooling expects the Simulator URL
to end in `/slice-simulator`, regardless of what your site's actual route looks like.

## Enabling it

1. Go to **Stores > Configuration > Elgentos > Prismic.IO > Slice Simulator**
2. Set **Enabled** to Yes
3. Optionally set a **Secret** - if set, requests must include a matching `secret` (as a query
   parameter, or as the path segment above)

## Configuring Prismic

1. Open any document for editing
2. Click the settings icon (stacked dots, top right)
3. Go to **Live preview settings**
4. Set the **Slice Simulator URL** to:

   ```
   https://your-store.com/slice-simulator
   ```

   or, with a secret configured:

   ```
   https://your-store.com/secret/{your-secret}/slice-simulator
   ```

## Matching slices to blocks

Rendering a slice update takes two lookups, each with its own admin setting:

- **Layout Handle Prefix** - which layout handle to load for the slice, e.g. `hero_banner` loads
  `prismicio_slices_hero_banner` by default. This defaults to `prismicio_slices_`, matching this
  module's own slice layout naming convention (see [Layout Handles](../layout/handles.md)); change
  it only if your project's slice layouts use a different prefix.
- **Block Name Prefix** - once that handle is loaded, which block within it is the slice's root.
  By default the simulator tries the slice type name itself (e.g. `hero_banner`), matching
  `Elgentos\PrismicIO\Block\Slices::getSliceTypeBlock`'s own convention. If your layouts namespace
  slice root blocks instead (e.g. `slice.hero_banner`), set this prefix rather than restructuring
  your layouts. Leave it empty to only try the unprefixed name.

Both lookups are also public, pluggable methods (`Render::getLayoutHandle()` and
`Render::getBlockNames()`) - if your project's convention isn't a fixed prefix, plugin one of
these instead of reconfiguring the field.

## Troubleshooting

**"Refused to display '...' in a frame because it set 'X-Frame-Options' to 'sameorigin'"**

This module already clears `X-Frame-Options` for the simulator route specifically, so this
shouldn't happen there. If you see it, double check the URL configured in Prismic actually points
at `/slice-simulator` (or the secret-path variant) rather than some other page - Magento sends
`X-Frame-Options: SAMEORIGIN` on every other route by design, and a `Content-Security-Policy`
running in **report-only** mode does not override it (only an enforced CSP's `frame-ancestors`
directive would, and this module doesn't rely on that alone).

**"Block not found for slice type: ..."**

The JSON response includes `tried_handles` and `tried_blocks` - use these to confirm the layout
handle and block name actually match what your `prismicio_slices_*.xml` layout defines. A common
cause is a **Block Name Prefix** or **Layout Handle Prefix** with a stray leading/trailing space.
